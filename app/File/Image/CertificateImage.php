<?php

namespace App\File\Image;

use App\Models\Participant;
use App\Models\ParticipantFile;
use App\Models\UmrohTrip;
use Illuminate\Support\Str;
use SnappyImage;
use File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CertificateImage
{
    private $participant;
    private $umrohTripId;

    public function __construct(Participant $participant,$umroh_trip_id)
    {
        $this->participant = $participant;
        $this->umrohTripId = $participant->umroh_trip_id;
    }

    public function download()
    {
        $participant = $this->participant;
        $umrohTrip = UmrohTrip::findOrFail($this->umrohTripId);
        //return view('crm.milad_card_2', compact(['participant','title','photo']))->render();
        $img = SnappyImage::setOption('width', 100)
            ->setOption('enable-local-file-access', true)
            ->loadView('crm.certificates', compact(['participant','umrohTrip']));
        return $img->download("{$participant->name} Certificate.jpg");
    }

    public function stream()
    {
        $participant = $this->participant;
        $umrohTrip = UmrohTrip::findOrFail($this->umrohTripId);
        $fileName = $participant->name . "Certificate.jpg";

        $img = SnappyImage::setOption('width', 100)
            ->setOption('enable-local-file-access', true)
            ->loadView('crm.certificates', compact(['participant', 'umrohTrip']));
        $img->save(storage_path('app/' . $fileName));
        $storageKey = "/Certificate/{$fileName}";
        Storage::put($storageKey, fopen(storage_path('app/' . $fileName), 'r'));
        unlink(storage_path('app/' . $fileName));

        $expiredAt = now('UTC')->addDays(1);
        $params = [
            'Content-Disposition' => 'attachment',
            'ResponseExpires' => $expiredAt->format('D, d M Y H:i:s \G\M\T')
        ];

        $presignedUrl = Storage::temporaryUrl($storageKey, $expiredAt, $params);
        return $presignedUrl;
    }

    public function html()
    {
        $participant = $this->participant;
        $umrohTrip = UmrohTrip::findOrFail($this->umrohTripId);

        return view('crm.certificates', compact(['participant', 'umrohTrip']))->render();
    }
}
