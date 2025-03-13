<?php

namespace App\Http\Controllers\CrewApp;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\SummaryAttendance;
use App\Models\LogAttendance;
use App\Models\ParticipantUmrohTrip;
use App\Models\AttendanceOpenRegistration;
use App\Models\AttendanceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Requests\CrewApp\Participant\StoreSummaryAttendanceRequest;
use Carbon\Carbon;

class EventAttendeeController extends Controller
{
    public function __construct()
    {
        App::setLocale('id');
    }

    public function attendee(Request $request)
    {
        $request->validate([
            'event_id' => 'required_if:act,add|required_if:act,checkinBarcode',
            'participant_id' => 'required_if:act,add',
            'barcode' => 'required_if:act,checkinBarcode|uuid',
            'participant_id' => 'required_if:act,checkin',
            'act' => 'required|in:add,remove,checkin,checkinBarcode',
        ],['barcode.required' => 'Barcode is invalid','barcode.uuid' => 'Barcode is invalid']);
        if ($request->act == 'checkin') {
            if($request->closeRegistration == "true") {
                $attendee = Attendance::where('participant_id',$request->get('participant_id'))->where('event_id', request()->event_id)->whereNull('session')->orderBy('id', 'ASC')->first();
                $this->toogleCheckInClosedReg($attendee);
            } else {
                $attendee = AttendanceOpenRegistration::find($request->get('id'));
                $this->toogleCheckInOpenedReg($attendee);
            }
            return response()->json(['success' => 'ok', 'check_in_at' => $attendee->check_in_at]);
        }

        if ($request->act == 'checkinBarcode') {
            return $this->checkInBarcode($request);
        }

        Attendance::updateOrCreate(['event_id' => $request->event_id, 'participant_id' => $request->participant_id], $request->all());
        return response()->json(['success' => 'ok']);
    }

    private function checkInBarcode(Request $request)
    {
        $participant = Participant::select(['id', 'name', 'profile_photo_path'])->where('barcode', $request->barcode)->first();
        if (! $participant) {
            throw ValidationException::withMessages(['barcode' => ['Attendee Barcode is not found']]);
        }

        $attendee = Attendance::where('event_id', $request->event_id)
                        ->where('participant_id',$participant->id)
                        ->first();
        if (! $attendee) {
            throw ValidationException::withMessages(['barcode' => ['Attendee is not in this event']]);
        }

        if ($attendee->check_in_at != null) {
            $checkInAt = Carbon::parse($attendee->check_in_at)->format('M j, Y g:i:s A');
            throw ValidationException::withMessages(['barcode' => ["{$participant->name} checked in already {$checkInAt}"]]);
        }

        $checkIn = now();
        $attendee->check_in_at = $checkIn;
        $attendee->save();
        return response()->json(['checkInAt' => Carbon::parse($checkIn)->format('Y-m-d H:i:s'), 'attendee' => $participant]);
    }

    private function toogleCheckInClosedReg(Attendance $attendee)
    {
        if ($attendee->check_in_at == null) {
            $attendee->check_in_at = Carbon::now();
        } else {
            $attendee->check_in_at = null;
        }
        $attendee->save();
    }
    private function toogleCheckInOpenedReg(AttendanceOpenRegistration $attendee)
    {
        if ($attendee->check_in_at == null) {
            $attendee->check_in_at = Carbon::now();
        } else {
            $attendee->check_in_at = null;
        }
        $attendee->save();
    }

    public function summaryAttendee(StoreSummaryAttendanceRequest $request)
    {
        $request->validate([
            'umroh_trip_id' => 'required',
            'category_id' => 'required',
            'attendance_name' => 'required',
            'total_attendance' => 'required'
        ]);
        $request->merge(['status' => 1]);
        $logs = json_decode($request->logs);
        
        SummaryAttendance::updateOrCreate(['umroh_trip_id' => $request->umroh_trip_id, 'category_id' => $request->category_id], $request->except(['photo', 'logs']));
        $summaryAttendanceId = SummaryAttendance::where('umroh_trip_id',$request->umroh_trip_id)->where('category_id',$request->category_id)->first();

        foreach($logs as $item) {
            LogAttendance::updateOrCreate(
                ['summary_attendance_id' => $summaryAttendanceId->id, 'umroh_trip_id' => $request->umroh_trip_id, 'category_id' => $request->category_id, 'participant_id' => $item->id],
                [
                    'summary_attendance_id' => $summaryAttendanceId->id,
                    'umroh_trip_id' => $request->umroh_trip_id,
                    'category_id' => $request->category_id,
                    'participant_id' => $item->id,
                    'attendance_status' => ($item->check_in_at != '') ? 1 : 0
                ]
            );
        }
        return response()->json(['success' => 'ok', 'message' => 'Data has been saved successfully']);
    }

    public function attendanceCategories()
    {
        return AttendanceCategory::all();
    }

    public function attendanceDraft(Request $request)
    {
        $category = AttendanceCategory::where('name', $request->category)->first();
        $attendances = SummaryAttendance::where('umroh_trip_id', request()->query('umrohTripId', 0))->where('category_id', $category->id)->where('status', 0)->get();
        foreach ($attendances as $value) {
            $value->category = $category->name;
            $value->last_update = date('d/m/Y H:i', strtotime($value->updated_at));
        }
        return $attendances;
    }
    
    public function attendeeDescriptions()
    {
        return SummaryAttendance::where('umroh_trip_id', request()->query('umrohTripId', 0))->where('category_id', request()->query('categoryId', 0))->get();
    }

    public function summaryAttendeeDetail()
    {
        $summary = SummaryAttendance::where('umroh_trip_id', request()->query('umrohTripId', 0))->where('category_id', request()->query('categoryId', 0))->where('attendance_name', request()->query('description', 0))->first();
        $summary['total_participant'] = ParticipantUmrohTrip::tableSearch()->count();

        return $summary;
    }

    public function logAttendances()
    {
        $participant = LogAttendance::query()
        ->join('participant', 'log_attendances.participant_id', '=', 'participant.id')
        ->select('participant.name','log_attendances.*')
        ->where('summary_attendance_id', request()->query('summaryAttendanceId', 0))->get();

        return $participant;
    }

    public function draftAttendee(Request $request)
    {
        $request->validate([
            'umroh_trip_id' => 'required',
            'category' => 'required',
            'attendance_name' => 'required',
            'total_attendance' => 'required'
        ]);
        $logs = json_decode($request->participants);

        $category = AttendanceCategory::where('name', $request->category)->first();
        
        SummaryAttendance::updateOrCreate(['umroh_trip_id' => $request->umroh_trip_id, 'category_id' => $category->id, 'status' => 0], $request->except(['photo', 'logs']));
        $summaryAttendanceId = SummaryAttendance::where('umroh_trip_id',$request->umroh_trip_id)->where('category_id',$category->id)->first();

        foreach($logs as $item) {
            LogAttendance::updateOrCreate(
                ['summary_attendance_id' => $summaryAttendanceId->id, 'umroh_trip_id' => $request->umroh_trip_id, 'category_id' => $category->id, 'participant_id' => $item->id],
                [
                    'summary_attendance_id' => $summaryAttendanceId->id,
                    'umroh_trip_id' => $request->umroh_trip_id,
                    'category_id' => $category->id,
                    'participant_id' => $item->id,
                    'attendance_status' => ($item->check_in_at != '') ? 1 : 0
                ]
            );
        }
        return response()->json(['success' => 'ok', 'message' => 'Data has been saved successfully']);
    }
}
