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

class AttendanceTag
{
    private $umrohTrip;
    private $pdf;
    private $data;

    public function __construct($umrohTripId)
    {
        App::setLocale('id');

        $umrohTrip = UmrohTrip::findOrFail($umrohTripId);
        $this->umrohTrip = $umrohTrip;
        $query = ParticipantUmrohTrip::join('event_attendances', 'event_attendances.umroh_trip_id', '=', 'participant_umroh_trips.umroh_trip_id')
        ->join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')
        ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', '=', 'package_umroh_trips.id')
        ->join('umroh_trips', 'umroh_trips.id', '=', 'participant_umroh_trips.umroh_trip_id')
        ->where('participant_umroh_trips.umroh_trip_id', $umrohTripId)
        ->whereNull('event_attendances.deleted_at');
        $query->select([
            'participant.name',
            'participant.name_in_passport',
            'participant.barcode',
            'participant_umroh_trips.no_urut', 
            'participant_umroh_trips.manasik_table',
            'event_attendances.name as event_name',
            'event_attendances.event_date',
            'event_attendances.location',
            DB::raw('(CASE WHEN participant_umroh_trips.role_type = 1 THEN \'z\' ELSE \'a\' END) AS crew'),
            DB::raw('(CASE 
                WHEN package_umroh_trips.name iLIKE \'%Rub%\' THEN \'c\' 
                WHEN package_umroh_trips.name iLIKE \'%Emerald%\' THEN \'b\' 
                WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'a\' 
                ELSE \'c\' END
            ) AS package_type')]);
        $query->orderByRaw('no_urut, crew, package_type, booking_order_no, participant.name, room_type, group_hotel_room ASC NULLS LAST');
        $participant = $query->get();
        
        $data = [
            'participants' => $participant,
            'umrohTrip' => $umrohTrip
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.attendance_tag', $data);
        $this->pdf->setOption('enable-local-file-access', true)
        ->setOption('margin-top','0.4cm')
        ->setOption('margin-bottom','0.4cm');
    }

    public function download()
    {
        return $this->pdf->download("Attendance_Tag_".$this->umrohTrip->title.'.pdf');
    }

    public function output()
    {
        return $this->pdf->output();
    }

    public function html()
    {
        return view('pdf.attendance_tag', $this->data)->render();
    }
}
