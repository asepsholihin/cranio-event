<?php

namespace App\File\Image;

use App\Models\Participant;
use App\Models\ParticipantUmrohTrip;
use App\Models\UmrohTrip;
use Illuminate\Support\Str;
use SnappyImage;
use File;
use Illuminate\Support\Facades\Storage;

class AttendanceTagImage
{
    private $participant;
    private $data;

    public function __construct($participant, $umrohTripId)
    {
        $this->participant = $participant;
        $umrohTripId = UmrohTrip::findOrFail($umrohTripId);
        $this->data = [
            'participant' => $participant,
            'umrohTrip' => $umrohTripId
        ];
    }

    public function download()
    {
        $participant = $this->participant;
        // return view('image.attendance-tag', $this->data)->render();
        $img = SnappyImage::setOption('width', 100)->loadView('image.attendance-tag', $this->data);
        return $img->download("{$participant->name} Attendance Tag.jpg");
    }

    public function stream()
    {
        $participant = $this->participant;
        $fileName = $participant->name . " Attendace Tag.jpg";

        $img = SnappyImage::setOption('width', 100)->loadView('image.attendance-tag', $this->data);
        $img->save(storage_path('app/' . $fileName));
        $storageKey = "/Attendance-Tag/{$fileName}";
        Storage::put($storageKey, fopen(storage_path('app/' . $fileName), 'r'));
        unlink(storage_path('app/' . $fileName));

        $expiredAt = now('UTC')->addDays(1);
        $params = [
            'Content-Disposition' => 'attachment',
            'ResponseExpires' => $expiredAt->format('D, d M Y H:i:s \G\M\T'),
        ];

        $presignedUrl = Storage::temporaryUrl($storageKey, $expiredAt, $params);
        return $presignedUrl;
    }

    public function output()
    {
        $participant = $this->participant;
        $fileName = $participant->name . "-Attendace Tag.jpg";
        if (file_exists(storage_path('app/' . $fileName))) {
            unlink(storage_path('app/' . $fileName));
        }
        $img = SnappyImage::setOption('width', 100)->loadView('image.attendance-tag', $this->data);
        $img->save(storage_path('app/' . $fileName));
        return storage_path('app/' . $fileName);
    }
}
