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

class AbsensiKeberangkatan
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
            'participant_umroh_trips.no_urut',
            'participant_umroh_trips.group_bus',
            'package_umroh_trips.name as package_name'
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
        $participants = $query->orderBy('no_urut', 'ASC')->get();

        $data = [
            'umrohTrip' => $umrohTrip,
            'participants' => $participants,
            'event' => (object) array('name' => "Keberangkatan Participant " . $umrohTrip->title)
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.absensi_keberangkatan', $data);
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
        return $this->pdf->download("ABSENSI KEBERANGKATAN JAMAAH - ".strtoupper($this->umrohTrip->title).'.pdf');
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
        return view('pdf.absensi_keberangkatan', $this->data)->render();
    }
}
