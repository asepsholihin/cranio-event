<?php

namespace App\Exports;

use App\Models\Participant;
use App\Models\EventAttendance;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ParticipantEventUpdateExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents, WithColumnFormatting
{
    private $event;
    private $umrohTrip;
    private $rowNumber;

    public function __construct($event, $umrohTrip)
    {
        $this->event = $event;
        $this->umrohTrip = $umrohTrip;
        $this->rowNumber = 0;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Keberangkatan',
            'Paket',
            'Bus',
            'Nama',
            'No. Handphone',
            'Berangkat Dari Before',
            'Berangkat Dari After',
        ];
    }

    /**
     * @var Participant $participant
     */
    public function map($participant): array
    {
        return [
            $participant->no_urut,
            $this->umrohTrip->title,
            $participant->package_name,
            $participant->group_bus,
            $participant->name,
            $participant->no_hp,
            $participant->departure_from,
            $participant->departure_from_update,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        $query = Attendance::select('attendances.id', 'participant.name', 'participant.no_hp', 'attendances.departure_from', 'attendances.departure_from_update', 'participant_umroh_trips.no_urut', 'participant_umroh_trips.group_bus', 'participant_umroh_trips.package_umroh_trip_id', 'package_umroh_trips.name as package_name')
            ->join('participant', 'participant.id', '=', 'attendances.participant_id')
            ->join('event_attendances', 'event_attendances.id', 'attendances.event_id')
            ->leftjoin('participant_umroh_trips', function ($join) {
                $join->on('participant_umroh_trips.participant_id', 'attendances.participant_id');
                $join->on('participant_umroh_trips.umroh_trip_id', 'event_attendances.umroh_trip_id');
            })
            ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
            ->where('attendances.event_id', $this->event->id);
        $query->orderByRaw('participant_umroh_trips.no_urut ASC NULLS LAST');
        return $query;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
            },
        ];
    }

    public function columnFormats(): array
    {
        return [];
    }
}
