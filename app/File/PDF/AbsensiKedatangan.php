<?php
namespace App\File\PDF;

use App\Models\ParticipantUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\PackageUmrohTrip;
use Illuminate\Support\Str;
use SnappyImage;
use PDF;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiKedatangan
{
    private $umrohTrip;
    private $pdf;
    private $data;

    public function __construct($umrohTripId)
    {
        App::setLocale('id');

        $umrohTrip = UmrohTrip::findOrFail($umrohTripId);
        $this->umrohTrip = $umrohTrip;
        $packages = PackageUmrohTrip::where('umroh_trip_id', $umrohTripId)->get();
        $query = ParticipantUmrohTrip::
        join('participant', 'participant.id', 'participant_umroh_trips.participant_id')
        ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
        ->select([
            'participant.title',
            'participant.name',
            'participant.name_in_passport',
            'participant.no_passport',
            'participant_umroh_trips.is_reference',
            'participant_umroh_trips.no_urut',
            'participant_umroh_trips.group_bus',
            'participant_umroh_trips.booking_order_no',
            'package_umroh_trips.name as package_name',
            DB::raw("(SELECT number_of_participant FROM participant_albums WHERE participant_albums.participant_reference_id=participant_umroh_trips.id) as rowspan"),
            DB::raw("(SELECT album_qty FROM participant_albums WHERE participant_albums.participant_reference_id=participant_umroh_trips.id) as album_qty"),
            DB::raw("(SELECT id FROM participant_albums WHERE participant_albums.order_umroh_trip_id=participant_umroh_trips.order_umroh_trip_id) as has_rowspan")
        ])
        ->whereIn('participant_umroh_trips.role_type', array(1,2,4))
        ->where('participant_umroh_trips.umroh_trip_id', $umrohTripId);
        if (!empty(request()->query('package'))) {
            $query->where('package_umroh_trips.id', request()->query('package'));
        }
        if (!empty(request()->query('gender'))) {
            $query->where('participant.gender', request()->query('gender'));
        }
        if (!empty(request()->query('booking'))) {
            $query->where('participant_umroh_trips.order_umroh_trip_id', request()->query('booking'));
        }
        if (!empty(request()->query('packing_date'))) {
            $query->whereDate('equipment_deliveries.created_at', request()->query('packing_date'));
        }
        if (!empty(request()->query('self_pickup'))) {
            $query->where('equipment_deliveries.self_pickup', request()->query('self_pickup'));
        }
        if (!empty(request()->query('selected'))) {
            $query->whereIn('participant_umroh_trips.id', explode(",", request()->query('selected')));
        }
        $participants = $query->orderByRaw('participant_umroh_trips.no_urut ASC, participant_umroh_trips.order_umroh_trip_id ASC')->get();

        $totalAlbum = 0;
        foreach ($participants as $value) {
            $totalAlbum += $value->album_qty;
        }

        $data = [
            'umrohTrip' => $umrohTrip,
            'participants' => $participants,
            'totalAlbum' => $totalAlbum,
            'event' => (object) array('name' => "Kedatangan Participant " . $umrohTrip->title . " - " . Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y'))
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.absensi_kedatangan', $data);
        $this->pdf->setOption('enable-local-file-access', true)
        ->setOption('page-size', 'A4')
        ->setOption('margin-top', '3.8cm')
        ->setOption('margin-left', '2cm')
        ->setOption('margin-right', '2cm')
        ->setOption('margin-bottom', '2cm')
        ->setOption('header-html', view('pdf._header_attendance_report', $data));
    }

    public function download()
    {   
        return $this->pdf->download("Manifest Kedatangan - ".strtoupper($this->umrohTrip->title) . " - PT JEJAK IMANI BERKAH BERSAMA.pdf");
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
        return view('pdf.absensi_kedatangan', $this->data)->render();
    }
}
