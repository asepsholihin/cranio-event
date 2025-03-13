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
use App\File\PDF\LuggageTag;
use App\File\PDF\KoperTag;
use App\File\PDF\IdCard;
use App\File\PDF\Certificate;
use App\File\PDF\AttendanceTag;
use App\Models\UmrohTrip;

class GenerateDocumentPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mail_to;
    public $document;
    public $umrohTripId;
    public $type;
    public $bus;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($document, $umrohTripId, $type, $bus)
    {
        $this->mail_to = \config('mail.mail_sender');
        $this->document = $document;
        $this->umrohTripId = $umrohTripId;
        $this->type = $type;
        $this->bus = $bus;
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
            if($this->document == "luggage_tag") {
                $document = new LuggageTag($this->umrohTripId, $this->type);
                $fileName = "Luggage_Tag_".$umrohTrip->title.".pdf";
            } elseif($this->document == "koper_tag") {
                $document = new KoperTag($this->umrohTripId);
                $fileName = "Koper_Tag_".$umrohTrip->title.".pdf";
            } elseif($this->document == "id_card") {
                $document = new IdCard($this->umrohTripId, $this->type, $this->bus);
                $fileName = "ID_Card_".$umrohTrip->title.".pdf";
            } elseif($this->document == "certificate") {
                $document = new Certificate($this->umrohTripId);
                $fileName = "Certificate_".$umrohTrip->title.".pdf";
            } elseif($this->document == "attendance_tag") {
                $document = new AttendanceTag($this->umrohTripId);
                $fileName = "Attendance_Tag_".$umrohTrip->title.".pdf";
            }

            $storageKey = "/DocumentPDF/{$fileName}";
            Storage::put($storageKey, $document->output(), 'r');

            $expiredAt = now('UTC')->addDays(1);
            $params = [
                'Content-Disposition' => 'attachment',
                'ResponseExpires' => $expiredAt->format('D, d M Y H:i:s \G\M\T')
            ];

            $presignedUrl = Storage::temporaryUrl($storageKey, $expiredAt, $params);

            Mail::to($this->mail_to)->queue(new GenerateDocumentMail($this->document, $presignedUrl, $this->umrohTripId, null));
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
