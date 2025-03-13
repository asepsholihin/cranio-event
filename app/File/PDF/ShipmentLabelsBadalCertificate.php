<?php
namespace App\File\PDF;

use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use DB;

class ShipmentLabelsBadalCertificate
{
    private $pdf;
    private $badals;
    private $data;

    public function __construct($badals)
    {
        App::setLocale('id');
        $this->badals = $badals;
        foreach ($badals as $key => $value) {
            
        }

        $data = [
            'badals' => $badals
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.shipment_labels_badal_certificate', $data);
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
        return $this->pdf->download('SHIPMENT_'.date('dmy').'.pdf');
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
        return view('pdf.shipment_labels_badal_certificate', $this->data)->render();
    }
}
