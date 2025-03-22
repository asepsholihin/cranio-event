<?php

namespace App\Exports;

use App\Models\EquipmentDelivery;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Carbon\Carbon;

class ChecklistEquipmentDeliveryExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents
{
    private $rowNumber;
    private $request;
    private $equipments;

    public function __construct($request)
    {
        $this->request = $request;
        $this->rowNumber = 0;


        $query = DB::table('equipments')->select(['name']);
        $umrohTrip = DB::table('umroh_trips')->where('id', $this->request['umroh_trip_id'])->first();
        if($umrohTrip) {
            $query->where(function($q) use($umrohTrip) {
                $q->where('trip_category_id', $umrohTrip->category_id)
                    ->orWhereNull('trip_category_id');
            });
        }
        $this->equipments = $query->pluck('name')->toArray();
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
        ];
    }

    public function prepareRows($rows)
    {
        return $rows->transform(function ($equipmentDelivery) {
            return $equipmentDelivery;
        });
    }

    public function headings(): array
    {
        $headingRow = [
            'No.',
            'Paket',
            'Nama Participant',
            'Gender',
            'Usia',
            'No. HP',
            'Ukuran Outer',
            'Alamat Pengiriman'
        ];

        $headingRow = array_merge($headingRow, $this->equipments);
        return $headingRow;
    }

    /**
    * @var EquipmentDelivery $equipmentDelivery
    */
    public function map($equipmentDelivery): array
    {
        $this->rowNumber += 1;
        return [
            $this->rowNumber,
            $equipmentDelivery->packageName,
            $equipmentDelivery->name,
            ($equipmentDelivery->gender == 1) ? 'L': 'P',
            $equipmentDelivery->age,
            $equipmentDelivery->no_hp,
            $equipmentDelivery->body_size,
            $equipmentDelivery->address,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = EquipmentDelivery::rightJoin('participant_umroh_trips', 'equipment_deliveries.participant_id', 'participant_umroh_trips.participant_id')
            ->leftJoin('delivery_log', 'equipment_deliveries.id', 'delivery_log.equipment_delivery_id')
            ->leftJoin('order_umroh_trips', 'participant_umroh_trips.booking_order_no', 'order_umroh_trips.order_no')
            ->join('participants', 'participant.id', '=', 'participant_umroh_trips.participant_id')
            ->join('package_umroh_trips', 'package_umroh_trips.id', '=', 'participant_umroh_trips.package_umroh_trip_id')
            ->join('umroh_trips', 'umroh_trips.id', '=', 'participant_umroh_trips.umroh_trip_id')
            // ->whereDate('umroh_trips.departure_at', '>', Carbon::now())
            ->select(['equipment_deliveries.id','equipment_deliveries.delivery_status',
            'equipment_deliveries.delivery_notes',
            'equipment_deliveries.received_notes',
            'equipment_deliveries.received_evidence',
            'equipment_deliveries.delivery_code',
            'equipment_deliveries.preparation_notes',
            'equipment_deliveries.created_at as packing_date',
            'delivery_log.created_at as pickup_date',
            'participant_umroh_trips.participant_id as participant_id',
            'participant_umroh_trips.id as participant_umroh_trip_id',
            'participant_umroh_trips.umroh_trip_id',
            'participant_umroh_trips.package_umroh_trip_id',
            'package_umroh_trips.name as packageName','participant.name', 'participant_umroh_trips.booking_order_no as order_no', 'umroh_trips.title as umroh_trip_name', 'umroh_trips.departure_at', 'participant.no_hp','participant.passport_expired_date', 'participant.gender','participant.profile_photo_path', 'participant.title', 'participant.no_passport', 'participant.home_address as address', 'participant.body_size', 'participant.birth_date',
                DB::raw('
                    (CASE
                        WHEN package_umroh_trips.name iLIKE \'%Rub%\' THEN \'c\'
                        WHEN package_umroh_trips.name iLIKE \'%Emerald%\' THEN \'b\'
                        WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'a\'
                        ELSE \'c\' END
                    ) AS package_type'
                ),
            ]);
        $query->whereIn('participant_umroh_trips.role_type', [1,4]);
        if (!empty($this->request['umroh_trip_id'])) {
            $query->where('participant_umroh_trips.umroh_trip_id', $this->request['umroh_trip_id']);
        }
        if (!empty($this->request['package'])) {
            $package_id = EquipmentDelivery::singlePackageId($this->request['umroh_trip_id'], $this->request['package']);
            $query->where('package_umroh_trips.id', $package_id);
        }
        if (!empty($this->request['gender'])) {
            $query->where('participant.gender', $this->request['gender']);
        }
        if (!empty($this->request['booking'])) {
            $query->where('participant_umroh_trips.order_umroh_trip_id', $this->request['booking']);
        }
        if (!empty($this->request['packing_date'])) {
            $query->whereDate('equipment_deliveries.created_at', $this->request['packing_date']);
        }
        if (!empty($this->request['self_pickup'])) {
            $query->where('equipment_deliveries.self_pickup', $this->request['self_pickup']);
        }
        $query->where(function($q) {
            // $q->where('order_umroh_trips.paid', '>', 0)
            // ->orWhere('role_type', '4');
        });
        $query->orderByRaw('package_type ASC, booking_order_no ASC');
        return $query;
    }

     /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $phpSpreadSheet = $event->sheet->getDelegate();
                $phpSpreadSheet->getRowDimension('1')->setRowHeight(40, 'pt');
            },
        ];
    }
}
