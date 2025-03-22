<?php
namespace App\File\PDF;

use App\Models\ParticipantUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\PackageUmrohTrip;
use App\Models\Package;
use Illuminate\Support\Str;
use SnappyImage;
use PDF;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class LuggageTag
{
    private $umrohTrip;
    private $pdf;
    private $data;

    public function __construct($umrohTripId, $type)
    {
        App::setLocale('id');

        $umrohTrip = UmrohTrip::findOrFail($umrohTripId);
        $this->umrohTrip = $umrohTrip;
        $packageUmrohTrip = PackageUmrohTrip::where('umroh_trip_id', $umrohTripId)->where('name', $type)->first();
        $package = Package::find($packageUmrohTrip->package_id);
        
        $query = ParticipantUmrohTrip::
        join('participants', 'participant_umroh_trips.participant_id', '=', 'participant.id')
        ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', '=', 'package_umroh_trips.id')
        ->join('umroh_trips', 'umroh_trips.id', '=', 'participant_umroh_trips.umroh_trip_id')
        ->leftJoin('participant as crew', 'umroh_trips.tour_leader', '=', 'crew.id')
        ->where('participant_umroh_trips.umroh_trip_id', $umrohTripId)
        ->whereIn('participant_umroh_trips.role_type', [1,2,4]);
        $query->where('package_umroh_trip_id', $packageUmrohTrip->id);
        $query->select([
            'participant_umroh_trips.id',
            'participant.profile_photo_path',
            'participant.name',
            'participant.name_in_passport',
            'participant.no_passport', 
            'package_umroh_trips.name as package_name',
            'package_umroh_trips.hotel_makkah',
            'package_umroh_trips.hotel_madinah',
            'participant_umroh_trips.no_urut',
            'participant_umroh_trips.room_type',
        ]);
        $query->orderByRaw('no_urut, participant.name ASC NULLS LAST');
        $participant = $query->get();
        foreach ($participant as $value) {
            // $value->group_hotel_room = DB::table('room_umroh_trips')->where('participant_umroh_trip_id', $value->id)->first()->group_room ?? '';
            $value->group_hotel_room = DB::table('room_umroh_trips')->where('participant_umroh_trip_id', $value->id)->where('hotel_name', $value->hotel_makkah_selected)->first()->group_room ?? '';
        }

        $documentStyle = $package->document_style ?? 'lebih_hemat';
        
        $data = [
            'participants' => $participant,
            'umrohTrip' => $umrohTrip,
            'type' => $type,
            'documentStyle' => $documentStyle
        ];
        $this->data = $data;

        if($umrohTrip->category_id == 3) {
            $this->pdf = PDF::loadView('pdf.luggage_tag_tour', $data);
        } else {
            $this->pdf = PDF::loadView('pdf.luggage_tag', $data);
        }
        // if(!str_contains(strtolower($documentStyle), 'sapphire') && !str_contains(strtolower($documentStyle), 'emerald')) {
        //     $this->pdf->setOption('orientation', 'Landscape');
        // }
        $this->pdf->setOption('encoding', 'utf-8')->setOption('enable-local-file-access', true);
    }

    public function download()
    {
        return $this->pdf->download("Luggage_Tag_".$this->umrohTrip->title.'.pdf');
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
            return view('pdf.luggage_tag_tour', $this->data)->render();
        } else {
            return view('pdf.luggage_tag', $this->data)->render();
        }
    }
}
