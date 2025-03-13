<?php

namespace App\Exports;

use App\Models\ParticipantUmrohTrip;
use App\Models\Participant;
use App\Models\InvoiceUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\DiscountOrderUmrohTrip;
use App\Models\OrderUmrohTrip;
use App\Models\EquipmentDelivery;
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
use Carbon\Carbon;

class TabulationTripDetailExport implements FromQuery, ShouldAutoSize, WithEvents, WithHeadings, WithStyles, WithMapping, WithCustomStartCell, WithDefaultStyles
{
    const START_ROW = 1;

    private $umrohTripId;
    private $rowNumber;

    public function __construct($umrohTripId)
    {
        $this->umrohTripId = $umrohTripId;
        $this->rowNumber = 0;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'C' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'D' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'K' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'J' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'M' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'P' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'Q' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            1 => ['font' => ['bold' => true],'fill' => [
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
        return $rows->transform(function ($participantUmrohTrip) {

            return $participantUmrohTrip;
        });
    }

    /**
    * @var ParticipantUmrohTrip $participantUmrohTrip
    */
    public function map($participantUmrohTrip): array
    {
        $this->rowNumber += 1;
        
        $totalDiscountBelongsParticipant = DiscountOrderUmrohTrip::where('assigned_participant', 'iLIKE', '%' . $participantUmrohTrip->id . '%')->sum('discount_per_pax');
        $participantUmrohTrip->discount = $totalDiscountBelongsParticipant;

        $sales = OrderUmrohTrip::find($participantUmrohTrip->order_umroh_trip_id);
        $participantUmrohTrip->sales_name = $sales->sales_name??'Admin';
        $participantUmrohTrip->booking_notes = $sales->notes??'-';

        $participantUmrohTrip->delivery_code = null;
        $participantUmrohTrip->delivery_date = null;
        $participantUmrohTrip->received_date = null;

        $equipmentDelivery = EquipmentDelivery::where('participant_id', $participantUmrohTrip->participant_id)->where('umroh_trip_id', $participantUmrohTrip->umroh_trip_id)->first();
        if($equipmentDelivery) {
            $deliveryLog = DB::table('delivery_log')->where('equipment_delivery_id', $equipmentDelivery->id)->first();
            $participantUmrohTrip->delivery_code = $deliveryLog->delivery_code;
            $participantUmrohTrip->delivery_date = ($deliveryLog->delivery_date) ? Carbon::parse($deliveryLog->delivery_date)->isoFormat('D MMMM Y') : null;
            $participantUmrohTrip->received_date = ($deliveryLog->received_date) ? Carbon::parse($deliveryLog->received_date)->isoFormat('D MMMM Y') : null;
        }

        $requestKhusus = "";
        foreach ($participantUmrohTrip->request_participant as $value) {
            $requestKhusus .= $value['description'];
        }

        return [
            $this->rowNumber,
            $participantUmrohTrip->name,
            ($participantUmrohTrip->gender == 1) ? 'Pria': 'Wanita',
            $participantUmrohTrip->age,
            "'".$participantUmrohTrip->no_hp,
            $participantUmrohTrip->room_group_notes,
            $participantUmrohTrip->maskapai_notes,
            $requestKhusus,
            $participantUmrohTrip->booking_notes,
            $participantUmrohTrip->discount,
            $participantUmrohTrip->room_type,
            $participantUmrohTrip->home_address,
            $participantUmrohTrip->body_size,
            $participantUmrohTrip->sales_name,
            $participantUmrohTrip->delivery_date,
            $participantUmrohTrip->received_date,
            $participantUmrohTrip->delivery_code
        ];
    }

    public function headings(): array
    {
        return [
            "NO",
            "NAMA JAMAAH",
            "GENDER",
            "USIA",
            "NO HP",
            "CATATAN KAMAR",
            "CATATAN MASKAPAI",
            "CATATAN KHUSUS",
            "CATATAN BOOKING",
            "DISKON",
            "TIPE KAMAR",
            "ALAMAT PENGIRIMAN",
            "UKURAN OUTER",
            "REKOMENDASI",
            "TANGGAL KIRIM",
            "TANGGAL TERIMA",
            "NO RESI",
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return ParticipantUmrohTrip::
        select([
            'participant_umroh_trips.id',
            'participant_umroh_trips.participant_id',
            'participant_umroh_trips.umroh_trip_id',
            'participant.name',
            'participant.gender',
            'participant.birth_date',
            'participant.no_hp',
            'participant_umroh_trips.room_group_notes',
            'participant_umroh_trips.maskapai_notes',
            'participant_umroh_trips.room_type',
            'participant.home_address',
            'participant.body_size',
            'participant_umroh_trips.order_umroh_trip_id',
            DB::raw("date_part('year', age(participant.birth_date)) age"),
            DB::raw('
                (CASE 
                    WHEN package_umroh_trips.name iLIKE \'%Silver%\' THEN \'e\'
                    WHEN package_umroh_trips.name iLIKE \'%Rub%\' THEN \'d\' 
                    WHEN package_umroh_trips.name iLIKE \'%Emerald%\' THEN \'c\' 
                    WHEN package_umroh_trips.name iLIKE \'%Gold%\' THEN \'b\' 
                    WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'a\' 
                    ELSE \'f\' END
                ) AS package_type'
            )
        ])
        ->join('participant', 'participant.id', 'participant_umroh_trips.participant_id')
        ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
        ->where('participant_umroh_trips.umroh_trip_id', $this->umrohTripId)
        ->whereIn('role_type', [1,4])
        ->orderByRaw('package_type ASC, participant_umroh_trips.booking_order_no ASC');
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
                $phpSpreadSheet->getRowDimension('1')->setRowHeight(40, 'pt');
                $phpSpreadSheet->getStyle("A1:Q" . ($this->rowNumber + self::START_ROW))->applyFromArray([
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
