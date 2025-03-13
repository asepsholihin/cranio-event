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

use App\Models\InvoiceUmrohTrip;
use App\Models\LogQontakBroadcast;
use App\Models\Participant;
use App\File\PDF\InvoiceBookingSeatPDF;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Mail\GenerateDocument\ErrorJobMail;


class SendWhatsappHistoryInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $phoneNumber;
    public $name;
    public $templateId;
    public $channelId;
    public $order;
    public $invoice;

    public function __construct($order)
    {
        App::setLocale('id');
        $this->order = $order;
        $this->invoice = InvoiceUmrohTrip::whereNull('deleted_at')->where('order_umroh_trip_id', $order->id)->orderBy('created_at', 'desc')->first();
        $qontakTemplate = DB::table('qontak_templates')->where('name', 'histrory-invoice')->first();
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

            if (Str::startsWith($this->phoneNumber, '0')) {
                $this->phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $this->phoneNumber);
            }

            $fileName_inv = "Invoice ".$this->order->name. "_".str_replace("/", "_", $this->order->order_no)."_".Carbon::now()->timestamp.".pdf";
            $filename = str_replace(" ", "_", $fileName_inv);
            $invoicePDF = (new InvoiceBookingSeatPDF($this->order, $this->invoice));
            $storageKey_inv = InvoiceUmrohTrip::DIR_INVOICE . "/{$filename}";
            Storage::put($storageKey_inv, $invoicePDF->output(), 'r');
            
            $this->invoice->url = $storageKey_inv;
            $this->invoice->save();

            // Create message receiver
            $name_formated = rtrim(preg_replace(array('/\s{2,}/', '/[\t\n]/', '/[^a-zA-Z0-9\']/','/[^\p{L}\p{N}]/u', '/\s*(?:[\d_]|[^\w\s])+/', '!\s+!'), ' ', $this->name), " ");
            $receiver = new Receiver($this->phoneNumber, $name_formated);

            // [Optional] Create language, supported 'en' and 'id', default is 'id'
            $language = new Language('id');

            // [Optional] Create params message body
            $body = [
                new Body($this->name)
            ];

            // [Optional] Create header message, support "DOCUMENT", "VIDEO", "IMAGE"
            $header = new Header(
                Header::TYPE_DOCUMENT, $this->invoice->url, $filename
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
                'type' => 'Histori Invoice',
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
                'type' => 'Histori Invoice',
                'status' => 'Failed',
                'reason' => $e->getMessage()
            ]);
            Log::error('Error: ' . $e->getMessage());
        }
    }
}
