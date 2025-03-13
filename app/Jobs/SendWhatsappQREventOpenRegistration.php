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

use App\Models\LogQontakBroadcast;
use App\Models\Participant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\File\Image\BarcodeParticipant;
use App\Mail\GenerateDocument\ErrorJobMail;
use Carbon\Carbon;


class SendWhatsappQREventOpenRegistration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mail_to;
    public $event;
    public $phoneNumber;
    public $name;
    public $barcode;
    public $templateId;
    public $channelId;

    public function __construct($event, $phoneNumber, $name, $barcode)
    {
        App::setLocale('id');
        $qontakTemplate = DB::table('qontak_templates')->where('name', 'link-confirmation-open-event')->first();
        $this->mail_to = \config('mail.mail_sender');
        $this->event = $event;
        $this->phoneNumber = $phoneNumber;
        $this->name = $name;
        $this->barcode = $barcode;
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

            if (Str::startsWith($this->phoneNumber, '0')) {
                $this->phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $this->phoneNumber);
            }
            $fileName = Str::slug($this->name, '_') . "_barcode_" .Carbon::now()->timestamp. ".jpg";

            // Create message receiver
            $name_formated = rtrim(preg_replace(array('/\s{2,}/', '/[\t\n]/', '/[^a-zA-Z0-9\']/','/[^\p{L}\p{N}]/u', '/\s*(?:[\d_]|[^\w\s])+/', '!\s+!'), ' ', $this->name), " ");
            $receiver = new Receiver($this->phoneNumber, $name_formated);

            // [Optional] Create language, supported 'en' and 'id', default is 'id'
            $language = new Language('id');

            // [Optional] Create params message body
            $body = [
                new Body($this->name),
                new Body($this->event)
            ];

            // [Optional] Create header message, support "DOCUMENT", "VIDEO", "IMAGE"
            $header = new Header(
                Header::TYPE_IMAGE, $this->barcode, $fileName
            );

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
                'type' => 'QR Attendance Event',
                'status' => 'Delivered',
                'reason' => json_encode($response->getData()),
                'request_body' => json_encode(\App\Support\QontakBodyRequest::makeRequestBody($message))
            ]);

            // Message Id and Receiver Name
            // echo $response->getMessageId();
            // echo $response->getName();
            
        } catch (\Exception $e) {
            LogQontakBroadcast::create([
                'participant_id' => 0,
                'name' => $this->name,
                'no_hp' => $this->phoneNumber,
                'channel_id' => $this->channelId,
                'template_id' => $this->templateId,
                'type' => 'QR Attendance Event',
                'status' => 'Failed',
                'reason' => $e->getMessage()
            ]);
            Log::error('Error: ' . $e->getMessage());
        }

    }

    public function failed($e)
    {
        // Mail::to($this->mail_to)->queue(new ErrorJobMail("Error Kirim Whatsapp QR Absensi - " . $e->getMessage()));
    }
}
