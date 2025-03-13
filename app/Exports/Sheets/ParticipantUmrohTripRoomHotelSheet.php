<?php

namespace App\Exports\Sheets;

use App\Models\ParticipantUmrohTrip;
use Carbon\Carbon;
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

class ParticipantUmrohTripRoomHotelSheet implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithCustomStartCell, WithEvents, WithTitle
{
    const START_ROW = 7;

    private $umrohTrip;
    private $hotel;
    private $rowNumber;
    private $totalDoubleType;
    private $totalTripleType;
    private $totalQuadType;
    private $totalQueenType;
    private $totalSingleType;
    private $groupCellList;
    private $groupCellIndex;
    private $currentGroupRoom;
    private $checkIn;
    private $checkOut;

    public function __construct($umrohTrip, $hotel)
    {
        $this->umrohTrip = $umrohTrip;
        $this->hotel = $hotel;
        $this->rowNumber = 0;
        $this->totalDoubleType = 0;
        $this->totalTripleType = 0;
        $this->totalQuadType = 0;
        $this->totalQueenType = 0;
        $this->totalSingleType = 0;
        $this->groupCellList[] = self::START_ROW;
        $this->groupCellIndex = 1;
        $this->currentGroupRoom = null;
        $this->checkIn = Carbon::parse($hotel['check_in'])->format('d-m-Y');
        $this->checkOut = Carbon::parse($hotel['check_out'])->format('d-m-Y');
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 32], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            2    => ['font' => ['bold' => true, 'size' => 32], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            4    => ['font' => ['bold' => true]],
            5    => ['font' => ['bold' => true]],
            7    => ['font' => ['bold' => true], 'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            'B7'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'A'  => ['alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            'B'  => ['alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            'C'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'D'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'E'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'F'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'G'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
        ];
    }

    /**
     * @var ParticipantUmrohTrip $participant
     */
    public function map($participant): array
    {
        $this->rowNumber += 1;

        $this->setCellListGroup($participant->room_type, $participant->group_hotel_room, $participant->room_group_notes);

        return [
            $this->rowNumber,
            strtoupper($participant->name_in_passport ?? $participant->name),
            $participant->title,
            $participant->no_passport,
            $participant->room_type,
            $participant->group_hotel_room,
            strip_tags($participant->room_group_notes),
        ];
    }

    private function setCellListGroup($roomType, $groupRoom, $groupRoomNotes)
    {
        $untilGroupRow = $this->rowNumber + self::START_ROW;
        $groupHotelRoom = $roomType . $groupRoom;

        if (empty($groupHotelRoom)) {
            $this->groupCellIndex += (count($this->groupCellList) == 1) ? 0 : 1;
            $this->groupCellList[$this->groupCellIndex] = $untilGroupRow;
            return;
        }

        if ($this->currentGroupRoom == null || $this->currentGroupRoom == $groupHotelRoom) {
            $this->groupCellList[$this->groupCellIndex] = $untilGroupRow;
            $this->currentGroupRoom = $groupHotelRoom;
            return;
        }

        $this->groupCellIndex += 1;
        $this->groupCellList[$this->groupCellIndex] = $untilGroupRow;
        $this->currentGroupRoom = $groupHotelRoom;
    }

    public function prepareRows($rows)
    {
        return $rows->transform(function ($participant) {
            $participant->gender = ($participant->gender == 1) ? 'Laki-laki' : 'Perempuan';
            $participant->room_type = strtoupper($participant->room_type);

            return $participant;
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Participant',
            'Title',
            'No Passpor',
            'Tipe Kamar',
            'Room List',
            'Keterangan',
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
            ->join('room_umroh_trips', 'room_umroh_trips.participant_umroh_trip_id', '=', 'participant_umroh_trips.id')
            ->select([
                'participant.name_in_passport',
                'participant.name',
                'participant.title',
                'participant.no_passport',
                'package_umroh_trips.name as packageName',
                'participant_umroh_trips.room_type',
                'participant_umroh_trips.group_bus',
                'room_umroh_trips.group_room as group_hotel_room',
                'room_umroh_trips.notes as room_group_notes',
                DB::raw('(CASE WHEN participant_umroh_trips.role_type = 1 THEN \'z\' ELSE \'a\' END) AS crew'),
                DB::raw('(CASE 
                    WHEN participant_umroh_trips.room_type iLIKE \'%double%\' THEN \'a\' 
                    WHEN participant_umroh_trips.room_type iLIKE \'%triple%\' THEN \'b\' 
                    WHEN participant_umroh_trips.room_type iLIKE \'%quad%\' THEN \'c\' 
                    WHEN participant_umroh_trips.room_type iLIKE \'%queen%\' THEN \'d\' 
                    WHEN participant_umroh_trips.room_type iLIKE \'%single%\' THEN \'e\' 
                    ELSE \'e\' END
                ) AS room_type_order')
            ])
            ->where('participant_umroh_trips.umroh_trip_id', $this->umrohTrip->id)
            ->where('room_umroh_trips.hotel_name', $this->hotel['hotel_name'])
            ->orderByRaw('group_hotel_room ASC NULLS LAST');
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
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->setShowGridlines(false);
                $phpSpreadSheet = $event->sheet->getDelegate();
                $phpSpreadSheet->getRowDimension('1')->setRowHeight(36, 'pt');
                $phpSpreadSheet->getRowDimension('2')->setRowHeight(36, 'pt');
                $phpSpreadSheet->getDefaultRowDimension()->setRowHeight(23);
                $phpSpreadSheet->mergeCells('A1:G1')->mergeCells('A2:G2')->mergeCells('A4:E4')->mergeCells('A5:E5')
                    ->setCellValue('A1', strtoupper("LAPORAN PENGATURAN KAMAR {$this->umrohTrip->title}"))
                    ->setCellValue('A2', 'PT. JEJAK IMANI BERKAH BERSAMA')
                    ->setCellValue('A5', "NAMA HOTEL {$this->hotel['city_name']}: {$this->hotel['hotel_name']} *{$this->hotel['star']} ({$this->checkIn} - {$this->checkOut})");
                $phpSpreadSheet->getStyle('G5')->getFont()->setBold(true);
                $phpSpreadSheet->getColumnDimension('B')->setAutoSize(false)->setWidth(35);
                $phpSpreadSheet->getColumnDimension('F')->setAutoSize(false)->setWidth(10);
                $phpSpreadSheet->getColumnDimension('G')->setAutoSize(false)->setWidth(60);
                $phpSpreadSheet->getStyle('G')->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getStyle('F7')->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getStyle('G7')->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getRowDimension('7')->setRowHeight(36);
                $phpSpreadSheet->getStyle("A7:G" . ($this->rowNumber + self::START_ROW))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);
                $this->groupingRoom($phpSpreadSheet);
                $phpSpreadSheet->setCellValue('G5', "TOTAL: {$this->totalDoubleType} DOUBLE, {$this->totalTripleType} TRIPLE, {$this->totalQuadType} QUAD, {$this->totalQueenType} QUEEN, {$this->totalSingleType} SINGLE");
            },
        ];
    }

    private function groupingRoom($phpSpreadSheet)
    {
        $i = 0;
        $until = count($this->groupCellList) - 1;
        while ($i < $until) {
            $start = $this->groupCellList[$i] + 1;
            $end = $this->groupCellList[$i + 1];
            $index = $start;
            $this->countRoomTypeGroup($phpSpreadSheet->getCell("E{$start}")->getValue());
            $phpSpreadSheet->mergeCells("E{$start}:E{$end}");
            $phpSpreadSheet->mergeCells("F{$start}:F{$end}");
            $phpSpreadSheet->mergeCells("G{$start}:G{$end}");
            $i++;
        }
    }

    private function countRoomTypeGroup($roomType)
    {
        if ($roomType == 'DOUBLE') {
            $this->totalDoubleType += 1;
        }

        if ($roomType == 'TRIPLE') {
            $this->totalTripleType += 1;
        }

        if ($roomType == 'QUAD') {
            $this->totalQuadType += 1;
        }

        if ($roomType == 'QUEEN') {
            $this->totalQueenType += 1;
        }

        if ($roomType == 'SINGLE') {
            $this->totalSingleType += 1;
        }
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return strtoupper(str_replace("/","-",$this->hotel['hotel_name']));
    }
}
