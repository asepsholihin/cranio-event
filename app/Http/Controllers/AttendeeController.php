<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Participant;
use App\Models\LogQontakBroadcast;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Jobs\QontakStatusMessage;
use DB;

class AttendeeController extends Controller
{
    const SPA_PATH = '/event-attendee';
    public function __construct()
    {
        $this->middleware('permission:event-attendance-view')->only(['index','show']);
        $this->middleware('permission:event-attendance-add-or-edit')->only(['store', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = request()->query('perPage', 500);
        $attendances = Attendance::tableSearch()
        ->orderByRaw('participant_umroh_trips.no_urut asc, qontak asc, participant_umroh_trips.manasik_table asc, crew asc')
        ->paginate($perPage)
        ->withQueryString()
        ->withPath(self::SPA_PATH);

        $event = DB::table('event_attendances')->where('id', request()->eventId)->first();
        foreach ($attendances as $value) {

            $attendance = DB::table('attendances')
            ->select(['check_in_at','confirm','confirm_at','departure_from','departure_from_update'])
            ->where('event_id', $value->event_id)->where('participant_id', $value->participant_id)->orderBy('id', 'ASC')->first();
            $value->check_in_at = $attendance->check_in_at;
            $value->confirm = $attendance->confirm;
            $value->confirm_at = $attendance->confirm_at;
            $value->departure_from = $attendance->departure_from;
            $value->departure_from_update = $attendance->departure_from_update;

            $onlines = [];
            for ($i=0; $i < $event->session; $i++) { 
                $session_attendances = Attendance::select(['check_in_at_online', 'session'])
                ->where('session', 'sesi-'.($i + 1))
                ->where('event_id', $value->event_id)
                ->where('participant_id', $value->participant_id)->first();
                if($session_attendances) {
                    $onlines[] = $session_attendances;
                }
            }

            if($value->qontak_status) {
                if($value->qontak_status == "Delivered") {
                    $logQontak = LogQontakBroadcast::select('id','qontak_log','whatsapp_status','status')->where('participant_id', $value->participant_id)
                    ->where('type', 'Link Event')->where('event_id', $value->event_id)
                    ->first();
                    QontakStatusMessage::dispatch($logQontak);
                    $statusDelivery = "sending";
                    $statusDeliveryMessage = "Link kehadiran berhasil dikirim ke Qontak";
                }
                if($value->qontak_status == "Failed") {
                    $statusDelivery = "failed";
                    $statusDeliveryMessage = "Link kehadiran gagal dikirim ke Qontak";
                }
                if($value->whatsapp_status == 'failed') {
                    $statusDelivery = "failed";
                    $statusDeliveryMessage = "Link kehadiran gagal kirim ke Participant";
                } else {
                    $statusDelivery = "delivered";
                    $statusDeliveryMessage = "Link kehadiran berhasil dikirim ke Participant";
                }
                $value->link_event_delivery_status = $statusDelivery;
                $value->link_event_sent = $statusDeliveryMessage;
            }

            $value->manasik_online = $onlines;
        }

        return response()->json($attendances);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required_if:act,add|required_if:act,checkin,checkinBarcode,photoBoothBarcode',
            'participant_id' => 'required_if:act,add',
            'barcode' => 'required_if:act,checkinBarcode|uuid',
            'participant_id' => 'required_if:act,checkin',
            'act' => 'required|in:add,remove,checkin,checkinBarcode,photoBoothBarcode',
        ],['barcode.required' => 'Barcode is invalid','barcode.uuid' => 'Barcode is invalid']);
        if ($request->act == 'checkin') {
            $attendee = Attendance::where('participant_id',$request->get('participant_id'))->where('event_id', request()->event_id)->whereNull('session')->orderBy('id', 'ASC')->first();
            $this->toogleCheckIn($attendee);
            return response()->json(['success' => 'ok']);
        }

        if ($request->act == 'checkinBarcode') {
            return $this->checkInBarcode($request);
        }

        if ($request->act == 'photoBoothBarcode') {
            return $this->photoBoothBarcode($request);
        }
        Attendance::updateOrCreate(['event_id' => $request->event_id, 'participant_id' => $request->participant_id], $request->all());
        return response()->json(['success' => 'ok']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EventAttendance $event
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $event_attendee)
    {
        return response()->json($event_attendee->toArray());
    }

    public function destroy(Attendance $event_attendee)
    {
        $event_attendee->delete();
    }

    public function delete(Request $request)
    {
        Attendance::where('event_id', $request->event_id)->where('participant_id', $request->participant_id)->delete();
    }

    public function resetDepartureConfirmation(Request $request)
    {
        Attendance::where('event_id', $request->event_id)->where('participant_id', $request->participant_id)->update([
            'departure_from' => null, 
            'departure_confirmation_at' => null,
            'departure_confirmation_by' => null
        ]);
    }

    private function toogleCheckIn(Attendance $attendee)
    {
        if ($attendee->check_in_at == null) {
            $attendee->check_in_at = now();
        } else {
            $attendee->check_in_at = null;
        }
        $attendee->save();
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

    private function photoBoothBarcode(Request $request)
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

        if ($attendee->photo_at != null) {
            $checkInAt = Carbon::parse($attendee->photo_at)->format('M j, Y g:i:s A');
            throw ValidationException::withMessages(['barcode' => ["{$participant->name} photo already taken at {$checkInAt}"]]);
        }

        $checkIn = now();
        $attendee->photo_at = $checkIn;
        $attendee->save();
        return response()->json(['checkInAt' => Carbon::parse($checkIn)->format('Y-m-d H:i:s'), 'attendee' => $participant]);
    }

}
