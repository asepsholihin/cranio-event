<?php
namespace App\File\PDF;

use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use DB;

class ShipmentLabelsAdditional
{
    private $pdf;
    private $additionalDeliveries;
    private $data;

    public function __construct($umrohTripId, $additionalDeliveries)
    {
        App::setLocale('id');
        $this->additionalDeliveries = $additionalDeliveries;
        $umrohTrip = DB::table('umroh_trips')->select('title','departure_at')->find($umrohTripId);
        foreach ($additionalDeliveries as $additionalDelivery) {
            $getOrder = DB::table('participant_umroh_trips')->select('order_umroh_trip_id','umroh_trip_id')
            ->where('participant_id', $additionalDelivery->participant_id)
            ->where('umroh_trip_id', $additionalDelivery->umroh_trip_id)->first();

            $participantInAccount = DB::table('participant')
            ->select(['participant_umroh_trips.participant_id', 'participant.home_postalcode'])
            ->join('participant_umroh_trips', 'participant_umroh_trips.participant_id', 'participant.id')
            ->where('participant_umroh_trips.order_umroh_trip_id', $getOrder->order_umroh_trip_id)
            ->where('participant.home_postalcode', $additionalDelivery->home_postalcode)
            ->orderBy('participant_umroh_trips.created_at', 'ASC')->get();

            $totalPax = ($participantInAccount) ? count($participantInAccount) : 1;

            $index = 1;
            foreach ($participantInAccount as $key => $row) {
                if($row->participant_id == $additionalDelivery->participant_id && $row->home_postalcode == $additionalDelivery->home_postalcode) {
                    $index = $key + 1;
                }
            }
            $additionalDelivery->index = $index . " of " . $totalPax;
        }
        $data = [
            'umrohTrip' => $umrohTrip,
            'additionalDeliveries' => $additionalDeliveries
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.shipment_labels_additional', $data);
        $this->pdf->setOption('enable-local-file-access', true)
            ->setOrientation('landscape')
            ->setOption('page-width', '21cm')
            ->setOption('page-height', '29.7cm')
            ->setOption('margin-top', '1cm')
            ->setOption('margin-left', '1cm')
            ->setOption('margin-right', '1cm')
            ->setOption('margin-bottom', '1cm');
    }

    public function download()
    {
        return $this->pdf->download('SHIPMENT_'.strtoupper($this->deliveryLog->name).'.pdf');
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
        return view('pdf.shipment_labels', $this->data)->render();
    }
}
