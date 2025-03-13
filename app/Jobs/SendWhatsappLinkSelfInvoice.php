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

use App\Models\UmrohTrip;
use App\Models\BookingTransaction;
use App\Models\LogQontakBroadcast;
use App\Models\Participant;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Mail\GenerateDocument\ErrorJobMail;


class SendWhatsappLinkSelfInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $phoneNumber;
    public $name;
    public $templateId;
    public $channelId;
    public $order;

    public function __construct($order)
    {
        App::setLocale('id');
        $this->order = $order;
        $qontakTemplate = DB::table('qontak_templates')->where('name', 'link-self-invoice')->first();
        $this->mail_to = \config('mail.mail_sender');
        $this->phoneNumber = $order->no_hp;
        $this->name = $order->name;
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
        try {
            $credentials = new Credential(\config('qontak.username'), \config('qontak.password'), \config('qontak.client_id'), \config('qontak.client_secret'));
            $client = new Client($credentials);

            $umrohTrip = UmrohTrip::find($this->order->umroh_trip_id);
            
            if (Str::startsWith($this->phoneNumber, '0')) {
                $this->phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $this->phoneNumber);
            }

            $bookingOrder = strtoupper(str_replace("/","-", $this->order->order_no));
            $link = "https://www.jejakimani.com/ruang-participant/generate-invoice?booking=". $bookingOrder ."&hp=". $this->phoneNumber;

            // Create message receiver
            $name_formated = rtrim(preg_replace(array('/\s{2,}/', '/[\t\n]/', '/[^a-zA-Z0-9\']/','/[^\p{L}\p{N}]/u', '/\s*(?:[\d_]|[^\w\s])+/', '!\s+!'), ' ', $this->name), " ");
            $receiver = new Receiver($this->phoneNumber, $name_formated);

            // [Optional] Create language, supported 'en' and 'id', default is 'id'
            $language = new Language('id');

            // [Optional] Create params message body
            $body = [
                new Body($this->name),
                new Body($umrohTrip->title ?? ''),
                new Body($link)
            ];

            // [Optional] Create header message, support "DOCUMENT", "VIDEO", "IMAGE"
            $header = null;

            // [Optional] Create buttons
            $buttons = [];

            $message = new Message($receiver, $language, $body, $header, $buttons);
            $response = $client->send($this->templateId, $this->channelId, $message);

            LogQontakBroadcast::create([
                'participant_id' => 0,
                'name' => $this->name,
                'no_hp' => $this->phoneNumber,
                'channel_id' => $this->channelId,
                'template_id' => $this->templateId,
                'type' => 'Link generate self invoice',
                'status' => 'Delivered',
                'reason' => json_encode($response->getData()),
                'request_body' => json_encode(\App\Support\QontakBodyRequest::makeRequestBody($message))
            ]);
        } catch (\Exception $e) {
            LogQontakBroadcast::create([
                'participant_id' => 0,
                'name' => $this->name,
                'no_hp' => $this->phoneNumber,
                'channel_id' => $this->channelId,
                'template_id' => $this->templateId,
                'type' => 'Link generate self invoice',
                'status' => 'Failed',
                'reason' => $e->getMessage()
            ]);
            Log::error('Error: ' . $e->getMessage());
        }
    }
}
