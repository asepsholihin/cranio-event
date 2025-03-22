<?php
namespace App\File\PDF;

use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use DB;

class ShipmentLabel
{
    private $pdf;
    private $participantUmrohTrip;
    private $data;

    public function __construct($participantUmrohTrip)
    {
        App::setLocale('id');
        $this->participantUmrohTrip = $participantUmrohTrip;
        $umrohTrip = DB::table('umroh_trips')->select('title','departure_at')->where('id', $participantUmrohTrip->umroh_trip_id)->first();
        
        $getOrder = DB::table('participant_umroh_trips')->select('order_umroh_trip_id','umroh_trip_id')
        ->where('participant_id', $participantUmrohTrip->participant_id)
        ->where('umroh_trip_id', $participantUmrohTrip->umroh_trip_id)->first();

        $participantInAccount = DB::table('participants')
        ->select(['participant_umroh_trips.participant_id', 'participant.home_postalcode', 'participant.name'])
        ->join('participant_umroh_trips', 'participant_umroh_trips.participant_id', 'participant.id')
        ->where('participant_umroh_trips.order_umroh_trip_id', $getOrder->order_umroh_trip_id)
        ->where('participant.home_address', $participantUmrohTrip->address)
        ->orderBy('participant_umroh_trips.created_at', 'ASC')->get();

        $totalPax = ($participantInAccount) ? count($participantInAccount) : 1;

        $index = 1;
        foreach ($participantInAccount as $key => $row) {
            if($row->participant_id == $participantUmrohTrip->participant_id) {
                $participantUmrohTrip->recipient = $participantInAccount[0]->name ?? $participantUmrohTrip->name;
                $index = $key + 1;
            }
        }
        if($participantInAccount) {
            $indexOf = $index . " of " . $totalPax;
        } else {
            $indexOf = $index;
        }
        
        $data = [
            'umrohTrip' => $umrohTrip,
            'index' => $indexOf,
            'participants' => $participantUmrohTrip
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.shipment_label', $data);
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
        return view('pdf.shipment_label', $this->data)->render();
    }
}
