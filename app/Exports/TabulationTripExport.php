<?php

namespace App\Exports;

use App\Models\ParticipantUmrohTrip;
use App\Models\Participant;
use App\Models\InvoiceUmrohTrip;
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
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TabulationTripExport implements FromQuery, ShouldAutoSize, WithEvents, WithHeadings, WithStyles, WithMapping, WithCustomStartCell, WithDefaultStyles
{
    const START_ROW = 5;

    private $umrohTrip;
    private $rowNumber;

    public function __construct()
    {
        $this->umrohTrip = UmrohTrip::get();
        $this->rowNumber = 0;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            2 => ['font' => ['bold' => true], 'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            3 => ['font' => ['bold' => true], 'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            'A' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            5 => ['font' => ['bold' => true],'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'ff864c']
            ], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
        ];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [ 'font' => [ 'size' => 12 ] ];
    }

    public function prepareRows($rows)
    {
        return $rows->transform(function ($umrohTrip) {

            return $umrohTrip;
        });
    }

    /**
    * @var UmrohTrip $umrohTrip
    */
    public function map($umrohTrip): array
    {
        $this->rowNumber += 1;
        $mr = ParticipantUmrohTrip::join('participants', 'participant.id', 'participant_umroh_trips.participant_id')->where('umroh_trip_id', $umrohTrip->id)->where('participant.gender', 1)->count();
        $ms = ParticipantUmrohTrip::join('participants', 'participant.id', 'participant_umroh_trips.participant_id')->where('umroh_trip_id', $umrohTrip->id)->where('participant.gender', 2)->count();
        $total_infants = ParticipantUmrohTrip::where('umroh_trip_id', $umrohTrip->id)->where('infants', 1)->count();
        $tour_leader = (Participant::find($umrohTrip->tour_leader))? Participant::find($umrohTrip->tour_leader)->name : "";
        
        return [
            $this->rowNumber,
            $umrohTrip->title,
            date('d-m-Y', strtotime($umrohTrip->departure_at)) ." - ". $umrohTrip->total_days . " Hari",
            $umrohTrip->flight_status,
            $tour_leader,
            $umrohTrip->airlines,
            $umrohTrip->number_of_seats,
            $umrohTrip->total_booking,
            $umrohTrip->available_seats,
        ];
    }

    public function headings(): array
    {
        return [
            "NO",
            "PAKET UMRAH/ ISLAMIC TUR",
            "TANGGAL KEBERANGKATAN",
            "FIX / TENTATIF",
            "TOUR LEADER / USTADZ",
            "MASKAPAI",
            "KAPSASITAS SEAT DEWASA",
            "BOOKING",
            "SEAT TERSISA"
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return UmrohTrip::select([
            'umroh_trips.*',
            DB::raw("(SELECT count(*) FROM participant_umroh_trips WHERE participant_umroh_trips.infants = 1 AND participant_umroh_trips.umroh_trip_id = umroh_trips.id) as total_infants"),
            DB::raw("(SELECT sum(total_pax_trip) FROM order_umroh_trips WHERE order_umroh_trips.umroh_trip_id = umroh_trips.id AND deleted_at IS NULL AND is_badal=false) as total_booking"),
            DB::raw("(SELECT sum(total_pax_trip) FROM order_umroh_trips WHERE order_umroh_trips.umroh_trip_id = umroh_trips.id AND deleted_at IS NULL AND is_badal=false AND order_from IN ('cabang','mitra')) as total_booking_mitra"),
            DB::raw("(SELECT count(id) FROM participant_umroh_trips WHERE participant_umroh_trips.umroh_trip_id = umroh_trips.id AND waiting_list=1 AND role_type In (1,4)) as total_waiting_list"),
            DB::raw("((SELECT sum(total_pax_trip) FROM order_umroh_trips WHERE order_umroh_trips.umroh_trip_id = umroh_trips.id AND deleted_at IS NULL AND is_badal=false) - (SELECT sum(total_pax_trip) FROM order_umroh_trips WHERE order_umroh_trips.umroh_trip_id = umroh_trips.id AND is_badal=false AND order_from IN ('cabang','mitra'))) as total_booking_ji"),
        ])->orderBy('umroh_trips.departure_at','desc');
    }

     /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $dp_paid = InvoiceUmrohTrip::join('order_umroh_trips', 'order_umroh_trips.id', 'invoice_umroh_trips.order_umroh_trip_id')->where('invoice_umroh_trips.status', 2)->where(\DB::raw('substr(invoice_umroh_trips.description, 1, 2)'), '=' , 'DP')->count();

                $event->sheet->setShowGridlines(false);
                $phpSpreadSheet = $event->sheet->getDelegate();
                $phpSpreadSheet->getDefaultRowDimension()->setRowHeight(23);
                $phpSpreadSheet
                    ->setCellValue('B1', strtoupper("TARGET JAMAAH MASUK VIA JI 2022"))
                    ->setCellValue('B2', strtoupper("JAMAAH YANG SUDAH MASUK DAN DP VIA JI"))
                    ->setCellValue('B3', strtoupper("JAMAAH YANG SUDAH MASUK DAN DP VIA CABANG DAN MITRA"))

                    ->setCellValue('C1', 0)
                    ->setCellValue('C2', $dp_paid)
                    ->setCellValue('C3', 0)
                    
                    ->setCellValue('E1', strtoupper("SISA TARGET JAMAAH"))
                    ->setCellValue('E2', strtoupper("PERSENTASI KETERCAPAIAN"))
                    ->setCellValue('E3', strtoupper("TARGET JAMAAH DARI CABANG/ MITRA"))
                    
                    ->setCellValue('F1', 0)
                    ->setCellValue('F2', 0)
                    ->setCellValue('F3', 0);
                $phpSpreadSheet->getRowDimension('5')->setRowHeight(40, 'pt');
                $phpSpreadSheet->getStyle("A5:I" . ($this->rowNumber + self::START_ROW))->applyFromArray([
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
}
