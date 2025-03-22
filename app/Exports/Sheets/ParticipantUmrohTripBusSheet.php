<?php

namespace App\Exports\Sheets;

use App\Models\ParticipantUmrohTrip;
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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class ParticipantUmrohTripBusSheet implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithCustomStartCell, WithEvents, WithTitle
{
    const START_ROW = 5;

    private $umrohTrip;
    private $busGroup;
    private $rowNumber;
    private $busRoutes;

    public function __construct($umrohTrip, $busGroup, $busRoutes)
    {
        $this->umrohTrip = $umrohTrip;
        $this->busGroup = $busGroup;
        $this->rowNumber = 0;
        $this->busRoutes = $busRoutes;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 28], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            2    => ['font' => ['bold' => true, 'size' => 28], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            5    => ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'A'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'B'  => ['alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            'C'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'D'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
        ];
    }

    /**
    * @var ParticipantUmrohTrip $participant
    */
    public function map($participant): array
    {
        $this->rowNumber += 1;
        $defaultColumn = [
            $this->rowNumber,
            strtoupper($participant->name_in_passport ?? $participant->name),
            $participant->gender,
            $participant->no_passport,
        ];

        $newColumns = array();
        foreach ($this->busRoutes as $key => $value) {
            $newColumns[] = null;
        }
        array_merge($defaultColumn, $newColumns);

        return $defaultColumn;
    }

    public function prepareRows($rows)
    {
        return $rows->transform(function ($participant) {
            $participant->gender = ($participant->gender == 1)? 'M':'F';
            return $participant;
        });
    }

    public function headings(): array
    {
        $defaultColumn = [
            strtoupper('No'),
            strtoupper("Bus {$this->busGroup}"),
            strtoupper('Jenis Kelamin'),
            strtoupper('No Passpor'),
        ];
        return array_merge($defaultColumn, $this->busRoutes);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return ParticipantUmrohTrip::query()
            ->join('participants', 'participant.id', '=', 'participant_umroh_trips.participant_id')
            ->leftjoin('package_umroh_trips', 'package_umroh_trips.id', '=', 'participant_umroh_trips.package_umroh_trip_id')
            ->leftjoin('order_umroh_trips', 'participant_umroh_trips.booking_order_no', '=', 'order_umroh_trips.order_no')
            ->join('umroh_trips', 'umroh_trips.id', '=', 'participant_umroh_trips.umroh_trip_id')
            ->select([
                'participant.name_in_passport',
                'participant.name',
                'participant.gender',
                'participant.no_passport',
                DB::raw('
                (CASE 
                    WHEN participant_umroh_trips.role_type = 2 THEN \'a\' 
                    WHEN participant_umroh_trips.role_type = 3 THEN \'b\' 
                    ELSE \'z\' END) AS tour_crew'
                ),
                DB::raw('(CASE 
                    WHEN package_umroh_trips.name iLIKE \'%Rub%\' THEN \'c\' 
                    WHEN package_umroh_trips.name iLIKE \'%Emerald%\' THEN \'b\' 
                    WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'a\' 
                    ELSE \'c\' END
                ) AS package_type')
            ])
            ->where('participant_umroh_trips.umroh_trip_id', $this->umrohTrip->id)
            ->where('participant_umroh_trips.group_bus', $this->busGroup)
            ->orderByRaw('tour_crew ASC, no_urut ASC NULLS LAST');
    }

    public function startCell(): string
    {
        return 'A' . self::START_ROW;
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
                $phpSpreadSheet->getDefaultRowDimension()->setRowHeight(23);
                $phpSpreadSheet->mergeCells('A1:J1')->mergeCells('A2:J2')
                    ->setCellValue('A1', strtoupper("DAFTAR NAMA JAMAAH BUS {$this->busGroup} {$this->umrohTrip->title}"))
                    ->setCellValue('A2', strtoupper("PT. JEJAK IMANI BERKAH BERSAMA"));
                $phpSpreadSheet->getRowDimension('1')->setRowHeight(45, 'pt');
                $phpSpreadSheet->getRowDimension('2')->setRowHeight(45, 'pt');
                $phpSpreadSheet->getRowDimension('5')->setRowHeight(35, 'pt');

                $alphabetRange  = range('A', 'Z');
                $alphabet       = $alphabetRange[count($this->busRoutes)+3]; // returns Alphabet

                $totalRow       = ($this->rowNumber + self::START_ROW);
                $cellRange      = 'A5:'.$alphabet.$totalRow;

                $phpSpreadSheet->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'name' => 'Mulish',
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);
                $phpSpreadSheet->getStyle("A1:E" . ($this->rowNumber + self::START_ROW))->applyFromArray([
                    'font' => [
                        'name' => 'Mulish',
                    ]
                ]);
            },
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return strtoupper("BUS {$this->busGroup} {$this->umrohTrip->title}");
    }
}
