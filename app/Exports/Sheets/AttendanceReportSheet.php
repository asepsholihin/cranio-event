<?php

namespace App\Exports\Sheets;

use App\Models\EventAttendance;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceReportSheet implements FromQuery, ShouldAutoSize, WithEvents, WithHeadings, WithStyles, WithMapping, WithCustomStartCell, WithTitle
{
    const START_ROW = 4;

    private $event;
    private $package;
    private $rowNumber;

    public function __construct($eventId, $package)
    {
        $this->event = EventAttendance::find($eventId);
        $this->package = $package;
        $this->rowNumber = 0;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 20], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            2 => ['font' => ['bold' => true, 'size' => 20], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'A' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'B' => ['alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            'C' => ['alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            'D' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'E' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'F' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'G' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            4 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
        ];
    }

    public function prepareRows($rows)
    {
        return $rows->transform(function ($participant) {
            $participant->gender = ($participant->gender == 1)? 'Laki-laki':'Perempuan';

            return $participant;
        });
    }

    /**
    * @var Attendance $participant
    */
    public function map($participant): array
    {
        $this->rowNumber += 1;
        return [
            $participant->no_urut,
            strtoupper($participant->title),
            ($participant->name_in_passport) ? strtoupper($participant->name_in_passport) : strtoupper($participant->name),
            $participant->no_passport,
            $participant->manasik_table,
            null,
            null
            // ($participant->departure_from_name == 1) ? "V" : "",
            // ($participant->departure_from_name == 0) ? "V" : "",
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Title',
            'Nama',
            'Pass No',
            'Nomer Meja',
            'Hotel Manasik',
            'Hotel Lain/Rumah',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = Attendance::select(['participant.id', 'participant.title', 'participant.name', 'participant.name_in_passport', 'participant.no_hp', 'participant.no_passport', 'participant_umroh_trips.manasik_table', 'participant_umroh_trips.no_urut'])
        ->join('participants', 'participant.id', 'attendances.participant_id')
        ->join('event_attendances', 'event_attendances.id', 'attendances.event_id')
        ->leftjoin('participant_umroh_trips', function ($join) {
            $join->on('participant_umroh_trips.participant_id', 'attendances.participant_id');
            $join->on('participant_umroh_trips.umroh_trip_id', 'event_attendances.umroh_trip_id');
        })
        ->leftjoin('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
        ->where('event_id', $this->event->id)
        ->where('package_umroh_trips.name', $this->package->package_name)
        ->groupBy('participant.id','attendances.event_id','attendances.participant_id','participant_umroh_trips.id')
        ->orderByRaw('participant_umroh_trips.no_urut ASC NULLS LAST, manasik_table ASC NULLS LAST');
        
        return $query;
    }

     /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->setShowGridlines(false);
                $phpSpreadSheet = $event->sheet->getDelegate();
                $phpSpreadSheet->getDefaultRowDimension()->setRowHeight(15);
                $phpSpreadSheet->mergeCells('A1:G1')->mergeCells('A2:G2')
                    ->setCellValue('A1', strtoupper("ABSENSI " . $this->event->name))
                    ->setCellValue('A2', strtoupper("PT. JEJAK IMANI BERKAH BERSAMA"));
                $phpSpreadSheet->getStyle('A1:G1')->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getStyle('A2:G2')->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getStyle('A4:G4')->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getRowDimension('1')->setRowHeight(30);
                $phpSpreadSheet->getRowDimension('2')->setRowHeight(30);
                $phpSpreadSheet->getRowDimension('4')->setRowHeight(40);
                $phpSpreadSheet->getColumnDimension('E')->setAutoSize(false)->setWidth(10);
                $phpSpreadSheet->getColumnDimension('F')->setAutoSize(false)->setWidth(11);
                $phpSpreadSheet->getColumnDimension('G')->setAutoSize(false)->setWidth(11);
                $phpSpreadSheet->getStyle('A4:G4')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFFF00');   
                $phpSpreadSheet->getStyle("A4:G" . ($this->rowNumber + self::START_ROW))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);
            },
        ];
    }

    public function startCell(): string
    {
        return 'A' . self::START_ROW;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return strtoupper($this->package->package_name);
    }
}
