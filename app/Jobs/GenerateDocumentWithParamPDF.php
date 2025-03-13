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
use App\Mail\GenerateDocument\GenerateDocumentMail;
use App\Mail\GenerateDocument\ErrorGenerateDocumentMail;
use App\File\PDF\DocumentPassports;
use App\Models\UmrohTrip;

class GenerateDocumentWithParamPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mail_to;
    public $document;
    public $umrohTripId;
    public $request;
    public $page;
    public $limit;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($document, $umrohTripId, $request, $page, $limit)
    {
        $this->mail_to = \config('mail.mail_sender');
        $this->document = $document;
        $this->umrohTripId = $umrohTripId;
        $this->request = $request;
        $this->page = $page;
        $this->limit = $limit;
        $this->onQueue('generate-asset'); # php artisan queue:work generate-asset
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $umrohTrip = UmrohTrip::findOrFail($this->umrohTripId);
        try{
            $fileName = "";
            if($this->document == "passport") {
                $document = new DocumentPassports($this->umrohTripId, $this->request, $this->page, $this->limit);
                $fileName = "PASSPORT_".$umrohTrip->title."_part".$this->page."_".time().".pdf";
            }

            $storageKey = "/DocumentPDF/{$fileName}";
            Storage::put($storageKey, $document->output(), 'r');

            $expiredAt = now('UTC')->addDays(1);
            $params = [
                'Content-Disposition' => 'attachment',
                'ResponseExpires' => $expiredAt->format('D, d M Y H:i:s \G\M\T')
            ];

            $presignedUrl = Storage::temporaryUrl($storageKey, $expiredAt, $params);

            Mail::to($this->mail_to)->queue(new GenerateDocumentMail($this->document, $presignedUrl, $this->umrohTripId, $this->page));
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            $this->failed($e);
        }
        
    }

    public function failed($e)
    {
        Mail::to($this->mail_to)->queue(new ErrorGenerateDocumentMail($this->document, $e->getMessage(), $this->umrohTripId));
    }
}
