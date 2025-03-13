<?php
namespace App\File\Image;

use App\Models\Participant;
use Illuminate\Support\Str;
use SnappyImage;
use File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DB;

class BarcodeEventParticipant
{
    private $participant;
    private $event;

    public function __construct(Participant $participant, $event)
    {
        $this->participant = $participant;
        $this->event = $event;
        if ($this->participant->barcode == null) {
            $this->setBarcodeUuid();
        }
    }

    public function streamPublic()
    {
        $participant = $this->participant;
        $event = $this->event;

        $seat_name = DB::table('participant_umroh_trips')->where('participant_id', $participant->id)->where('umroh_trip_id', $event->umroh_trip_id)->first()->manasik_table ?? '-';
        $fileName = Str::slug($participant->name, '_') . "_barcode_" .Carbon::now()->timestamp. ".jpg";

        $img = SnappyImage::setOption('width', 100)->loadView('barcode.event_participant', compact(['participant', 'event', 'seat_name']));
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
