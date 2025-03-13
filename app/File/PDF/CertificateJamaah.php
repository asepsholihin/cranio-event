<?php
namespace App\File\PDF;

use App\Models\Participant;
use App\Models\ParticipantUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\PackageUmrohTrip;
use App\Models\ParticipantLetterInformation;
use Illuminate\Support\Str;
use SnappyImage;
use PDF;
use File;
use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class CertificateParticipant
{
    private $umrohTrip;
    private $pdf;
    private $data;
    private $participant;

    public function __construct($participantId)
    {
        App::setLocale('id');

        $participantUmrohTrip = ParticipantUmrohTrip::where('participant_id', $participantId)->orderBy('id', 'DESC')->first();
        $umrohTrip = UmrohTrip::findOrFail($participantUmrohTrip->umroh_trip_id);
        $this->umrohTrip = $umrohTrip;
        $query = ParticipantUmrohTrip::
        join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')
        ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', '=', 'package_umroh_trips.id')
        ->join('umroh_trips', 'umroh_trips.id', '=', 'participant_umroh_trips.umroh_trip_id')
        ->where('participant_umroh_trips.id', $participantUmrohTrip->id);
        $participant = $query->select([
            'participant.title',
            'participant.front_title',
            'participant.back_title',
            'participant.name',
            'participant.name_in_passport',
            'participant.name_in_certificate',
            'package_umroh_trips.name as package_name',
            'umroh_trips.category_id',
            DB::raw('(CASE 
                WHEN package_umroh_trips.name iLIKE \'%Rub%\' THEN \'c\' 
                WHEN package_umroh_trips.name iLIKE \'%Emerald%\' THEN \'b\' 
                WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'a\' 
                ELSE \'c\' END
            ) AS package_type')])
        ->orderByRaw('package_type, booking_order_no, participant.name, room_type, group_hotel_room ASC NULLS LAST')
        ->limit(1)->get();
        
        $this->participant = $participant[0];
        $data = [
            'participants' => $participant,
            'umrohTrip' => $umrohTrip
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.certificates', $data);
        $this->pdf->setOption('enable-local-file-access', true)
        ->setOption('page-size', 'A4')
        ->setOption('margin-top', '0cm')
        ->setOption('margin-left', '0cm')
        ->setOption('margin-right', '0cm')
        ->setOption('margin-bottom', '0cm');
    }

    public function download()
    {   
        return $this->pdf->download("Sertifikat_".$this->participant->name."_".$this->umrohTrip->title.'.pdf');
    }

    public function output()
    {   
        return $this->pdf->output();
    }

    public function stream()
    {   
        return $this->pdf->stream();
    }

    public function html()
    {   
        return view('pdf.certificates', $this->data)->render();
    }
}
