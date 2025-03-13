<?php
namespace App\File\PDF;

use App\Models\ParticipantUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\HotelUmrohTrip;
use Illuminate\Support\Str;
use SnappyImage;
use PDF;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class KoperTag
{
    private $umrohTrip;
    private $pdf;
    private $data;

    public function __construct($umrohTripId)
    {
        App::setLocale('id');
        
        $umrohTrip = UmrohTrip::findOrFail($umrohTripId);
        $this->umrohTrip = $umrohTrip;
        $participant = ParticipantUmrohTrip::
        join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')
        ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', '=', 'package_umroh_trips.id')
        ->join('umroh_trips', 'umroh_trips.id', '=', 'participant_umroh_trips.umroh_trip_id')
        ->leftJoin('participant as tour_leader', 'umroh_trips.tour_leader', '=', 'tour_leader.id')
        ->where('participant_umroh_trips.umroh_trip_id', $umrohTripId)
        ->whereIn('participant_umroh_trips.role_type', [1,2,4])
        ->select([
            'participant_umroh_trips.id',
            'participant.profile_photo_path',
            'participant.name',
            'participant.name_in_passport',
            'participant.no_passport', 
            'participant.no_hp', 
            'participant_umroh_trips.no_urut',
            'package_umroh_trips.package_id',
            'package_umroh_trips.name as package_name',
            'package_umroh_trips.hotel_makkah',
            'package_umroh_trips.hotel_madinah',
            'tour_leader.name as tour_leader',
            'tour_leader.no_hp as tour_leader_phone',
            'participant_umroh_trips.group_bus',
            DB::raw('(CASE WHEN participant_umroh_trips.role_type = 1 THEN \'z\' ELSE \'a\' END) AS crew'),
            DB::raw('(CASE 
                WHEN package_umroh_trips.name iLIKE \'%Rub%\' THEN \'c\' 
                WHEN package_umroh_trips.name iLIKE \'%Emerald%\' THEN \'b\' 
                WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'a\' 
                ELSE \'c\' END
            ) AS package_type'),])
        ->orderByRaw('no_urut, crew, package_type, booking_order_no, participant.name, room_type, group_hotel_room ASC NULLS LAST')
        ->get();

        foreach ($participant as $row) {
            $jumlahMutawwif = ParticipantUmrohTrip::select('participant.name','participant.no_hp')->join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')->where('role_type', 3)->where('umroh_trip_id', $umrohTripId)->get();
            if(count($jumlahMutawwif) == 1) {
                $mutawwif = $jumlahMutawwif[0];
                $row->mutawwif = $mutawwif->name ?? '';
            } else {
                $mutawwif = ParticipantUmrohTrip::select('participant.name','participant.no_hp')->join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')->where('role_type', 3)->where('umroh_trip_id', $umrohTripId)->where('group_bus', $row->group_bus)->first();
                $row->mutawwif = $mutawwif->name ?? '';
            }
        }

        $hotels = HotelUmrohTrip::select('city_name','hotel_name','pic_name','pic_phone')->where('umroh_trip_id', $umrohTripId)->where('hotel_name', "!=", "-")->orderBy('check_in', 'ASC')->get();
        
        $data = [
            'participants' => $participant,
            'umrohTrip' => $umrohTrip,
            'hotels' => $hotels
        ];
        $this->data = $data;
        if($umrohTrip->category_id == 3) {
            $this->pdf = PDF::loadView('pdf.koper_tag_tour', $data);
        } else {
            $this->pdf = PDF::loadView('pdf.koper_tag', $data); 
        }
        $this->pdf->setOption('enable-local-file-access', true)
        ->setOption('encoding', 'utf-8')
        ->setOption('orientation', 'Landscape');
    }

    public function download()
    {
        return $this->pdf->download("Koper_Tag_".$this->umrohTrip->title.'.pdf');
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
            return view('pdf.koper_tag_tour', $this->data)->render();
        } else { 
            return view('pdf.koper_tag', $this->data)->render();
        }
    }
}
