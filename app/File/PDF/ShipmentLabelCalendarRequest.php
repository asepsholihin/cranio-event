<?php
namespace App\File\PDF;

use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use DB;

class ShipmentLabelCalendarRequest
{
    private $pdf;
    private $participant;
    private $data;

    public function __construct($participant)
    {
        App::setLocale('id');
        $this->participant = $participant;

        $departureYear = $participant->departure_year;
        if($participant->mitra == 'Ya' || $participant->haji_khusus == 'Ya') {
            $departureYear = '';
        }
        $typeParticipant = "Participant";
        if($participant->mitra == "Ya") {
            $typeParticipant = "Mitra";
        }
        if($participant->haji_khusus == "Ya") {
            $typeParticipant = "Haji Khusus";
        }

        $data = [
            'participants' => $participant,
            'calenderYear' => date('Y', strtotime('+1 year')),
            'departureYear' => $departureYear,
            'typeParticipant' => $typeParticipant
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.shipment_label_calendar_request', $data);
        $this->pdf->setOption('enable-local-file-access', true)
            ->setOrientation('landscape')
            ->setOption('page-width', '10cm')
            ->setOption('page-height', '10cm')
            ->setOption('margin-top', '0cm')
            ->setOption('margin-left', '0cm')
            ->setOption('margin-right', '0cm')
            ->setOption('margin-bottom', '0cm');
    }

    public function download()
    {
        return $this->pdf->download('SHIPMENT_'.strtoupper($this->participantUmrohTrip->name).'.pdf');
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
        return view('pdf.shipment_label_calendar_request', $this->data)->render();
    }
}
