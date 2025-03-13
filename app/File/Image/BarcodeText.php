<?php
namespace App\File\Image;

use App\Models\Participant;
use Illuminate\Support\Str;
use SnappyImage;
use File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BarcodeText
{
    private $text;

    public function __construct($text)
    {
        $this->text = $text;
    }

    public function download()
    {
        $text = $this->text;
        $img = SnappyImage::setOption('width', 100)->loadView('barcode.text', compact('text'));
        return $img->download("{$this->text}_Barcode.jpg");
    }

    public function stream()
    {
        $fileName = $this->text . "_Barcode" .Carbon::now()->timestamp. ".jpg";

        $img = SnappyImage::setOption('width', 100)->loadView('barcode.text', compact('text'));
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
}
