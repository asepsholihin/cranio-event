<?php
namespace App\File\PDF;

use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use DB;

class ShipmentLabels
{
    private $pdf;
    private $participantUmrohTrips;
    private $data;

    public function __construct($umrohTripId, $participantUmrohTrips)
    {
        App::setLocale('id');
        $this->participantUmrohTrips = $participantUmrohTrips;
        $umrohTrip = DB::table('umroh_trips')->select('title')->whereIn('id', $umrohTripId)->get();
        foreach ($participantUmrohTrips as $participant) {
            $getOrder = DB::table('participant_umroh_trips')->select('order_umroh_trip_id','umroh_trip_id')
            ->where('participant_id', $participant->participant_id)
            ->where('umroh_trip_id', $participant->umroh_trip_id)->first();

            $participantInAccount = DB::table('participants')
            ->select(['participant_umroh_trips.participant_id', 'participant.home_address', 'participant.name'])
            ->join('participant_umroh_trips', 'participant_umroh_trips.participant_id', 'participant.id')
            ->leftjoin('equipment_deliveries', 'equipment_deliveries.participant_id', 'participant.id')
            ->where('participant_umroh_trips.order_umroh_trip_id', $getOrder->order_umroh_trip_id)
            ->where('participant.home_address', $participant->address)
            ->where(function($q) {
                $q->where('equipment_deliveries.delivery_status', '!=', 3);
                $q->orWhere('equipment_deliveries.delivery_status', NULL);
            })
            ->orderByRaw('participant.gender ASC, participant_umroh_trips.created_at ASC')->get();
            $totalPax = ($participantInAccount) ? count($participantInAccount) : 1;

            $index = 1;
            foreach ($participantInAccount as $key => $row) {
                if($row->participant_id == $participant->participant_id && $row->home_address == $participant->address) {
                    $participant->recipient = $participantInAccount[0]->name ?? $participant->name;
                    $index = $key + 1;
                }
            }
            $participant->index = $index . " of " . $totalPax;
        }
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
            'participantUmrohTrips' => $participantUmrohTrips
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.shipment_labels', $data);
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
        return view('pdf.shipment_labels', $this->data)->render();
    }
}
