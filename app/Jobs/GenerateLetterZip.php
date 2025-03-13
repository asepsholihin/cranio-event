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
use App\Mail\GenerateLetter\GenerateLetterMail;
use App\Mail\GenerateLetter\ErrorGenerateLetterMail;
use App\Models\UmrohTrip;
use App\Models\Participant;
use App\Models\ParticipantUmrohTrip;
use App\Models\OrderUmrohTrip;
use App\Models\PackageUmrohTrip;
use App\File\PDF\LetterParticipant;
use ZipArchive;
use App\Exceptions\ErrorMessageException;

class GenerateLetterZip implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mail_to;
    public $umrohTripId;
    public $participantIds;
    public $categories;
    public $yearLetter;
    public $page;
    public $limit;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($yearLetter, $umrohTripId, $participantIds, $letterType, $page, $limit)
    {
        $this->mail_to = \config('mail.mail_sender');
        $this->page = $page;
        $this->limit = $limit;
        $this->umrohTripId = $umrohTripId;
        $this->participantIds = $participantIds;
        $this->yearLetter = $yearLetter;
        $this->categories = array();
        if($letterType == "common") {
            $this->categories = array('Surat Izin','Surat Pernyataan dan Jaminan','Surat Rekomendasi Meningitis','Surat Pengantar Pembuatan Paspor Baru','Surat Pengantar Perpanjang Paspor','Surat Pengantar Penggantian Paspor','Surat Pengantar Penambahan Nama Paspor','Surat Keterangan Participant');
        }
        if($letterType == "mou") {
            $this->categories = array('Surat Perjanjian Perjalanan Umrah');
            $umrohTrip = UmrohTrip::findOrFail($umrohTripId);
            if($umrohTrip->category_id == 1)
                $this->categories = array('Surat Perjanjian Perjalanan Umrah');
            if($umrohTrip->category_id == 2) {
                $packageId = ParticipantUmrohTrip::whereIn('id', $this->participantIds)->where('umroh_trip_id', $this->umrohTripId)->whereNotNull('booking_order_no')->first()->package_umroh_trip_id ?? null;
                $sub_category = PackageUmrohTrip::find($packageId)->sub_category_id ?? null;
                if($sub_category == 5)
                    $this->categories = array('Surat Perjanjian Perjalanan Haji Furoda');
                if($sub_category == 6)
                    $this->categories = array('Surat Perjanjian Perjalanan Haji Khusus','Surat Persetujuan Haji Khusus','Surat Kuasa Haji Khusus','Surat Perjanjian Haji Khusus','Surat Pemberitahuan Nomor Porsi');
            }
        }
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
            $participantIds = ParticipantUmrohTrip::whereIn('id', $this->participantIds)->where('umroh_trip_id', $this->umrohTripId)
            ->whereNotNull('booking_order_no')->skip(($this->page - 1) * $this->limit)->take($this->limit)->pluck('participant_id');
            $umrohTrip = UmrohTrip::findOrFail($this->umrohTripId);
            $zip = new ZipArchive;
            $fileName = $umrohTrip->title."_part".$this->page."_".time().".zip";

            if ($zip->open(storage_path('app/'.$fileName), ZipArchive::CREATE) === TRUE) {
                $this->addZipLetters($umrohTrip, $zip, $participantIds);
            }

            $zip->close();
            if (! file_exists(storage_path('app/'.$fileName))) {
                throw new ErrorMessageException('There is no file to be downloaded');
            }

            // Deleting files after ZIP
            $this->deleteFiles($participantIds);

            $storageKey = "/Downloads/{$fileName}";
            Storage::put($storageKey, fopen(storage_path('app/'.$fileName), 'r'));
            unlink(storage_path('app/'.$fileName));

            $expiredAt = now('UTC')->addDays(1);
            $params = [
                'Content-Disposition' => 'attachment',
                'ResponseExpires' => $expiredAt->format('D, d M Y H:i:s \G\M\T')
            ];

            $presignedUrl = Storage::temporaryUrl($storageKey, $expiredAt, $params);

            Mail::to($this->mail_to)->queue(new GenerateLetterMail($presignedUrl, $this->umrohTripId, $this->page));
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            $this->failed($e);
        }
        
    }

    private function addZipLetters($umrohTrip, $zip, $participantIds)
    {
        $participants = Participant::select(['participant.id','participant.name'])
        ->whereIn('participant.id', $participantIds)
        ->get();
        
        foreach ($participants as $key => $participant) {
            foreach ($this->categories as $category) {
                $fileNamePDF = $category .' '. $participant->name .'.pdf';
                $file = storage_path('app/'.$fileNamePDF);
                $participantUmrohTrip = ParticipantUmrohTrip::where('umroh_trip_id', $umrohTrip->id)->where('participant_id', $participant->id)->first();
                $order = OrderUmrohTrip::where('order_no', $participantUmrohTrip->booking_order_no)->first();
                $packageUmroh = PackageUmrohTrip::find($participantUmrohTrip->package_umroh_trip_id);
                $output = (new LetterParticipant($this->yearLetter, $participantUmrohTrip, $umrohTrip->id, $category))->output();
                file_put_contents($file, $output);

                $folderPath = "DOKUMEN SURAT - " .strtoupper($packageUmroh->name) ."/". "DOKUMEN AKUN - ". strtoupper($order->name) . " - " . str_replace("/","_",$order->order_no) ."/". strtoupper($participant->name) ."/";
                $path = $folderPath . $fileNamePDF;
                $zip->addFile($file, $path);
            }
        }
    }

    private function deleteFiles($participantIds)
    {
        $participants = Participant::select(['participant.id','participant.name'])
        ->whereIn('participant.id', $participantIds)
        ->get();
        
        foreach ($participants as $key => $participant) {
            foreach ($this->categories as $category) {
                $fileNamePDF = $category .' '. $participant->name .'.pdf';
                $file = storage_path('app/'.$fileNamePDF);
                unlink($file);
            }
        }
    }

    public function failed($e)
    {
        Mail::to($this->mail_to)->queue(new ErrorGenerateLetterMail($e->getMessage(), $this->umrohTripId));
    }
}
