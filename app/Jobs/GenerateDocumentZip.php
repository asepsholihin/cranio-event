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
use App\Models\UmrohTrip;
use App\Models\Participant;
use App\Models\ParticipantUmrohTrip;
use ZipArchive;
use App\Exceptions\ErrorMessageException;

class GenerateDocumentZip implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mail_to;
    public $umrohTripId;
    public $request;
    public $document;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->mail_to = \config('mail.mail_sender');
        $this->umrohTripId = $request['umrohTripId'];
        $this->request = $request;
        $this->document = 'participant_documents';
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
            $participantIds = ParticipantUmrohTrip::where('participant_umroh_trips.umroh_trip_id', $this->umrohTripId)->pluck('participant_id');
            $umrohTrip = UmrohTrip::findOrFail($this->umrohTripId);
            $zip = new ZipArchive;
            $fileName = $umrohTrip->title.".zip";
            if ($zip->open(storage_path('app/'.$fileName), ZipArchive::CREATE) === TRUE) {
                $this->addZipDocuments($umrohTrip, $zip, $participantIds);
            }

            $zip->close();
            if (! file_exists(storage_path('app/'.$fileName))) {
                throw new ErrorMessageException('There is no file to be downloaded');
            }
            $storageKey = "/Downloads/{$fileName}";
            Storage::put($storageKey, fopen(storage_path('app/'.$fileName), 'r'));
            unlink(storage_path('app/'.$fileName));

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

    private function addZipDocuments($umrohTrip, $zip, $participantIds)
    {
        if(!empty($this->request['documentType'])) {
            if($this->request['documentType'] == 'all' || $this->request['documentType'] == 'Pas Photo') {
                $queryParticipant = Participant::select(['participant.ji_code', 'participant.name', 'participant.profile_photo_path', 'order_umroh_trips.order_no', 'order_umroh_trips.name as booking_name'])
                ->join('participant_umroh_trips', 'participant_umroh_trips.participant_id', 'participant.id')
                ->leftJoin('order_umroh_trips', 'participant_umroh_trips.booking_order_no', 'order_umroh_trips.order_no')
                ->whereNotNull('participant.profile_photo_path');
                if(!empty($this->request['packageUmrohTripId'])) {
                    $queryParticipant->where('participant_umroh_trips.package_umroh_trip_id', $this->request['packageUmrohTripId']);
                }
                if(!empty($this->request['booking'])) {
                    $queryParticipant->where('participant_umroh_trips.order_umroh_trip_id', $this->request['booking']);
                }
                if(!empty($this->request['q'])) {
                    $queryParticipant->where('participant.name', 'like', '%'.$this->request['q'].'%');
                }
                if(!empty($this->request['busGroup'])) {
                    $queryParticipant->where('participant_umroh_trips.group_bus', $this->request['busGroup']);
                }
                if(!empty($this->request['selectedDocumentIds'])) {
                    $queryParticipant->whereIn('participant.id', $this->request['selectedDocumentIds']);
                } else {
                    $queryParticipant->whereIn('participant.id', $participantIds);
                }
                $participant = $queryParticipant->get();

                foreach ($participant as $file) {
                    if(Storage::get($file->profile_photo_path)) {
                        $path_parts = pathinfo($file->profile_photo_path);
                        $path = strtoupper($umrohTrip->title) ."/". 
                        strtoupper($file->booking_name)."-".strtoupper(str_replace("/","_",$file->order_no)) ."/".
                        "PASFOTO_" .strtoupper($file->name) ."_".$file->ji_code.".{$path_parts['extension']}";
                        $zip->addFromString($path, Storage::get($file->profile_photo_path));
                    }
                }
            }
        }
        
        $queryFile = Participant::join('participant_files', 'participant_files.participant_id', '=', 'participant.id')
            ->select(['participant.ji_code','participant.name', 'participant_files.file_path', 'participant_files.title', 'order_umroh_trips.order_no', 'order_umroh_trips.name as booking_name'])
            ->join('participant_umroh_trips', 'participant_umroh_trips.participant_id', 'participant.id')
            ->leftJoin('order_umroh_trips', 'participant_umroh_trips.booking_order_no', 'order_umroh_trips.order_no')
            ->whereNotNull('participant_files.file_path');
            if(!empty($this->request['documentType'])) {
                if($this->request['documentType'] != 'all') {
                    $queryFile->where('participant_files.title', $this->request['documentType']);
                }
            }
            if(!empty($this->request['packageUmrohTripId'])) {
                $queryFile->where('participant_umroh_trips.package_umroh_trip_id', $this->request['packageUmrohTripId']);
            }
            if(!empty($this->request['booking'])) {
                $queryFile->where('participant_umroh_trips.order_umroh_trip_id', $this->request['booking']);
            }
            if(!empty($this->request['q'])) {
                $queryFile->where('participant.name', 'like', '%'.$this->request['q'].'%');
            }
            if(!empty($this->request['busGroup'])) {
                $queryFile->where('participant_umroh_trips.group_bus', $this->request['busGroup']);
            }
            if(!empty($this->request['selectedDocumentIds'])) {
                $queryFile->whereIn('participant.id', $this->request['selectedDocumentIds']);
            } else {
                $queryFile->whereIn('participant.id', $participantIds);
            }
            $files = $queryFile->get();
        foreach ($files as $key => $file) {
            if(Storage::get($file->file_path)) {
                $path_parts = pathinfo($file->file_path);
                $more_file = "_".($key+1);
                $path = strtoupper($umrohTrip->title) . "/" . 
                strtoupper($file->booking_name)."-".strtoupper(str_replace("/","_",$file->order_no)) ."/". 
                strtoupper($file->title) ."_". strtoupper($file->name) ."_".$file->ji_code.$more_file.".{$path_parts['extension']}";
                $zip->addFromString($path, Storage::get($file->file_path));
            }
        }
    }

    public function failed($e)
    {
        Mail::to($this->mail_to)->queue(new ErrorGenerateDocumentMail($this->document, $e->getMessage(), $this->umrohTripId));
    }
}
