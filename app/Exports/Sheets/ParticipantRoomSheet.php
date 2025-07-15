<?php

namespace App\Exports\Sheets;

use App\Models\Participant;
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

class ParticipantRoomSheet implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithCustomStartCell, WithEvents, WithTitle
{
    const START_ROW = 8;

    private $startRow;
    private $rowNumber;
    private $totalDoubleType;
    private $totalTripleType;
    private $totalQuadType;
    private $totalQueenType;
    private $totalSingleType;
    private $groupCellList;
    private $groupCellIndex;
    private $currentGroupRoom;

    public function __construct($eventId)
    {
        $this->rowNumber = 0;
        $this->totalDoubleType = 0;
        $this->totalTripleType = 0;
        $this->totalQuadType = 0;
        $this->totalQueenType = 0;
        $this->totalSingleType = 0;
        $this->groupCellIndex = 1;
        $this->currentGroupRoom = null;

        $list_hotels = array();
        
        $this->hotels = $list_hotels;
        $this->groupCellList[] = count($list_hotels) + 4;
        $this->startRow = count($list_hotels) + 4;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 28], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            2    => ['font' => ['bold' => true, 'size' => 28], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            4    => ['font' => ['bold' => true]],
            'A'  => ['alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            'B'  => ['alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            'C'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'D'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'E'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'F'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'G'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'H'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'H4'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER]],
        ];
    }

    /**
     * @var Participant $participant
     */
    public function map($participant): array
    {
        $this->rowNumber += 1;

        $this->setCellListGroup($participant->room_number, $participant->room_group);

        return [
            $this->rowNumber,
            strtoupper($participant->name),
            $participant->whatsapp,
            $participant->room_group,
            $participant->room_number,
            '',
            ''
        ];
    }

    private function setCellListGroup($roomNumber, $roomGroup)
    {
        $untilGroupRow = $this->rowNumber + $this->startRow;
        $groupHotelRoom = $roomNumber . $roomGroup;

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
            'No.',
            'Nama Peserta',
            'No. Handphone',
            'Room Group',
            'No. Kamar',
            'Terima Kunci',
            'Keterangan',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        $query = Participant::query()
            ->join('participant_bookings', 'participant_bookings.participant_id', '=', 'participants.id')
            ->select([
                'participants.name',
                'participants.whatsapp',
                'participants.room_number',
                'participants.room_group',
            ]);
            $query->orderByRaw('room_group ASC, room_number ASC');

            return $query;
    }

    public function startCell(): string
    {
        return 'A' . $this->startRow;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $packageTitle = "Upgrade";
                $event->sheet->setShowGridlines(false);
                $phpSpreadSheet = $event->sheet->getDelegate();
                $phpSpreadSheet->getRowDimension('1')->setRowHeight(36, 'pt');
                $phpSpreadSheet->getRowDimension('2')->setRowHeight(36, 'pt');
                $phpSpreadSheet->getDefaultRowDimension()->setRowHeight(23);
                $phpSpreadSheet->mergeCells('A1:G1')->mergeCells('A2:G2')
                    ->setCellValue('A1', strtoupper("Room List"))
                    ->setCellValue('A2', 'Cranio Indonesia');
                $phpSpreadSheet->getColumnDimension('A')->setAutoSize(false)->setWidth(4);
                $phpSpreadSheet->getColumnDimension('G')->setAutoSize(false)->setWidth(60);
                $phpSpreadSheet->getStyle('G')->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getRowDimension($this->startRow)->setRowHeight(36);
                $phpSpreadSheet->getStyle("A".$this->startRow.":G" . ($this->rowNumber + $this->startRow))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);
                $this->groupingRoom($phpSpreadSheet);
               
                $phpSpreadSheet->getStyle("A1:G" . ($this->rowNumber + self::START_ROW))->applyFromArray([
                    'font' => [
                        'name' => 'Mulish',
                    ]
                ]);
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
            $phpSpreadSheet->mergeCells("D{$start}:D{$end}");
            $phpSpreadSheet->mergeCells("E{$start}:E{$end}");
            $i++;
        }
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "Room List";
    }
}
