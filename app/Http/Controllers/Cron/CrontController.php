<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OrderUmrohTrip;
use App\Models\EventAttendance;
use App\Models\Attendance;
use App\Models\LogQontakBroadcast;
use App\Actions\Qontak\Greeting;
use App\Actions\Qontak\Document;
use App\Jobs\SendWhatsappLinkEventConfirmation;
use Carbon\Carbon;

class CrontController extends Controller
{
    public function sendQontakDocumentInformation()
    {
        $orderUmrohTrips = OrderUmrohTrip::select('id','umroh_trip_id','name','no_hp', 'order_no', 'transaction_date')
        ->where('paid', '>', 0)->where('transaction_date', '>', Carbon::now()->subDays(1))
        ->orderBy('id', 'desc')->get();
        foreach($orderUmrohTrips as $key => $item) {
            Document::sendDocumentInfo($item);
        }
    }

    public function sendClosedEventConfirmation()
    {
        $dueDate = Carbon::now()->addDays(3);
        $eventAttendances = EventAttendance::where('event_date', $dueDate)->get();
        $arr = [];
        foreach ($eventAttendances as $event) {
            $participants = Attendance::select('participant.id','participant.name','participant.no_hp')
            ->join('participant', 'participant.id', 'attendances.participant_id')
            ->where('event_id', $event->id)->groupBy('participant.id')->get();
            foreach ($participants as $key => $participant) {
                $logQontakBroadcast = LogQontakBroadcast::where('participant_id', $participant->id)
                ->where('type', 'Link Event')->where('umroh_trip_id',$event->umroh_trip_id)->where('event_id',$event->id)
                ->where('status', 'Delivered')->first();
                if(!$logQontakBroadcast) {
                    $arr[] = $participant;
                    SendWhatsappLinkEventConfirmation::dispatch($event, $participant->id)->delay(Carbon::now()->addSeconds(($key*30)));
                }
            }
        }

        return response()->json($arr);
    }
}
