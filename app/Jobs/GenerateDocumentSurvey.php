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
use App\Models\Form;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SummarySurveyExport;
use App\Mail\GenerateDocument\GenerateDocumentSurveyMail;
use App\Mail\GenerateDocument\ErrorGenerateDocumentSurveyMail;

class GenerateDocumentSurvey implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200;
    public $mail_to;
    public $formId;
    public $request;
    public $documentName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($formId, $request)
    {
        $this->mail_to = \config('mail.mail_sender_crm');
        $this->formId = $formId;
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
            $form = Form::find($this->formId);
            $fileName = $form->title ?? '';
            $this->documentName = $fileName;

            $storageKey = "/Downloads/Summary-" . $fileName ."-". hrtime(true) . ".xlsx";
            
            Excel::store(new SummarySurveyExport($form, $this->request), $storageKey);
            $expiredAt = now('UTC')->addDays(1);
            $params = [
                'Content-Disposition' => 'attachment',
                'ResponseExpires' => $expiredAt->format('D, d M Y H:i:s \G\M\T')
            ];
            $presignedUrl = Storage::temporaryUrl($storageKey, $expiredAt, $params);
            Mail::to($this->mail_to)->queue(new GenerateDocumentSurveyMail($this->documentName, $presignedUrl));
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            $this->failed($e);
        }
        
    }

    public function failed($e)
    {
        Mail::to($this->mail_to)->queue(new ErrorGenerateDocumentSurveyMail($this->documentName, $e->getMessage()));
    }
}
