<?php
namespace App\File\PDF;

use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use DB;

class ShipmentLabelsDocument
{
    private $pdf;
    private $documentDeliveries;
    private $data;

    public function __construct($documentDeliveries)
    {
        App::setLocale('id');
        $this->documentDeliveries = $documentDeliveries;
        // echo json_encode($documentDeliveries);
        // exit;

        $data = [
            'documentDeliveries' => $documentDeliveries,
            'calenderYear' => date('Y', strtotime('+1 year'))
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.shipment_labels_document', $data);
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
        return view('pdf.shipment_labels_document', $this->data)->render();
    }
}
