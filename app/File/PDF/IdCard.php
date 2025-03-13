<?php
namespace App\File\PDF;

use App\Models\ParticipantUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\HotelUmrohTrip;
use App\Models\PackageUmrohTrip;
use Illuminate\Support\Str;
use SnappyImage;
use PDF;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class IdCard
{
    private $umrohTrip;
    private $pdf;
    private $data;

    public function __construct($umrohTripId, $type, $bus=null)
    {
        App::setLocale('id');

        $umrohTrip = UmrohTrip::findOrFail($umrohTripId);
        $this->umrohTrip = $umrohTrip;
        $query = ParticipantUmrohTrip::
        join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')
        ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', '=', 'package_umroh_trips.id')
        ->join('umroh_trips', 'umroh_trips.id', '=', 'participant_umroh_trips.umroh_trip_id')
        ->leftJoin('participant as tour_leader', 'umroh_trips.tour_leader', '=', 'tour_leader.id')
        ->where('participant_umroh_trips.umroh_trip_id', $umrohTripId);
        if($type) {
            $query->where('package_umroh_trips.name', $type);
        }
        if($bus) {
            $query->where('participant_umroh_trips.group_bus', $bus);
        }
        
        $query->select([
            'participant_umroh_trips.id',
            'participant.profile_photo_path',
            'participant.name',
            'participant.name_in_passport',
            'participant.no_passport', 
            'participant_umroh_trips.siskopatuh_id', 
            'participant_umroh_trips.code_siskopatuh', 
            'participant.no_hp', 
            'package_umroh_trips.name as package_name',
            'package_umroh_trips.hotel_makkah',
            'package_umroh_trips.hotel_madinah',
            'participant_umroh_trips.group_bus',
            'tour_leader.name as tour_leader',
            'tour_leader.no_hp as tour_leader_phone',
            DB::raw('
            (CASE 
                WHEN participant_umroh_trips.role_type = 2 THEN \'Tour Leader\'
                WHEN participant_umroh_trips.role_type = 3 THEN \'Mutawwif\'
                ELSE \'\' END) AS crew'
            ),
            DB::raw('
            (CASE 
                WHEN participant_umroh_trips.role_type = 2 THEN \'a\' 
                WHEN participant_umroh_trips.role_type = 3 THEN \'b\' 
                ELSE \'z\' END) AS tour_crew'
            )
        ]);
        $participant = $query->orderByRaw('group_bus,tour_crew ASC NULLS LAST')->get();

        foreach ($participant as $row) {
            $jumlahMutawwif = ParticipantUmrohTrip::select('participant.name','participant.no_hp')->join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')->where('role_type', 3)->where('umroh_trip_id', $umrohTripId)->get();
            $jumlahRunner = ParticipantUmrohTrip::select('participant.name','participant.no_hp')->join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')->where('role_type', 5)->where('umroh_trip_id', $umrohTripId)->get();
            
            if(count($jumlahMutawwif) == 1) {
                $mutawwif = $jumlahMutawwif[0];
                $row->mutawwif = $mutawwif->name ?? '';
                $row->mutawwif_phone = $mutawwif->no_hp ?? '';
            } else {
                $mutawwif = ParticipantUmrohTrip::select('participant.name','participant.no_hp')->join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')->where('role_type', 3)->where('umroh_trip_id', $umrohTripId)->where('group_bus', $row->group_bus)->first();
                $row->mutawwif = $mutawwif->name ?? '';
                $row->mutawwif_phone = $mutawwif->no_hp ?? '';
            }

            if(count($jumlahRunner) == 1) {
                $runner = $jumlahRunner[0];
                $row->runner = $runner->name ?? '';
                $row->runner_phone = $runner->no_hp ?? '';
            } else {
                $runner = ParticipantUmrohTrip::select('participant.name','participant.no_hp')->join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')->where('role_type', 5)->where('umroh_trip_id', $umrohTripId)->where('group_bus', $row->group_bus)->first();
                $row->runner = $runner->name ?? '';
                $row->runner_phone = $runner->no_hp ?? '';
            }
        }

        $hotels = HotelUmrohTrip::select('city_name','hotel_name')->where('umroh_trip_id', $umrohTripId)->where('hotel_name', "!=", "-")->orderBy('check_in', 'ASC')->get();
        
        $data = [
            'participants' => $participant,
            'umrohTrip' => $umrohTrip,
            'hotels' => $hotels
        ];
        $this->data = $data;

        if($umrohTrip->category_id == 3) {
            $this->pdf = PDF::loadView('pdf.id_card_tour', $data);
        } else {
            $this->pdf = PDF::loadView('pdf.id_card_siskopatuh', $data);   
        }
        $this->pdf->setOption('enable-local-file-access', true)
        ->setOption('encoding', 'utf-8')
        ->setOption('orientation', 'Landscape');
    }

    public function download()
    {
        return $this->pdf->download("ID_Card_Umroh_".$this->umrohTrip->title.'.pdf');
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
        if($this->umrohTrip->category_id == 3) { 
            return view('pdf.id_card_tour', $this->data)->render();
        } else { 
            return view('pdf.id_card_siskopatuh', $this->data)->render();
        }
    }
}
