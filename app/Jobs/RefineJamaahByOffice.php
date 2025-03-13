<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Inisiatif\WhatsappQontakPhp\Client;
use Inisiatif\WhatsappQontakPhp\Credential;
use Inisiatif\WhatsappQontakPhp\Message\Body;
use Inisiatif\WhatsappQontakPhp\Message\Button;
use Inisiatif\WhatsappQontakPhp\Message\Header;
use Inisiatif\WhatsappQontakPhp\Message\Message;
use Inisiatif\WhatsappQontakPhp\Message\Receiver;
use Inisiatif\WhatsappQontakPhp\Message\Language;

use App\Models\Participant;
use App\Models\ParticipantCRM;
use App\Models\ParticipantUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\OrderUmrohTrip;
use App\Models\InvoiceUmrohTrip;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\GenerateDocument\ErrorJobMail;
use Carbon\Carbon;
use DB;


class RefineParticipantByOffice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        App::setLocale('id');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function() {
            $orders = OrderUmrohTrip::select('id','order_no','created_by_user_id')->orderBy('id', 'ASC')->get();
            foreach ($orders as $key => $order) {
                $participantUmrohTrip = ParticipantUmrohTrip::where('booking_order_no', $order->order_no)->update(['created_by'=>$order->created_by_user_id,'updated_by'=>$order->created_by_user_id]);
                $participant = Participant::join('participant_umroh_trips', 'participant_umroh_trips.participant_id', 'participant.id')->where('booking_order_no', $order->order_no)->update(['created_by'=>$order->created_by_user_id,'updated_by'=>$order->created_by_user_id]);
                $participant_crm = ParticipantCRM::join('participant_umroh_trips', 'participant_umroh_trips.participant_id', 'participant_crm.id')->where('booking_order_no', $order->order_no)->update(['created_by'=>$order->created_by_user_id,'updated_by'=>$order->created_by_user_id]);
                $invoice_umroh_trips = InvoiceUmrohTrip::where('order_umroh_trip_id', $order->id)->update(['created_by'=>$order->created_by_user_id,'updated_by'=>$order->created_by_user_id]);
            }
        });

    }

    public function failed($e)
    {
        
    }
}
