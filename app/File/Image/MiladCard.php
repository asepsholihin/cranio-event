<?php
namespace App\File\Image;

use App\Models\Participant;
use App\Models\ParticipantFile;
use Illuminate\Support\Str;
use SnappyImage;
use File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MiladCard
{
    private $participant;
    private $title;
    private $photo;
    private $withPhoto;
    private $editing;

    public function __construct(Participant $participant, $withPhoto, $image="")
    {
        $this->participant = $participant;

        $age = (new Carbon($participant->birth_date))->age;
        $this->title = "";
        if($age >= 22) {
            $this->title = ($participant->gender == 1) ? "Bapak" : "Ibu";
        } else {
            $this->title = "Ananda";
        }
        $this->photo = ParticipantFile::where('participant_id', $participant->id)->where('title', "Foto Milad")->first()->file_path_url ?? '';
        $this->editing = false;
        if($image!=""){
            $this->photo = $image;
            $this->editing = true;
        }

        $this->withPhoto = false;
        if($withPhoto) {
            if($this->photo) {
                $this->withPhoto = true;
            }
        }
    }

    public function download()
    {
        $participant = $this->participant;
        $title = $this->title;
        $photo = $this->photo;
        $withPhoto = $this->withPhoto;
        $editing = $this->editing;

        //return view('crm.milad_card', compact(['participant','title','photo']))->render();
        $img = SnappyImage::
        setOption('width', 100)
        ->setOption('enable-local-file-access', true)
        ->loadView('crm.milad_card', compact(['participant','title','photo','withPhoto', 'editing']));
        return $img->download("{$participant->name} Milad Card.jpg");
    }

    public function stream()
    {
        $participant = $this->participant;
        $title = $this->title;
        $photo = $this->photo;
        $withPhoto = $this->withPhoto;
        $editing = $this->editing;

        $fileName = $participant->name . " Milad Card.jpg";

        $img = SnappyImage::
        setOption('width', 100)
        ->setOption('enable-local-file-access', true)
        ->loadView('crm.milad_card', compact(['participant','title','photo','withPhoto', 'editing']));
        return $img->stream();
    }

    public function streamPublic()
    {
        $participant = $this->participant;
        $title = $this->title;
        $photo = $this->photo;
        $withPhoto = $this->withPhoto;
        $editing = $this->editing;

        $fileName = "Milad_Card_" . str_replace(" ", "_", $this->participant->name).".jpg";

        if(!$this->participant->milad_card_url) {
            $img = SnappyImage::
            setOption('width', 100)
            ->setOption('enable-local-file-access', true)
            ->loadView('crm.milad_card', compact(['participant','title','photo','withPhoto', 'editing']));
            $img->save(storage_path('app/'.$fileName));
            $storageKey = "web/Milad_Card/{$fileName}";
            Storage::put($storageKey, fopen(storage_path('app/'.$fileName), 'r'));
            $this->participant->milad_card_url = $storageKey;
            $this->participant->save();
        }

        return $this->participant->milad_card_url;
    }

    public function html()
    {
        $participant = $this->participant;
        $title = $this->title;
        $photo = $this->photo;
        $editing = $this->editing;
        return view('crm.milad_card', compact(['participant', 'title', 'photo', 'editing']))->render();
    }
}
