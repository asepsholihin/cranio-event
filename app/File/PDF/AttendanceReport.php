<?php
namespace App\File\PDF;

use App\Models\Attendance;
use App\Models\EventAttendance;
use Illuminate\Support\Str;
use SnappyImage;
use PDF;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class AttendanceReport
{
    private $eventId;
    private $event;
    private $data;
    private $pdf;

    public function __construct($eventId)
    {
        App::setLocale('id');
        $this->eventId = $eventId;
        $this->event = EventAttendance::find($eventId);

        $packages = Attendance::selectRaw("package_umroh_trips.name as package_name,
        (CASE 
            WHEN package_umroh_trips.name iLIKE '%Onyx%' THEN 'd' 
            WHEN package_umroh_trips.name iLIKE '%Rub%' THEN 'c' 
            WHEN package_umroh_trips.name iLIKE '%Emerald%' THEN 'b' 
            WHEN package_umroh_trips.name iLIKE '%Sapphire%' THEN 'a' 
            ELSE 'e' END
        ) AS package_type")
        ->join('event_attendances', 'event_attendances.id', 'attendances.event_id')
        ->leftjoin('participant_umroh_trips', function ($join) {
            $join->on('participant_umroh_trips.participant_id', 'attendances.participant_id');
            $join->on('participant_umroh_trips.umroh_trip_id', 'event_attendances.umroh_trip_id');
        })
        ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
        ->where('event_id', $this->eventId)
        ->groupBy('package_umroh_trips.name')
        ->orderBy('package_type', 'ASC')
        ->get();

        foreach ($packages as $key => $package) {
            $package->data = Attendance::select(['participant.id', 'participant.title', 'participant.name', 'participant.name_in_passport', 'participant.no_hp', 'participant.no_passport', 'participant_umroh_trips.manasik_table', 'participant_umroh_trips.no_urut'
            ])
            ->join('participants', 'participant.id', 'attendances.participant_id')
            ->join('event_attendances', 'event_attendances.id', 'attendances.event_id')
            ->leftjoin('participant_umroh_trips', function ($join) {
                $join->on('participant_umroh_trips.participant_id', 'attendances.participant_id');
                $join->on('participant_umroh_trips.umroh_trip_id', 'event_attendances.umroh_trip_id');
            })
            ->leftjoin('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
            ->where('event_id', $this->eventId)
            ->where('package_umroh_trips.name', $package->package_name)
            ->groupBy('participant.id','attendances.event_id','attendances.participant_id','participant_umroh_trips.id')
            ->orderByRaw('participant_umroh_trips.no_urut ASC NULLS LAST, manasik_table ASC NULLS LAST')->get();
        }
        
        $data = [
            'packages' => $packages,
            'event' => $this->event
        ];
        $this->data = $data;

        // return view('pdf.attendance_report', $data)->render();
        $this->pdf = PDF::loadView('pdf.attendance_report', $data);
        $this->pdf->setOption('enable-local-file-access', true)
        ->setOption('encoding', 'utf-8')
        ->setOption('margin-top', '3cm')
        ->setOption('margin-left', '1cm')
        ->setOption('margin-right', '1cm')
        ->setOption('margin-bottom', '1cm')
        ->setOption('header-html', view('pdf._header_attendance_report', $data));
    }

    public function download()
    {
        return $this->pdf->download("Attendance-Report-".$this->event->name.'.pdf');
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
        return view('pdf.attendance_report', $this->data)->render();
    }
}
