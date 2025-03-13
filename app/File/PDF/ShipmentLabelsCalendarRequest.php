<?php
namespace App\File\PDF;

use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use DB;

class ShipmentLabelsCalendarRequest
{
    private $pdf;
    private $calendarRequests;
    private $data;

    public function __construct($calendarRequests)
    {
        App::setLocale('id');
        $this->calendarRequests = $calendarRequests;
        foreach ($calendarRequests as $key => $value) {
            $value->departure_year = $value->departure_year;
            $value->type_participant = "Participant";
            if($value->mitra == "Ya" || $value->haji_khusus == "Ya") {
                $value->departure_year = '';
            }
            if($value->mitra == "Ya") {
                $value->type_participant = "Mitra";
            }
            if($value->haji_khusus == "Ya") {
                $value->type_participant = "Haji Khusus";
            }
        }

        $data = [
            'calendarRequests' => $calendarRequests,
            'calenderYear' => date('Y', strtotime('+1 year'))
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.shipment_labels_calendar_request', $data);
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
        return $this->pdf->download('SHIPMENT_'.strtoupper($this->participantUmrohTrips->name).'.pdf');
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
        return view('pdf.shipment_labels_calendar_request', $this->data)->render();
    }
}
