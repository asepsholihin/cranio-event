<?php

namespace App\Exports;

use App\Exceptions\ErrorMessageException;
use App\Exports\Sheets\AttendanceReportSheet;
use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Style\Style;

class AttendanceReportExport implements WithMultipleSheets
{
    private $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
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

        foreach ($packages as $package) {
            $sheets[] = new AttendanceReportSheet($this->eventId, $package);
        }

        return $sheets;
    }

}
