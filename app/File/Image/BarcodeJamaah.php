<?php
namespace App\File\Image;

use App\Models\Participant;
use Illuminate\Support\Str;
use SnappyImage;
use File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BarcodeParticipant
{
    private $participant;

    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
        if ($this->participant->barcode == null) {
            $this->setBarcodeUuid();
        }

    }

    public function download()
    {
        $participant = $this->participant;
       // return view('barcode.participant', compact('participant'))->render();
        $img = SnappyImage::setOption('width', 100)->loadView('barcode.participant', compact('participant'));
        return $img->download("{$participant->name} Barcode.jpg");
    }

    public function stream()
    {
        $participant = $this->participant;
        $fileName = $participant->name . " Barcode" .Carbon::now()->timestamp. ".jpg";

        $img = SnappyImage::setOption('width', 100)->loadView('barcode.participant', compact('participant'));
        $img->save(storage_path('app/'.$fileName));
        $storageKey = "/Barcode/{$fileName}";
        Storage::put($storageKey, fopen(storage_path('app/'.$fileName), 'r'));
        unlink(storage_path('app/'.$fileName));

        $expiredAt = now('UTC')->addDays(1);
        $params = [
            'Content-Disposition' => 'attachment',
            'ResponseExpires' => $expiredAt->format('D, d M Y H:i:s \G\M\T')
        ];

        $presignedUrl = Storage::temporaryUrl($storageKey, $expiredAt, $params);
        return $presignedUrl;
    }

    public function streamPublic()
    {
        $participant = $this->participant;
        $fileName = Str::slug($participant->name, '_') . "_barcode_" .Carbon::now()->timestamp. ".jpg";

        $img = SnappyImage::setOption('width', 100)->loadView('barcode.participant', compact('participant'));
        $img->save(storage_path('app/'.$fileName));
        $storageKey = Participant::DIR_BARCODE . "/{$fileName}";
        Storage::put($storageKey, fopen(storage_path('app/'.$fileName), 'r'));
        
        $this->participant->barcode_thumbnail = $storageKey;
        $this->participant->save();

        return $this->participant->barcode_thumbnail;
    }
    
    private function setBarcodeUuid()
    {
        $this->participant->barcode = Str::uuid()->toString();
        $this->participant->save();
    }
}
