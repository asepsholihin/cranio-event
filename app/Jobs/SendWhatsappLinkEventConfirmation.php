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
use App\Models\ParticipantUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\LogQontakBroadcast;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Mail\GenerateDocument\ErrorJobMail;
use Carbon\Carbon;


class SendWhatsappLinkEventConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mail_to;
    public $event;
    public $partialParticipantId;
    public $templateId;
    public $channelId;

    public function __construct($event, $partialParticipantId=null)
    {
        App::setLocale('id');
        $qontakTemplate = DB::table('qontak_templates')->where('name', 'link-confirmation-closed-event')->first();
        $this->mail_to = \config('mail.mail_sender');
        $this->event = $event;
        $this->partialParticipantId = $partialParticipantId;
        $this->templateId = $qontakTemplate->template_id;
        $this->channelId = '96f2fe5f-fcd8-4e97-ba92-fa2d66741010';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $credentials = new Credential(\config('qontak.username'), \config('qontak.password'), \config('qontak.client_id'), \config('qontak.client_secret'));
        $client = new Client($credentials);

        $umrohTrip = UmrohTrip::find($this->event->umroh_trip_id);
        $queryParticipants = ParticipantUmrohTrip::select(['participant.id','participant.name','participant.no_hp', 'participant_umroh_trips.booking_order_no', 'participant_umroh_trips.package_umroh_trip_id', 'package_umroh_trips.name as package_name'])
        ->join('participants', 'participant.id', 'participant_umroh_trips.participant_id')
        ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id');
        if($this->partialParticipantId) {
            $queryParticipants->where('participant_umroh_trips.participant_id', $this->partialParticipantId);
        }
        $participants = $queryParticipants->orderBy('participant_umroh_trips.created_at', 'ASC')->limit(1)->get();

        foreach ($participants as $key => $participant) {
            $totalParticipantRef = ParticipantUmrohTrip::whereNotNull('booking_order_no')->where('booking_order_no', $participant->booking_order_no)->count();
            if($totalParticipantRef > 1) {
                $textReference = "Bapak/Ibu dan " . ($totalParticipantRef - 1) . " participant lainnya";
            } else {
                $textReference = "Bapak/Ibu";
            }

            $phoneNumber = $participant->no_hp;
            if (Str::startsWith($phoneNumber, '0')) {
                $phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $phoneNumber);
            }

            $openGateHour = date('H:i', strtotime($this->event->event_at));
            if($this->event->open_gate_split == 1) {
                foreach (json_decode($this->event->open_gate) as $gate) {
                    if($participant->package_umroh_trip_id == $gate->package_id) {
                        $openGateHour = $gate->open_gate_at;
                    }
                }
            }

            try {
                // Create message receiver
                $receiver = new Receiver($phoneNumber, $participant->name);

                // [Optional] Create language, supported 'en' and 'id', default is 'id'
                $language = new Language('id');

                // [Optional] Create params message body
                $body = [
                    new Body($participant->name),
                    new Body($umrohTrip->title),
                    new Body($textReference),
                    new Body($this->event->name),
                    new Body($this->event->location),
                    new Body(Carbon::parse($this->event->event_date)->translatedFormat('l, d F Y')),
                    new Body($participant->package_name),
                    new Body($openGateHour . " WIB"),
                    new Body("https://www.jejakimani.com/event-confirmation/" . $this->event->slug)
                ];

                // [Optional] Create header message, support "DOCUMENT", "VIDEO", "IMAGE"
                $header = null;

                // [Optional] Create buttons
                $buttons = [];

                $message = new Message($receiver, $language, $body, $header, $buttons);
                $response = $client->send($this->templateId, $this->channelId, $message);

                LogQontakBroadcast::updateOrCreate(
                [
                    'participant_id' => $participant->id,
                    'umroh_trip_id' => $this->event->umroh_trip_id,
                    'type' => 'Link Event',
                ],[
                    'participant_id' => $participant->id,
                    'name' => $participant->name,
                    'no_hp' => $phoneNumber,
                    'channel_id' => $this->channelId,
                    'template_id' => $this->templateId,
                    'type' => 'Link Event',
                    'status' => 'Delivered',
                    'umroh_trip_id' => $this->event->umroh_trip_id,
                    'event_id' => $this->event->id,
                    'reason' => json_encode($response->getData()),
                    'request_body' => json_encode(\App\Support\QontakBodyRequest::makeRequestBody($message))
                ]);
                // Message Id and Receiver Name
                // echo $response->getMessageId();
                // echo $response->getName();
            } catch (\Exception $e) {
                LogQontakBroadcast::updateOrCreate(
                [
                    'participant_id' => $participant->id ?? '',
                    'umroh_trip_id' => $this->event->umroh_trip_id,
                    'type' => 'Link Event',
                ],[
                    'participant_id' => $participant->id ?? '',
                    'name' => $participant->name ?? '',
                    'no_hp' => $phoneNumber ?? '',
                    'channel_id' => $this->channelId,
                    'template_id' => $this->templateId,
                    'type' => 'Link Event',
                    'status' => 'Failed',
                    'reason' => $e->getMessage(),
                    'umroh_trip_id' => $this->event->umroh_trip_id,
                    'event_id' => $this->event->id
                ]);
                Log::error('Error: ' . $e->getMessage());
                $this->failed($e);
            }
        }

    }

    public function failed($e)
    {
        // Mail::to($this->mail_to)->queue(new ErrorJobMail("Error Kirim Whatsapp Link Event - " . $e->getMessage()));
    }
}
