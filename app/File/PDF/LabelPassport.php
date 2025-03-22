<?php
namespace App\File\PDF;

use App\Models\ParticipantUmrohTrip;
use App\Models\UmrohTrip;
use Illuminate\Support\Str;
use SnappyImage;
use PDF;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class LabelPassport
{
    private $umrohTrip;
    private $data;
    private $pdf;

    public function __construct($umrohTripId)
    {
        App::setLocale('id');
        $umrohTrip = UmrohTrip::findOrFail($umrohTripId);
        $this->umrohTrip = $umrohTrip;
        $participants = ParticipantUmrohTrip::where('participant_umroh_trips.umroh_trip_id', $umrohTripId)
        ->join('participants', 'participant_umroh_trips.participant_id', '=', 'participant.id')
        ->select(['participant.name','participant.name_in_passport','participant_umroh_trips.no_urut', 'participant.gender'])
        ->orderBy('no_urut', 'asc')
        ->get();
        
        $data = [
            'participants' => $participants,
            'umrohTrip' => $umrohTrip
        ];
        $this->data = $data;

        // return view('pdf.label_passports', $data)->render();
        $this->pdf = PDF::loadView('pdf.label_passports', $data);
        $this->pdf->setOption('enable-local-file-access', true)
        ->setOption('encoding', 'utf-8')
        ->setOption('margin-top', '1.5cm')
        ->setOption('margin-left', '1.5cm')
        ->setOption('margin-right', '1.5cm')
        ->setOption('margin-bottom', '1.5cm');
    }

    public function download()
    {
        return $this->pdf->download("LABEL_NAME_PASSPORT_".$this->umrohTrip->title.'.pdf');
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
        return view('pdf.label_passports', $this->data)->render();
    }
}
