<?php

namespace App\Exports;

use App\Models\BookingReceipt;
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

class BookingReceiptExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents
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
            'Booking No.',
            'Sender Name',
            'Bank Account',
            'Total Price',
            'Created At',
            'Status'
        ];
    }

    /**
    * @var Booking $booking
    */
    public function map($booking): array
    {
        $this->rowNumber += 1;
        $status = "Pending";
        if($booking->status == 1) $status="Pending";
        if($booking->status == 2) $status="Verified";
        if($booking->status == 3) $status="Rejected";
        return [
            $this->rowNumber,
            $booking->booking_no,
            $booking->sender_name,
            $booking->bank_account,
            $booking->payment_amount,
            ($booking->created_at) ? Carbon::parse($booking->created_at)->isoFormat('D MMMM Y') : null,
            $status,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = BookingReceipt::tableSearch();
        $query->orderByRaw('booking_receipts.sender_name ASC');
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
