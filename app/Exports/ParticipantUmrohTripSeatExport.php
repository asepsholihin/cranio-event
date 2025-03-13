<?php

namespace App\Exports;

use App\Models\ParticipantUmrohTrip;
use App\Models\UmrohTrip;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Carbon\Carbon;

class ParticipantUmrohTripSeatExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents, WithCustomStartCell, WithDefaultStyles
{
    const START_ROW = 6;

    private $umrohId;
    private $umrohTrip;
    private $rowNumber;

    public function __construct($umrohId)
    {
        $this->umrohId = $umrohId;
        $this->umrohTrip = UmrohTrip::find($umrohId);
        $this->rowNumber = 0;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 28], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            2 => ['font' => ['bold' => true, 'size' => 28], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            3 => ['font' => ['bold' => true, 'size' => 28], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            4 => ['font' => ['bold' => true, 'size' => 28], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            6 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'A' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'B' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'D' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'E' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
        ];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [ 'font' => [ 'size' => 18 ] ];
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
            'No',
            'Title',
            'Nama',
            'PNR',
            'Keterangan',
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
            strtoupper($participant->title),
            strtoupper($participant->name_in_passport ?? $participant->name),
            null,
            $participant->maskapai_notes,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return ParticipantUmrohTrip::join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')
            ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', '=', 'package_umroh_trips.id')
            ->join('umroh_trips', 'participant_umroh_trips.umroh_trip_id', '=', 'umroh_trips.id')
            ->select([
                'participant.title',
                'participant.name',
                'participant.name_in_passport',
                'participant_umroh_trips.maskapai_notes',
                'participant.no_passport',
            ])
            ->where('participant_umroh_trips.role_type', '!=', 3)
            ->where('participant_umroh_trips.umroh_trip_id', $this->umrohId)
            ->orderByRaw('no_urut ASC NULLS LAST');
    }

     /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $departureAirport = $this->umrohTrip->airport_departure_code;
                $departureTime  = ($this->umrohTrip->departure_at_time) ? "(".Carbon::parse($this->umrohTrip->departure_at_time)->format('H:i').")" : "";
                $returnAirport = $this->umrohTrip->airport_departure_destination_code;
                $returnTime  = ($this->umrohTrip->departure_destination_at_time) ? "(".Carbon::parse($this->umrohTrip->departure_destination_at_time)->format('H:i').")" : "";

                $event->sheet->setShowGridlines(false);
                $phpSpreadSheet = $event->sheet->getDelegate();
                $phpSpreadSheet->getDefaultRowDimension()->setRowHeight(23);
                $phpSpreadSheet->mergeCells('A1:E1')->mergeCells('A2:E2')->mergeCells('A3:E3')->mergeCells('A4:E4')
                    ->setCellValue('A1', strtoupper("DAFTAR PERMINTAAN SEAT PESAWAT"))
                    ->setCellValue('A2', strtoupper("KEBERANGKATAN " . $this->umrohTrip->title))
                    ->setCellValue('A3', strtoupper(
                        Carbon::parse($this->umrohTrip->departure_at)->isoFormat('D MMMM Y') .
                        " - " . $departureAirport . " " . $departureTime .
                        $returnAirport . " " . $returnTime .
                        $this->umrohTrip->flight_number
                    ))
                    ->setCellValue('A4', strtoupper("PT. JEJAK IMANI BERKAH BERSAMA"));
                $phpSpreadSheet->getRowDimension('1')->setRowHeight(35);
                $phpSpreadSheet->getRowDimension('2')->setRowHeight(35);
                $phpSpreadSheet->getRowDimension('3')->setRowHeight(35);
                $phpSpreadSheet->getRowDimension('4')->setRowHeight(35);

                $phpSpreadSheet->getColumnDimension('C')->setAutoSize(false)->setWidth(70);
                $phpSpreadSheet->getColumnDimension('E')->setAutoSize(false)->setWidth(40);

                $phpSpreadSheet->getStyle('A6:E6')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFFF00');   
                $phpSpreadSheet->getStyle("A6:E" . ($this->rowNumber + self::START_ROW))->applyFromArray([
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

    public function startCell(): string
    {
        return 'A' . self::START_ROW;
    }
}
