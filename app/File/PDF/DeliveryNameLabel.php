<?php
namespace App\File\PDF;

use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use DB;

class DeliveryNameLabel
{
    private $pdf;
    private $equipmentDelivery;
    private $data;

    public function __construct($equipmentDelivery)
    {
        App::setLocale('id');
        $this->equipmentDelivery = $equipmentDelivery;
        $data = [
            'equipmentDelivery' => $equipmentDelivery
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.delivery_name_label', $data);
        $this->pdf->setOption('enable-local-file-access', true)
            ->setOption('page-width', '8cm')
            ->setOption('page-height', '5cm')
            ->setOption('margin-top', '0cm')
            ->setOption('margin-left', '0cm')
            ->setOption('margin-right', '0cm')
            ->setOption('margin-bottom', '0cm');
    }

    public function download()
    {
        return $this->pdf->download('LABEL_'.strtoupper($this->deliveryLog->name).'.pdf');
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
        return view('pdf.delivery_name_label', $this->data)->render();
    }
}
