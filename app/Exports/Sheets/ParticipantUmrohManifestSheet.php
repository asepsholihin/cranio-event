<?php

namespace App\Exports\Sheets;

use App\Models\ParticipantUmrohTrip;
use App\Models\UmrohTrip;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ParticipantUmrohManifestSheet implements FromQuery, ShouldAutoSize, WithEvents, WithHeadings, WithStyles, WithMapping, WithCustomStartCell, WithDefaultStyles, WithTitle
{
    const START_ROW = 5;

    private $umrohId;
    private $umrohTrip;
    private $rowNumber;
    private $ticketType;

    public function __construct($umrohId, $ticketType)
    {
        $this->umrohId = $umrohId;
        $this->umrohTrip = UmrohTrip::find($umrohId);
        $this->ticketType = $ticketType;
        $this->rowNumber = 0;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 32], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            2 => ['font' => ['bold' => true, 'size' => 24], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'A' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'B' => ['alignment' => ['horizontal' => Alignment::VERTICAL_CENTER]],
            'C' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'D' => ['alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            'E' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'F' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'G' => ['alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            'H' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'I' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'J' => ['alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            'K' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            5 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
        ];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [ 'font' => [ 'size' => 18 ] ];
    }

    public function prepareRows($rows)
    {
        return $rows->transform(function ($participant) {
            $participant->gender = ($participant->gender == 1)? 'M':'F';

            return $participant;
        });
    }

    /**
    * @var ParticipantUmrohTrip $participant
    */
    public function map($participant): array
    {
        $this->rowNumber += 1;

        $birthDate = Carbon::parse($participant->birth_date);
        $age = Carbon::parse($this->umrohTrip->return_at)->diffInYears($birthDate);
        return [
            $this->rowNumber,
            $participant->group_bus,
            $participant->title,
            ($participant->name_in_passport) ? strtoupper($participant->name_in_passport) : strtoupper($participant->name),
            $participant->gender,
            $age,
            $participant->birth_place,
            date('d-m-Y', strtotime($participant->birth_date)),
            $participant->no_passport,
            str_ireplace("imigrasi","",$participant->passport_held_by),
            date('d-m-Y', strtotime($participant->passport_published_date)),
            date('d-m-Y', strtotime($participant->passport_expired_date)),
        ];
    }

    public function headings(): array
    {
        return [
            strtoupper('No'),
            strtoupper('Bus'),
            strtoupper('Title'),
            strtoupper("Nama Participant"),
            strtoupper('Sex'),
            strtoupper('Age'),
            strtoupper('Place'),
            strtoupper('Date Birth'),
            strtoupper('No Passport'),
            strtoupper('Place Issue'),
            strtoupper('Date of Issue'),
            strtoupper('Date of Expired'),
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = ParticipantUmrohTrip::query()
            ->join('participants', 'participant_umroh_trips.participant_id', '=', 'participant.id')
            ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', '=', 'package_umroh_trips.id')
            ->join('umroh_trips', 'participant_umroh_trips.umroh_trip_id', '=', 'umroh_trips.id')
            ->leftjoin('order_umroh_trips', 'participant_umroh_trips.order_umroh_trip_id', '=', 'order_umroh_trips.id')
            ->select([
                'participant.name_in_passport',
                'participant.full_name_vaccine',
                'participant.name',
                'participant.gender',
                'participant.no_passport',
                'participant.passport_held_by',
                'participant.birth_date',
                'participant.title',
                'participant_umroh_trips.group_bus',
                // DB::raw("date_part('year', age(participant.birth_date)) age"),
                'participant.birth_place',
                'participant.passport_held_by',
                'participant.passport_expired_date',
                'participant.passport_published_date',
                DB::raw('(CASE 
                    WHEN package_umroh_trips.name iLIKE \'%Rub%\' THEN \'c\' 
                    WHEN package_umroh_trips.name iLIKE \'%Emerald%\' THEN \'b\' 
                    WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'a\' 
                    ELSE \'c\' END
                ) AS package_type'),
                DB::raw('(CASE WHEN participant_umroh_trips.role_type = 1 THEN \'z\' ELSE \'a\' END) AS crew'),
            ])
            ->where('participant_umroh_trips.role_type', '!=', 3);
        $query->where(function($q) {
            $q->where('participant_umroh_trips.umroh_trip_id', $this->umrohId);
            $q->orWhere('order_umroh_trips.manifest_umroh_trip_id', $this->umrohId);
        });
        if($this->ticketType == "Without Ticket") {
            $query->where('participant_umroh_trips.without_ticket', 1);
        } else if($this->ticketType == "Economy") {
            $query->where('participant_umroh_trips.ticket_type', 1);
            $query->where('participant_umroh_trips.without_ticket', 2);
        } else if($this->ticketType == "Business") {
            $query->where('participant_umroh_trips.ticket_type', 2);
            $query->where('participant_umroh_trips.without_ticket', 2);
        }
        $query->orderByRaw('no_urut ASC NULLS LAST');
        return $query;
    }

     /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $departureAt = date("d F Y", strtotime($this->umrohTrip->departure_at));
                $event->sheet->setShowGridlines(false);
                $phpSpreadSheet = $event->sheet->getDelegate();
                $phpSpreadSheet->getDefaultRowDimension()->setRowHeight(23);
                $phpSpreadSheet->mergeCells('A1:L1')->mergeCells('A2:L2')
                    ->setCellValue('A1', strtoupper("DATA MANIFEST TICKET {$this->umrohTrip->title}"))
                    ->setCellValue('A2', strtoupper("MANIFEST KEBERANGKATAN TANGGAL {$departureAt} PT. JEJAK IMANI BERKAH BERSAMA"));
                $phpSpreadSheet->getRowDimension('1')->setRowHeight(-1);
                $phpSpreadSheet->getRowDimension('2')->setRowHeight(-1);
                $phpSpreadSheet->getStyle('A1:L1')->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getStyle('A2:L2')->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getColumnDimension('D')->setAutoSize(false)->setWidth(35);
                $phpSpreadSheet->getStyle("A5:L" . ($this->rowNumber + self::START_ROW))->applyFromArray([
                    'font' => [
                        'name' => 'Mulish',
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);
                $phpSpreadSheet->getStyle("A1:L" . ($this->rowNumber + self::START_ROW))->applyFromArray([
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

    /**
     * @return string
     */
    public function title(): string
    {
        return strtoupper("{$this->ticketType}");
    }
}
