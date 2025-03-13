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


class SendWhatsappBookingPaidInvoiceAndReceipt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $phoneNumber;
    public $name;
    public $templateId;
    public $channelId;
    public $order;
    public $invoice;

    public function __construct($order, $invoice)
    {
        App::setLocale('id');
        $this->order = $order;
        $this->invoice = $invoice;
        $qontakTemplate = DB::table('qontak_templates')->where('name', 'invoice-and-receipt')->first();
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
        $credentials = new Credential(\config('qontak.username'), \config('qontak.password'), \config('qontak.client_id'), \config('qontak.client_secret'));
        $client = new Client($credentials);

        $umrohTrip = UmrohTrip::find($this->order->umroh_trip_id);
        $transaction = BookingTransaction::where('order_umroh_trip_id', $this->order->id)->where('invoice_umroh_trip_id', $this->invoice->id)->where('transaction_status', 'PAID')->first();
        $fileName = "kwitansi_dan_invoice_".str_replace(" ", "_", $this->order->name)."_".str_replace("/", "_", $this->invoice->invoice_no)."_".Carbon::now()->timestamp.".pdf";
        $paymentAmount = $this->invoice->payment_amount;
        if($transaction) {
            $paymentInformation = json_decode($transaction->payment_information);
            $paymentAmount = $paymentInformation->paid_amount;
            $fileName = 'kwitansi_dan_invoice_'.$transaction->transaction_id."_".Carbon::now()->timestamp.'.pdf';
        }

        $currency = $umrohTrip->currency ?? "IDR";
        if($this->invoice->usd_convertion){
            $currency = "IDR";
        }

        if (Str::startsWith($this->phoneNumber, '0')) {
            $this->phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $this->phoneNumber);
        }

        // Create message receiver
        $name_formated = rtrim(preg_replace(array('/\s{2,}/', '/[\t\n]/', '/[^a-zA-Z0-9\']/','/[^\p{L}\p{N}]/u', '/\s*(?:[\d_]|[^\w\s])+/', '!\s+!'), ' ', $this->name), " ");
        $receiver = new Receiver($this->phoneNumber, $name_formated);

        // [Optional] Create language, supported 'en' and 'id', default is 'id'
        $language = new Language('id');

        $due = "--";
        $due_date = "--";
        if($umrohTrip) {
            $due = $umrohTrip->invoice_due_date;
            $due_date = Carbon::createFromFormat('Y-m-d', $umrohTrip->departure_at)->subDays($due)->isoFormat('D MMMM Y');
        }

        // [Optional] Create params message body
        $body = [
            new Body($this->invoice->description),
            new Body($currency . " " . number_format($paymentAmount, 0, ',', '.')),
            new Body($due),
            new Body($due_date)
        ];

        // [Optional] Create header message, support "DOCUMENT", "VIDEO", "IMAGE"
        $header = new Header(
            Header::TYPE_DOCUMENT, $this->invoice->invoice_and_receipt_url, $fileName
        );

        // [Optional] Create buttons
        $buttons = [];

        try {
            $message = new Message($receiver, $language, $body, $header, $buttons);
            $response = $client->send($this->templateId, $this->channelId, $message);

            LogQontakBroadcast::create([
                'participant_id' => 0,
                'name' => $this->name,
                'no_hp' => $this->phoneNumber,
                'channel_id' => $this->channelId,
                'template_id' => $this->templateId,
                'type' => "Pengiriman invoice dan kwitansi bersamaan",
                'status' => 'Delivered',
                'reason' => json_encode($response->getData()),
                'request_body' => json_encode(\App\Support\QontakBodyRequest::makeRequestBody($message)),
                'umroh_trip_id' => $this->invoice->umroh_trip_id
            ]);
        } catch (\Exception $e) {
            LogQontakBroadcast::create([
                'participant_id' => 0,
                'name' => $this->name,
                'no_hp' => $this->phoneNumber,
                'channel_id' => $this->channelId,
                'template_id' => $this->templateId,
                'type' => "Pengiriman invoice dan kwitansi bersamaan",
                'status' => 'Failed',
                'reason' => $e->getMessage(),
                'request_body' => json_encode(\App\Support\QontakBodyRequest::makeRequestBody($message)),
                'umroh_trip_id' => $this->invoice->umroh_trip_id
            ]);
            Log::error('Error: ' . $e->getMessage());
        }
    }
}
