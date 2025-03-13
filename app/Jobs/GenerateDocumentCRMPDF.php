<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Mail\GenerateDocument\GenerateDocumentCRMMail;
use App\Mail\GenerateDocument\ErrorGenerateDocumentCRMMail;
use App\File\PDF\LuggageTag;
use App\File\PDF\KoperTag;
use App\File\PDF\IdCard;
use App\File\PDF\Certificate;
use App\File\PDF\AttendanceTag;
use App\File\PDF\CertificateCRM;
use App\Models\UmrohTrip;

class GenerateDocumentCRMPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200;
    public $mail_to;
    public $document;
    public $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($document, $request)
    {
        $this->mail_to = \config('mail.mail_sender_crm');
        $this->document = $document;
        $this->request = $request;
        $this->onQueue('generate-asset'); # php artisan queue:work generate-asset
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $fileName = "";
            if($this->document == "certificate_participant_crm") {
                $document = new CertificateCRM($this->request['trip']);
                $fileName = "Certificate_".$this->request['trip'].".pdf";
            } 

            $storageKey = "/DocumentPDF/{$fileName}";
            Storage::put($storageKey, $document->output(), 'r');

            $expiredAt = now('UTC')->addDays(1);
            $params = [
                'Content-Disposition' => 'attachment',
                'ResponseExpires' => $expiredAt->format('D, d M Y H:i:s \G\M\T')
            ];

            $presignedUrl = Storage::temporaryUrl($storageKey, $expiredAt, $params);

            Mail::to($this->mail_to)->queue(new GenerateDocumentCRMMail($this->document, $presignedUrl, $this->request['trip']));
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            $this->failed($e);
        }
        
    }

    public function failed($e)
    {
        Mail::to($this->mail_to)->queue(new ErrorGenerateDocumentCRMMail($this->document, $e->getMessage(), $this->request['trip']));
    }
}
