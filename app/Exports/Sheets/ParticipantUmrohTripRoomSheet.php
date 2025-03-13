<?php

namespace App\Exports\Sheets;

use App\Models\ParticipantUmrohTrip;
use App\Models\HotelUmrohTrip;
use App\Models\PackageUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\OrderUmrohTrip;
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

class ParticipantUmrohTripRoomSheet implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithCustomStartCell, WithEvents, WithTitle
{
    const START_ROW = 8;

    private $startRow;
    private $umrohTrip;
    private $umrohPackage;
    private $rowNumber;
    private $totalDoubleType;
    private $totalTripleType;
    private $totalQuadType;
    private $totalQueenType;
    private $totalSingleType;
    private $groupCellList;
    private $groupCellIndex;
    private $currentGroupRoom;

    private $groupCellList_Note;
    private $groupCellIndex_Note;
    private $currentGroupRoom_Note;

    private $hotels;
    private $participantUpgradeHotels;

    public function __construct($umrohTrip, $package, $participantUpgradeHotels = null)
    {
        $this->umrohTrip = $umrohTrip;
        $this->umrohPackage = $package;
        $this->participantUpgradeHotels = $participantUpgradeHotels;
        $this->rowNumber = 0;
        $this->totalDoubleType = 0;
        $this->totalTripleType = 0;
        $this->totalQuadType = 0;
        $this->totalQueenType = 0;
        $this->totalSingleType = 0;
        $this->groupCellIndex = 1;
        $this->currentGroupRoom = null;

        $this->groupCellIndex_Note = 1;
        $this->currentGroupRoom_Note = null;

        $list_hotels = array();
        if($package) {
            $list_hotels = UmrohTrip::getHotels($umrohTrip->id, $package->id);
        }
        if($participantUpgradeHotels) {
            $list_hotels = $participantUpgradeHotels[0]->hotel_info;
        }
        
        $this->hotels = $list_hotels;
        $this->groupCellList[] = count($list_hotels) + 6;
        $this->groupCellList_Note[] = count($list_hotels) + 6;
        $this->startRow = count($list_hotels) + 6;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 28], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            2    => ['font' => ['bold' => true, 'size' => 28], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            8    => ['font' => ['bold' => true]],
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
     * @var ParticipantUmrohTrip $participant
     */
    public function map($participant): array
    {
        $this->rowNumber += 1;

        $this->setCellListGroup($participant->room_type, $participant->group_room, $participant->notes);
        $this->setCellListGroupNote(strip_tags($participant->notes));

        $is_reference = ($participant->is_reference) ? "*" : "";
        if(!$participant->is_reference) {
            $isParent = OrderUmrohTrip::where('id', $participant->order_umroh_trip_id)->where('name', 'like', '%'.$participant->name.'%')->where('no_hp', $participant->no_hp)->count();
            $is_reference = ($isParent > 0) ? "*" : "";
        }

        return [
            $this->rowNumber,
            $is_reference . strtoupper($participant->name_in_passport ?? $participant->name),
            $participant->title,
            $participant->no_passport,
            $participant->room_type,
            $participant->group_room,
            $participant->room_number,
            strip_tags($participant->notes),
        ];
    }

    private function setCellListGroup($roomType, $groupRoom, $groupRoomNotes)
    {
        $untilGroupRow = $this->rowNumber + $this->startRow;
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

    private function setCellListGroupNote($groupRoomNotes)
    {
        $groupHotelRoomNote = $groupRoomNotes;
        $untilGroupRowNote = $this->rowNumber + $this->startRow;

        if (empty($groupHotelRoomNote)) {
            $this->groupCellIndex_Note += (count($this->groupCellList_Note) == 1) ? 0 : 1;
            $this->groupCellList_Note[$this->groupCellIndex_Note] = $untilGroupRowNote;
            return;
        }

        if ($this->currentGroupRoom_Note == null || $this->currentGroupRoom_Note == $groupHotelRoomNote) {
            $this->groupCellList_Note[$this->groupCellIndex_Note] = $untilGroupRowNote;
            $this->currentGroupRoom_Note = $groupHotelRoomNote;
            return;
        }

        $this->groupCellIndex_Note += 1;
        $this->groupCellList_Note[$this->groupCellIndex_Note] = $untilGroupRowNote;
        $this->currentGroupRoom_Note = $groupHotelRoomNote;
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
            'NO',
            'NAMA JAMAAH',
            'TITLE',
            'NO PASSPOR',
            'TIPE KAMAR',
            'ROOM LIST',
            'NO KAMAR',
            'KETERANGAN',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        $query = ParticipantUmrohTrip::query()
            ->join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')
            ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', '=', 'package_umroh_trips.id')
            ->join('umroh_trips', 'participant_umroh_trips.umroh_trip_id', '=', 'umroh_trips.id')
            ->join('room_umroh_trips', 'room_umroh_trips.participant_umroh_trip_id', '=', 'participant_umroh_trips.id')
            ->select([
                'participant.name_in_passport',
                'participant.name',
                'participant.title',
                'participant.no_passport',
                'participant.no_hp',
                'package_umroh_trips.name as packageName',
                'participant_umroh_trips.room_type',
                'participant_umroh_trips.group_bus',
                'participant_umroh_trips.is_reference',
                'participant_umroh_trips.order_umroh_trip_id',
                'room_umroh_trips.hotel_name',
                'room_umroh_trips.group_room',
                'room_umroh_trips.room_number',
                'room_umroh_trips.notes',
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
            ->where('participant_umroh_trips.umroh_trip_id', $this->umrohTrip->id);
            if($this->umrohPackage) {
                $query->where('participant_umroh_trips.package_umroh_trip_id', $this->umrohPackage->id)
                ->where('room_umroh_trips.hotel_name', $this->hotels[1]['hotel_name']);
            }
            if($this->participantUpgradeHotels) {
                $participantupgradehotels = [];
                foreach ($this->participantUpgradeHotels as $value) {
                    $participantupgradehotels[] = $value->id;
                }
                $query->whereIn('participant_umroh_trips.id', $participantupgradehotels)
                ->where('room_umroh_trips.hotel_name', (count($this->participantUpgradeHotels[0]->hotel_upgrade) > 0) ? $this->participantUpgradeHotels[0]->hotel_upgrade : $this->participantUpgradeHotels[0]->hotel_upgrade[0]);
            }
            $query->orderByRaw('group_room ASC NULLS LAST, notes ASC NULLS LAST');

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
                if($this->umrohPackage) {
                    $packageTitle = $this->umrohPackage->name;
                }
                
                $event->sheet->setShowGridlines(false);
                $phpSpreadSheet = $event->sheet->getDelegate();
                $phpSpreadSheet->getRowDimension('1')->setRowHeight(36, 'pt');
                $phpSpreadSheet->getRowDimension('2')->setRowHeight(36, 'pt');
                $phpSpreadSheet->getDefaultRowDimension()->setRowHeight(23);
                $phpSpreadSheet->mergeCells('A1:H1')->mergeCells('A2:H2')
                    ->setCellValue('A1', strtoupper("LAPORAN PENGATURAN KAMAR {$packageTitle} {$this->umrohTrip->title}"))
                    ->setCellValue('A2', 'PT. JEJAK IMANI BERKAH BERSAMA');
                $phpSpreadSheet->getStyle('H4')->getFont()->setBold(true);
                $phpSpreadSheet->getStyle('H4')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT
                    ],
                ]);
                $phpSpreadSheet->getColumnDimension('A')->setAutoSize(false)->setWidth(4);
                $phpSpreadSheet->getColumnDimension('F')->setAutoSize(false)->setWidth(13);
                $phpSpreadSheet->getColumnDimension('G')->setAutoSize(false)->setWidth(13);
                $phpSpreadSheet->getColumnDimension('H')->setAutoSize(false)->setWidth(80);
                $phpSpreadSheet->getStyle('G')->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getStyle('H')->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getStyle('F'.$this->startRow)->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getStyle('G'.$this->startRow)->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getStyle('H'.$this->startRow)->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getRowDimension($this->startRow)->setRowHeight(36);
                $phpSpreadSheet->getStyle("A".$this->startRow.":H" . ($this->rowNumber + $this->startRow))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);
                $this->groupingRoom($phpSpreadSheet);
                $this->groupingRoomNote($phpSpreadSheet);
                $phpSpreadSheet->setCellValue('H4', "TOTAL: {$this->totalDoubleType} DOUBLE, {$this->totalTripleType} TRIPLE, {$this->totalQuadType} QUAD, {$this->totalQueenType} QUEEN, {$this->totalSingleType} SINGLE");
                foreach ($this->hotels as $key => $hotel) {
                    $phpSpreadSheet->getStyle('A'.(4+$key))->getFont()->setBold(true);
                    $phpSpreadSheet->mergeCells('A'.(4+$key).':E'.(4+$key).'');
                    $phpSpreadSheet->setCellValue('A'.(4+$key), ucwords(strtolower($hotel['city_name'])).": ". ucwords(strtolower($hotel['hotel_name'])) ."*". $hotel['star'] . " - Check in: " . Carbon::parse($hotel['check_in'])->format('d/m/Y') ." | Check out: ". Carbon::parse($hotel['check_out'])->format('d/m/Y'));
                }

                $phpSpreadSheet->getStyle("A1:H" . ($this->rowNumber + self::START_ROW))->applyFromArray([
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
            $this->countRoomTypeGroup($phpSpreadSheet->getCell("E{$start}")->getValue());
            $phpSpreadSheet->mergeCells("E{$start}:E{$end}");
            $phpSpreadSheet->mergeCells("F{$start}:F{$end}");
            $phpSpreadSheet->mergeCells("G{$start}:G{$end}");
            $i++;
        }
    }


    private function groupingRoomNote($phpSpreadSheet)
    {
        $i = 0;
        $until = count($this->groupCellList_Note) - 1;
        while ($i < $until) {
            $start = $this->groupCellList_Note[$i] + 1;
            $end = $this->groupCellList_Note[$i + 1];
            $index = $start;
            $phpSpreadSheet->mergeCells("H{$start}:H{$end}");
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
        if($this->umrohPackage) {
            return "{$this->umrohPackage->name}";
        } else {
            return $this->participantUpgradeHotels[0]->note_upgrade;
        }
    }
}
