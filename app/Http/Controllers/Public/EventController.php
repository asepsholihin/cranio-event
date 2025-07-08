<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\EventAttendance;
use App\Models\Attendance;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

class EventController extends Controller
{
    public function __construct()
    {
        App::setLocale('id');
    }

    public function eventList(Request $request)
    {
        $query = EventAttendance::tableSearch();
        if($request->eventDate) {
            $query->where('event_date', '=', date('Y-m-d', strtotime($request->eventDate)));
        } else {
            $query->where('event_date', '>=', Carbon::now());
        }


        $events = $query->orderBy('event_date', 'desc')->get()->toArray();

        $eventsArr = array();
        foreach ($events as $value) {
            $value['close_registration'] = false;
            if($request->closeRegistration == "true") {
                $value['close_registration'] = true;
            }
            $value['id'] = strval($value['id']);
            $eventsArr[] = $value;
        }

        return response()->json($eventsArr);
    }

    public function eventJamaah(Request $request)
    {
        $query = Attendance::tableSearch();
        $query->orderByRaw('checkin asc');
        $jamaah = $query->get();

        return response()->json($jamaah);
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
            $attendee = Attendance::where('participant_id',$request->get('participant_id'))->where('event_id', request()->event_id)->whereNull('session')->orderBy('id', 'ASC')->first();
            $this->toogleCheckInClosedReg($attendee);
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
        $jamaah = Jamaah::select(['id', 'name', 'profile_photo_path'])->where('barcode', $request->barcode)->first();
        if (! $jamaah) {
            throw ValidationException::withMessages(['barcode' => ['Attendee Barcode is not found']]);
        }

        $attendee = Attendance::where('event_id', $request->event_id)
                        ->where('participant_id',$jamaah->id)
                        ->first();
        if (! $attendee) {
            throw ValidationException::withMessages(['barcode' => ['Attendee is not in this event']]);
        }

        if ($attendee->check_in_at != null) {
            $checkInAt = Carbon::parse($attendee->check_in_at)->format('M j, Y g:i:s A');
            throw ValidationException::withMessages(['barcode' => ["{$jamaah->name} checked in already {$checkInAt}"]]);
        }

        $checkIn = now();
        $attendee->check_in_at = $checkIn;
        $attendee->save();
        return response()->json(['checkInAt' => Carbon::parse($checkIn)->format('Y-m-d H:i:s'), 'attendee' => $jamaah]);
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
}
