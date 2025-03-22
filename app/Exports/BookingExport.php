<?php

namespace App\Exports;

use App\Models\Booking;
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

class BookingExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents
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
        return $rows->transform(function ($booking) {
            return $booking;
        });
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nama Participant',
            'Nomor Whatsapp',
            'Hospital',
            'Paket',
            'Total Pax',
            'Total Price',
            'Total Unpaid',
            'Date',
            'Status'
        ];
    }

    /**
    * @var Booking $booking
    */
    public function map($booking): array
    {
        $this->rowNumber += 1;
        return [
            $this->rowNumber,
            $booking->account_name,
            $booking->account_wa,
            $booking->account_hospital,
            $booking->package,
            $booking->total_pax,
            $booking->total_price,
            $booking->total_unpaid,
            ($booking->created_at) ? Carbon::parse($booking->created_at)->isoFormat('D MMMM Y') : null,
            $booking->order_status,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = Booking::tableSearch();
        $query->orderByRaw('bookings.id ASC');
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
