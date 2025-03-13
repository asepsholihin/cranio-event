<?php
namespace App\File\PDF;

use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use DB;

class DeliveryNameLabels
{
    private $pdf;
    private $equipmentDeliveries;
    private $data;

    public function __construct($umrohTripId, $equipmentDeliveries)
    {
        App::setLocale('id');
        $this->equipmentDeliveries = $equipmentDeliveries;
        $umrohTrip = DB::table('umroh_trips')->select('title')->whereIn('id', $umrohTripId)->get();
        $trips = '';
        foreach($umrohTrip as $key=>$trip){
            $lms = '';
            if($key < count($umrohTrip)){
                $lms = ',';
            }
            $trips .= $trip->title . $lms;
        }
        $data = [
            'umrohTrip' => $trips,
            'equipmentDeliveries' => $equipmentDeliveries
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.delivery_name_labels', $data);
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
        return view('pdf.delivery_name_labels', $this->data)->render();
    }
}
