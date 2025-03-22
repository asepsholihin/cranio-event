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

class ParticipantUmrohTripDocumentReportSheet implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithCustomStartCell, WithEvents, WithTitle
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
            'B'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'C'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'E'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'F'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'G'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'H'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'I'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'J'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'K'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'L'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'M'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'N'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'O'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'P'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'Q'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'R'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'S'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'T'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'U'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'V'  => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
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
            strtoupper($participant->packageName),
            date("d F Y", strtotime($this->umrohTrip->departure_at)),
            strtoupper($participant->name_in_passport ?? $participant->name),
            $participant->age,
            "'".$participant->no_hp ?? null,
            $participant->booking_order_no ?? null,
            ($participant->file_ktp_verified == 1) ? 'v' : null,
            ($participant->file_akta_verified == 1) ? 'v' : null,
            ($participant->file_buku_nikah_verified == 1) ? 'v' : null,
            ($participant->file_kk_verified == 1) ? 'v' : null,
            ($participant->file_passport_verified == 1) ? 'v' : null,
            ($participant->passport_received_at != null) ? 'v' : null,
            $participant->passport_notes,
            ($participant->file_buku_kuning_verified == 1) ? 'v' : null,
            ($participant->buku_kuning_received_at != null) ? 'v' : null,
            $participant->buku_kuning_notes,
            ($participant->file_photo_verified == 1) ? 'v' : null,
            ($participant->file_kartu_vaksin_verified == 1) ? 'v' : null,
            ($participant->file_bpjs_verified == 1) ? 'v' : null,
            ($participant->file_mcu_verified == 1) ? 'v' : null,
            ($participant->file_surat_keterangan_verified == 1) ? 'v' : null,
            ($participant->who_certificate == 1) ? 'v' : null,
            $participant->sales_name,
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
            'PAKET',
            'TANGGAL KEBERANGKATAN',
            'NAMA SESUAI KTP',
            'Usia',
            'NO HP / WA',
            'BOOKING NO',
            'KTP',
            'AKTA LAHIR',
            'BUKU NIKAH',
            'KK',
            'PASSPOR',
            'PASSPOR FISIK',
            'CATATAN PASSPOR',
            'BUKU KUNING',
            'BUKU KUNING FISIK',
            'CATATAN BUKU KUNING',
            'FOTO',
            'VAKSIN',
            'BPJS',
            'HASIL MCU',
            'SURAT KETERANGAN',
            'WHO',
            'SALES'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return ParticipantUmrohTrip::query()
            ->join('participants', 'participant_umroh_trips.participant_id', '=', 'participant.id')
            ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', '=', 'package_umroh_trips.id')
            ->join('umroh_trips', 'participant_umroh_trips.umroh_trip_id', '=', 'umroh_trips.id')
            ->join('order_umroh_trips', 'participant_umroh_trips.booking_order_no', '=', 'order_umroh_trips.order_no')
            ->select([
                'participant.name_in_passport',
                'participant.title',
                'participant.name',
                'participant.no_hp',
                'participant.birth_date',
                'package_umroh_trips.name as packageName',
                'participant_umroh_trips.*',
                DB::raw('(CASE WHEN participant_umroh_trips.role_type = 1 THEN \'z\' ELSE \'a\' END) AS crew'),
                'order_umroh_trips.sales_name',
                'participant_umroh_trips.booking_order_no',
                DB::raw("date_part('year', age(participant.birth_date)) age"),
                DB::raw('(CASE 
                    WHEN package_umroh_trips.name iLIKE \'%Rub%\' THEN \'c\' 
                    WHEN package_umroh_trips.name iLIKE \'%Emerald%\' THEN \'b\' 
                    WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'a\' 
                    ELSE \'c\' END
                ) AS package_type'),
            ])
            ->where('participant_umroh_trips.role_type', '!=', 3)
            ->where('participant_umroh_trips.umroh_trip_id', $this->umrohTrip->id)
            ->where('participant_umroh_trips.package_umroh_trip_id', $this->umrohPackage->id)
            ->orderByRaw('crew, package_type, booking_order_no, participant.name, room_type, group_hotel_room ASC NULLS LAST');
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
                $phpSpreadSheet->mergeCells('A1:W1')->mergeCells('A2:W2')
                    ->setCellValue('A1', strtoupper("DAFTAR STATUS DOKUMEN JAMAAH {$this->umrohPackage->name} {$this->umrohTrip->title}"))
                    ->setCellValue('A2', 'PT. JEJAK IMANI BERKAH BERSAMA');
                foreach (range('E', 'W') as $col) {            
                    $phpSpreadSheet->getColumnDimension($col)->setAutoSize(false)->setWidth(15);
                    $phpSpreadSheet->getStyle($col)->getAlignment()->setWrapText(true);
                }
                $phpSpreadSheet->getStyle('A4:W4')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFFFFF00');            
                $phpSpreadSheet->getRowDimension('4')->setRowHeight(-1);
                
                $phpSpreadSheet->getStyle("A4:W" . ($this->rowNumber + self::START_ROW))->applyFromArray([
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
