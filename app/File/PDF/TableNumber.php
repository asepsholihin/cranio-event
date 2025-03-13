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

class TableNumber
{
    private $umrohTrip;
    private $data;
    private $pdf;

    public function __construct($umrohTripId)
    {
        App::setLocale('id');
        $umrohTrip = UmrohTrip::findOrFail($umrohTripId);
        $this->umrohTrip = $umrohTrip;
        $tables = ParticipantUmrohTrip::where('participant_umroh_trips.umroh_trip_id', $umrohTripId)
        ->where('manasik_table', '!=', null)
        ->whereIn('participant_umroh_trips.role_type', [1,4])
        ->select(['manasik_table'])
        ->groupBy('manasik_table')
        ->orderBy('manasik_table', 'asc')
        ->get();

        foreach($tables as $table) {
            $table->participants = ParticipantUmrohTrip::
            join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')
            ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', '=', 'package_umroh_trips.id')
            ->join('umroh_trips', 'umroh_trips.id', '=', 'participant_umroh_trips.umroh_trip_id')
            ->leftJoin('participant as crew', 'umroh_trips.tour_leader', '=', 'crew.id')
            ->where('participant_umroh_trips.umroh_trip_id', $umrohTripId)
            ->where('participant_umroh_trips.manasik_table', $table->manasik_table)
            ->whereIn('participant_umroh_trips.role_type', [1,4])
            ->select([
                'participant.title',
                'participant.name',
                'participant.name_in_passport',
                'package_umroh_trips.name as package_name',
                'package_umroh_trips.package_id as package_id',
                DB::raw('(CASE 
                    WHEN package_umroh_trips.name iLIKE \'%Rub%\' THEN \'c\' 
                    WHEN package_umroh_trips.name iLIKE \'%Emerald%\' THEN \'b\' 
                    WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'a\' 
                    ELSE \'c\' END
                ) AS package_type')])
            ->orderByRaw('crew, package_type, booking_order_no, participant.name, room_type, group_hotel_room ASC NULLS LAST')
            ->get();
            $package_id = $table->participants[0]->package_id;
            $table->package_id = $package_id;
        }
        $data = [
            'tables' => $tables,
            'umrohTrip' => $umrohTrip
        ];
        $this->data = $data;

        // return view('pdf.table_numbers', $data)->render();
        $this->pdf = PDF::loadView('pdf.table_numbers', $data);
        $this->pdf->setOption('enable-local-file-access', true)
        ->setOption('margin-top','0.4cm')
        ->setOption('margin-bottom','0.4cm')
        ->setOption('orientation', 'Landscape');
    }

    public function download()
    {
        return $this->pdf->download("Table_Number_".$this->umrohTrip->title.'.pdf');
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
        return view('pdf.table_numbers', $this->data)->render();
    }
}
