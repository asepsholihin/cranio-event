<?php

namespace App\Exports;

use App\Models\BookingHotelEvent;
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

class BookingHotelEventExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents
{
    private $rowNumber;
    private $request;

    public function __construct($request)
    {
        $this->request = $request;
        $this->rowNumber = 0;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
        ];
    }

    public function prepareRows($rows)
    {
        return $rows->transform(function ($bookingHotelEvent) {
            return $bookingHotelEvent;
        });
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nama Participant',
            'Nomor Handphone',
            'Keberangkatan',
            'Paket',
            'Nama Hotel',
            'Check In',
            'Check Out',
            'Tipe Kamar',
            'Total Room',
            'Total Pax',
            'Pesanan Tambahan',
            'Total Pax Tambahan',
            'Room Number',
            'Total Biaya',
            'Metode Pembayaran',
            'Status'
        ];
    }

    /**
    * @var BookingHotelEvent $bookingHotelEvent
    */
    public function map($bookingHotelEvent): array
    {
        $this->rowNumber += 1;
        return [
            $this->rowNumber,
            $bookingHotelEvent->name,
            $bookingHotelEvent->no_hp,
            $bookingHotelEvent->umroh_trip_title,
            $bookingHotelEvent->package_name,
            $bookingHotelEvent->hotel_name,
            ($bookingHotelEvent->checkin_date) ? Carbon::parse($bookingHotelEvent->checkin_date)->isoFormat('D MMMM Y') : null,
            ($bookingHotelEvent->checkout_date) ? Carbon::parse($bookingHotelEvent->checkout_date)->isoFormat('D MMMM Y') : null,
            $bookingHotelEvent->room_type,
            $bookingHotelEvent->total_room,
            $bookingHotelEvent->total_pax,
            $bookingHotelEvent->additional_item,
            $bookingHotelEvent->additional_pax,
            $bookingHotelEvent->room_number,
            $bookingHotelEvent->total_amount,
            $bookingHotelEvent->payment_method,
            $bookingHotelEvent->status,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = BookingHotelEvent::select('booking_hotel_events.*', 'participant.name', 'participant.no_hp', 'package_umroh_trips.name as package_name', 'umroh_trips.title as umroh_trip_title');
        $query->join('participant', 'participant.id', 'booking_hotel_events.participant_id');
        $query->join('package_umroh_trips', 'package_umroh_trips.id', 'booking_hotel_events.package_umroh_trip_id');
        $query->join('umroh_trips', 'umroh_trips.id', 'booking_hotel_events.umroh_trip_id');
        if (!empty($this->request['checkinDate'])) {
            $dateXplode = explode('to', $this->request['checkinDate']);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
            $query->whereBetween('booking_hotel_events.checkin_date', [$start, $end]);
        }
        if (!empty($this->request['status'])) {
            $query->where('booking_hotel_events.status', $this->request['status']);
        }
        if (!empty($this->request['umrohTripId'])) {
            $query->where('booking_hotel_events.umroh_trip_id', $this->request['umrohTripId']);
        }
        if (!empty($this->request['packageName'])) {
            $query->where('package_umroh_trips.name', $this->request['packageName']);
        }
        if (!empty($this->request['roomType'])) {
            $query->where('booking_hotel_events.room_type', $this->request['roomType']);
        }
        $query->orderByRaw('booking_hotel_events.id ASC');
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
