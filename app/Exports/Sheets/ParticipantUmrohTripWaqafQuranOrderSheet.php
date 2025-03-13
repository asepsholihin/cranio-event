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

class ParticipantUmrohTripWaqafQuranOrderSheet implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithCustomStartCell, WithEvents, WithTitle
{
    const START_ROW = 4;

    private $umrohTrip;
    private $umrohPackage;
    private $rowNumber;

    public function __construct($umrohTrip, $package)
    {
        $this->umrohTrip = $umrohTrip;
        $this->umrohPackage = $package;
        $this->rowNumber = 0;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 32], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            2    => ['font' => ['bold' => true, 'size' => 32], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            4    => ['font' => ['bold' => true], 'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            'D'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'E'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
        ];
    }

    /**
    * @var ParticipantUmrohTrip $participant
    */
    public function map($participant): array
    {
        $this->rowNumber += 1;

        return [
            $this->rowNumber,
            $participant->title,
            strtoupper($participant->name_in_passport ?? $participant->name),
            null,
            null,
        ];
    }

    public function prepareRows($rows)
    {
        return $rows->transform(function ($participant) {
            return $participant;
        });
    }

    public function headings(): array
    {
        return [
            'NO',
            'TITLE',
            'NAMA JAMAAH',
            'NO KAMAR',
            'AL QURAN 40 SAR'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return ParticipantUmrohTrip::query()
            ->join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')
            ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', '=', 'package_umroh_trips.id')
            ->join('umroh_trips', 'participant_umroh_trips.umroh_trip_id', '=', 'umroh_trips.id')
            ->select([
                'participant.name_in_passport',
                'participant.title',
                'participant.name',
                'participant.gender',
                'participant.no_passport',
                'package_umroh_trips.name as packageName',
                'participant_umroh_trips.room_type',
                'participant_umroh_trips.group_bus',
                'participant_umroh_trips.group_hotel_room',
                'participant_umroh_trips.room_group_notes',
                DB::raw('(CASE WHEN participant_umroh_trips.role_type = 1 THEN \'z\' ELSE \'a\' END) AS crew'),
                DB::raw('(CASE 
                    WHEN package_umroh_trips.name iLIKE \'%Rub%\' THEN \'c\' 
                    WHEN package_umroh_trips.name iLIKE \'%Emerald%\' THEN \'b\' 
                    WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'a\' 
                    ELSE \'c\' END
                ) AS package_type')
            ])
            ->where('participant_umroh_trips.umroh_trip_id', $this->umrohTrip->id)
            ->where('participant_umroh_trips.package_umroh_trip_id', $this->umrohPackage->id)
            ->orderByRaw('no_urut ASC NULLS LAST');
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
                $phpSpreadSheet->mergeCells('A1:E1')->mergeCells('A2:E2')
                    ->setCellValue('A1', strtoupper("DAFTAR PEMESANAN WAQAF AL-QUR'AN {$this->umrohPackage->name} {$this->umrohTrip->title}"))
                    ->setCellValue('A2', 'PT. JEJAK IMANI BERKAH BERSAMA');
                $phpSpreadSheet->getStyle('A1')->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getRowDimension('1')->setRowHeight(-1);
                $phpSpreadSheet->getColumnDimension('C')->setAutoSize(false)->setWidth(30);
                $phpSpreadSheet->getColumnDimension('E')->setAutoSize(false)->setWidth(20);
                $phpSpreadSheet->getStyle('E')->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getStyle('A4:E4')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFFFFF00');            
                $phpSpreadSheet->getRowDimension('4')->setRowHeight(-1);
                
                $phpSpreadSheet->getStyle("A4:E" . ($this->rowNumber + self::START_ROW))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
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
        return "{$this->umrohPackage->name}";
    }
}
