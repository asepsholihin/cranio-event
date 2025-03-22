<?php

namespace App\File\Image;

use App\Models\Participant;
use App\Models\ParticipantFile;
use Illuminate\Support\Str;
use SnappyImage;
use File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MiladCardWithoutPhoto
{
    private $participant;
    private $title;
    private $photo;

    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
        $age = (new Carbon($participant->birth_date))->age;
        $this->title = "";
        if ($age >= 22) {
            $this->title = ($participant->gender == 1) ? "Bapak" : "Ibu";
        } else {
            $this->title = "Ananda";
        }
        $this->photo = ParticipantFile::where('participant_id', $participant->id)->where('title', "Foto Milad")->first()->file_path_url ?? '';
    }

    public function download()
    {
        $participant = $this->participant;
        $title = $this->title;
        $photo = $this->photo;

        //return view('crm.milad_card_2', compact(['participants','title','photo']))->render();
        $img = SnappyImage::setOption('width', 100)
            ->setOption('enable-local-file-access', true)
            ->loadView('crm.milad_card_2', compact(['participants', 'title', 'photo']));
        return $img->download("{$participant->name} Milad Card.jpg");
    }

    public function stream()
    {
        $participant = $this->participant;
        $title = $this->title;
        $photo = $this->photo;

        $fileName = $participant->name . "Milad Card.jpg";

        $img = SnappyImage::setOption('width', 100)
            ->setOption('enable-local-file-access', true)
            ->loadView('crm.milad_card_2', compact(['participants', 'title', 'photo']));
        $img->save(storage_path('app/' . $fileName));
        $storageKey = "web/Milad Card/{$fileName}";
        Storage::put($storageKey, fopen(storage_path('app/'.$fileName), 'r'));

        $this->participant->milad_card_url = $storageKey;
        $this->participant->save();
        return $this->participant->milad_card_url;
    }

    public function html()
    {
        $participant = $this->participant;
        $title = $this->title;
        $photo = $this->photo;
        return view('crm.milad_card_2',compact(['participants','title','photo']))->render();
    }
}
