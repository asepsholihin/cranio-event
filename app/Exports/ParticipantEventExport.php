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

class ParticipantEventExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents, WithColumnFormatting
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
            'Berangkat Dari (Rumah, Hotel Manasik)',
            'EVENT_ID',
            'ATTENDANCE_ID',
        ];
    }

    /**
    * @var Participant $participant
    */
    public function map($participant): array
    {
        $this->rowNumber += 1;

        return [
            $this->rowNumber,
            $this->umrohTrip->title,
            $participant->package_name,
            $participant->group_bus,
            $participant->name,
            $participant->fathers_name,
            $participant->id,
            $participant->package_umroh_trip_id,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = Attendance::select('attendances.id', 'participant.name', 'participant_umroh_trips.group_bus', 'participant_umroh_trips.package_umroh_trip_id','package_umroh_trips.name as package_name')
        ->join('participants', 'participant.id', '=', 'attendances.participant_id')
        ->join('event_attendances', 'event_attendances.id', 'attendances.event_id')
        ->leftjoin('participant_umroh_trips', function($join) {
            $join->on('participant_umroh_trips.participant_id', 'attendances.participant_id');
            $join->on('participant_umroh_trips.umroh_trip_id', 'event_attendances.umroh_trip_id');
        })
        ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
        ->where('attendances.event_id', $this->event->id)
        ->whereNull('attendances.session');
        $query->orderBy('participant.name', 'asc');
        return $query;
    }

     /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $phpSpreadSheet = $event->sheet->getDelegate();
                $phpSpreadSheet->getColumnDimension('G')->setVisible(false);
                $phpSpreadSheet->getColumnDimension('G')->setAutoSize(false)->setWidth(0);
                $phpSpreadSheet->getColumnDimension('H')->setVisible(false);
                $phpSpreadSheet->getColumnDimension('H')->setAutoSize(false)->setWidth(0);
                $phpSpreadSheet->getColumnDimension('F')->setAutoSize(false)->setWidth(32);

                $configs = "Rumah,Hotel Manasik";
                $objValidation = $phpSpreadSheet->getCell('F2')->getDataValidation();
                $objValidation->setType(DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Pilihan tidak sesuai.');
                $objValidation->setPromptTitle('Pilihan');
                $objValidation->setPrompt('Silahkan pilih dari pilihan yang tersedia');
                $objValidation->setFormula1('"' . $configs . '"');
                for ($i = 3; $i <= $this->rowNumber; $i++) {
                    $phpSpreadSheet->getCell("F{$i}")->setDataValidation(clone $objValidation);
                }
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
        ];
    }
}
