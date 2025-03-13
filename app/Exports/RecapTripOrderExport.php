<?php

namespace App\Exports;

use App\Models\InvoiceUmrohTrip;
use App\Models\OrderItemUmrohTrip;
use App\Models\OrderUmrohTrip;
use App\Models\RefundOrderUmrohTrip;
use App\Models\UmrohTrip;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class RecapTripOrderExport implements FromQuery, WithEvents, WithMapping, WithCustomStartCell, WithTitle, WithColumnFormatting, WithStrictNullComparison
{
    const START_ROW = 8;
    const END_COLUMN = 'S';

    const CURRENCY_IDR_FORMAT = '"Rp"#,##0';
    const CURRENCY_USD_FORMAT = '"$"#,##0';

    private $umrohTrip;
    private $rowNumber;
    private $orders;
    private $orderItems;
    private $invoices;
    private $refunds;

    private $totalParticipant;
    private $totalOrder;
    private $totalPaid;
    private $totalDue;

    public function __construct(UmrohTrip $umrohTrip)
    {
        $this->umrohTrip = $umrohTrip;
        $this->rowNumber = 0;
        $this->orders = [];
        $this->orderItems = new Collection();
        $this->invoices = new Collection();
        $this->refunds = new Collection();

        $this->totalParticipant = 0;
        $this->totalOrder = 0;
        $this->totalPaid = 0;
        $this->totalDue = 0;
    }

    public function title(): string
    {
        return $this->umrohTrip->title;
    }

    public function columnFormats(): array
    {
        $currencyFormat = ($this->umrohTrip->currency == 'USD') ?
            self::CURRENCY_USD_FORMAT :
            self::CURRENCY_IDR_FORMAT;

        return [
            'H' => $currencyFormat,
            'I' => $currencyFormat,
            'K' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'M' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'N' => $currencyFormat,
            'Q' => $currencyFormat,
            'R' => $currencyFormat,
            'S' => $currencyFormat,
        ];
    }

    /**
    * @var OrderUmrohTrip $order
    */
    public function map($order): array
    {
        $this->rowNumber += 1;
        $this->orders[] = $order;


        $this->totalParticipant += $order->total_pax_trip;
        $this->totalOrder += $order->total_payment;
        $this->totalPaid += $order->paid;
        $this->totalDue += $order->due_payment;

        return [
            $order->order_no,
            $order->name,
            $order->sales_name,
            $order->total_pax_trip,
            null,null,null,null,null,null,null,null,null,null,null,null,
            $order->total_payment,
            $order->paid,
            $order->due_payment,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return OrderUmrohTrip::query()
            ->where('order_umroh_trips.umroh_trip_id', $this->umrohTrip->id)
            ->orderByRaw('id');
    }

     /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $departureAt = date("d F Y", strtotime($this->umrohTrip->departure_at));
                $phpSpreadSheet = $event->sheet->getDelegate();
                $this->headers($phpSpreadSheet);
                $phpSpreadSheet
                    ->mergeCells('A1:'. self::END_COLUMN .'1')
                    ->mergeCells('A2:'. self::END_COLUMN .'2')
                    ->mergeCells('A3:'. self::END_COLUMN .'3')
                    ->mergeCells('A4:'. self::END_COLUMN .'4')
                    ->setCellValue('A1', "PT. JEJAK IMANI BERKAH BERSAMA")
                    ->setCellValue('A2', "REKAP DAFTAR PEMBAYARAN JAMAAH")
                    ->setCellValue('A3', strtoupper("{$this->umrohTrip->title}"))
                    ->setCellValue('A4', strtoupper("KEBERANGKATAN {$departureAt}"));

                $phpSpreadSheet->getStyle('A1:A4')->getFont()->setBold(true);

                $phpSpreadSheet->getStyle("A" . self::START_ROW - 2 . ':'. self::END_COLUMN . (self::START_ROW - 1))->applyFromArray([
                    'fill' => [
                       'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                       'startColor' => ['rgb' => 'fccc49']
                    ]
                ]);

                $this->addOrderItemRows($phpSpreadSheet);
                $this->addInvoiceRows($phpSpreadSheet);

                $phpSpreadSheet->getStyle("A" . self::START_ROW - 2 . ':'. self::END_COLUMN . ($this->rowNumber + self::START_ROW))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);

                $phpSpreadSheet->getStyle('E'.(self::START_ROW - 1).':I'.(self::START_ROW - 1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                        ],
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ],
                    ]
                ]);

                $phpSpreadSheet->getStyle('J'.(self::START_ROW - 1).':P'.(self::START_ROW - 1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                        ],
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ],
                    ]
                ]);

                $this->mergeRecordOrderRow($phpSpreadSheet);
                $this->setTotal($phpSpreadSheet);
                $this->setAutoWidthColumn($phpSpreadSheet);
                $this->setConditional($phpSpreadSheet);
            },
        ];
    }

    private function setTotal($phpSpreadSheet)
    {
        $currencyFormat = ($this->umrohTrip->currency == 'USD') ?
            self::CURRENCY_USD_FORMAT :
            self::CURRENCY_IDR_FORMAT;

        $line = self::START_ROW + $this->rowNumber + 1;
        $phpSpreadSheet->setCellValue("R{$line}", "Total Order");
        $phpSpreadSheet->setCellValue("S{$line}",  $this->totalOrder);
        $phpSpreadSheet->getStyle("S{$line}")->getNumberFormat()->setFormatCode($currencyFormat);
        $phpSpreadSheet->setCellValue("C{$line}", "Total Participant");
        $phpSpreadSheet->setCellValue("D{$line}", $this->totalParticipant);
        $phpSpreadSheet->getStyle("C{$line}:D{$line}")->applyFromArray([
            'fill' => [
               'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
               'startColor' => ['rgb' => 'fccc49']
            ]
        ]);
        $phpSpreadSheet->getStyle("B{$line}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $line ++;

        $phpSpreadSheet->setCellValue("R{$line}", "Total Dana Masuk");
        $phpSpreadSheet->setCellValue("S{$line}", $this->totalPaid);
        $phpSpreadSheet->getStyle("S{$line}")->getNumberFormat()->setFormatCode($currencyFormat);
        $line ++;

        $phpSpreadSheet->setCellValue("R{$line}", "Total  Sisa Pembayaran");
        $phpSpreadSheet->setCellValue("S{$line}", $this->totalDue);
        $phpSpreadSheet->getStyle("S{$line}")->getNumberFormat()->setFormatCode($currencyFormat);

        $phpSpreadSheet->getStyle("R" . $line - 2 . ':S' . $line)->applyFromArray([
            'fill' => [
               'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
               'startColor' => ['rgb' => 'fccc49']
            ]
        ]);

    }

    private function setConditional($phpSpreadSheet)
    {

        $conditional1 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional1->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional1->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_GREATERTHAN);
        $conditional1->addCondition('0');
        $conditional1->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditional1->getStyle()->getFont()->setBold(true);

        $column = self::END_COLUMN .self::START_ROW.':'.self::END_COLUMN.(self::START_ROW + $this->rowNumber);
        $conditionalStyles = $phpSpreadSheet->getStyle($column)->getConditionalStyles();
        $conditionalStyles[] = $conditional1;

        $phpSpreadSheet->getStyle($column)->setConditionalStyles($conditionalStyles);
    }

    private function mergeRecordOrderRow($phpSpreadSheet)
    {
        $startRow = self::START_ROW;
        $endRow = self::START_ROW;
        $isGreen = true;
        foreach($this->orders as $order) {
            $item = $this->orderItems->where('orderId', $order->id)->count();
            $payment = $this->invoices->where('orderId', $order->id)->count() + $this->refunds->where('orderId', $order->id)->count();
            $heightRow = $this->getHeightRowOrder($item, $payment);
            $endRow = $startRow +  $heightRow - 1;
            $phpSpreadSheet
                ->mergeCells("A{$startRow}:A{$endRow}")
                ->mergeCells("B{$startRow}:B{$endRow}")
                ->mergeCells("C{$startRow}:C{$endRow}")
                ->mergeCells("D{$startRow}:D{$endRow}")
                ->mergeCells("Q{$startRow}:Q{$endRow}")
                ->mergeCells("R{$startRow}:R{$endRow}")
                ->mergeCells("S{$startRow}:S{$endRow}")
            ;
            if ($item < $heightRow) {
                $itemRow = $startRow + $item;
                $phpSpreadSheet->mergeCells("E{$itemRow}:I{$endRow}");
            }

            if ($payment < $heightRow) {
                $paymentRow = $startRow + $payment;
                $phpSpreadSheet->mergeCells("J{$paymentRow}:P{$endRow}");
            }

            if ($heightRow == 1) {
                $phpSpreadSheet->getRowDimension($startRow)->setRowHeight(30);
            }

            $phpSpreadSheet->getStyle("A{$startRow}:". self::END_COLUMN . $endRow)->applyFromArray([
                'fill' => [
                   'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                   'startColor' => ['rgb' => $this->getColorRow($isGreen)]
                ]
            ]);

            $phpSpreadSheet->getStyle("E{$startRow}:I{$endRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                    ],
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    ],
                ]
            ]);

            $phpSpreadSheet->getStyle("J{$startRow}:P{$endRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                    ],
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    ],
                ]
            ]);

            $startRow = $endRow + 1;
            $isGreen = !$isGreen;
        }

    }

    private function getColorRow($isGreen)
    {
        if ($isGreen) {
            return 'DDF1E1';
        }

        return 'DDE3F1';
    }

    private function getHeightRowOrder($item, $payment)
    {
        if ($item > $payment) {
            return $item;
        }

        return $payment;
    }

    private function addOrderItemRows($phpSpreadSheet)
    {
        $this->orderItems = OrderItemUmrohTrip::query()
            ->join('order_umroh_trips', 'order_umroh_trips.id', '=', 'order_item_umroh_trips.order_umroh_trip_id')
            ->leftJoin('package_umroh_trips', 'order_item_umroh_trips.package_umroh_trip_id','=','package_umroh_trips.id')
            ->where('order_umroh_trips.umroh_trip_id', $this->umrohTrip->id)
            ->select([
                'order_umroh_trips.id as orderId',
                'package_umroh_trips.name as packageName',
                'order_item_umroh_trips.pax as pax',
                'order_item_umroh_trips.price as price',
                'order_item_umroh_trips.description as description',
                'order_item_umroh_trips.total_price as totalPrice',
                'order_item_umroh_trips.room_type as roomType',
            ])
            ->orderByRaw('order_umroh_trips.id ASC, order_item_umroh_trips.package_umroh_trip_id ASC, order_item_umroh_trips.id ASC')
            ->get();

        $lastEndRow = self::START_ROW;
        $startRow = self::START_ROW;
        foreach($this->orders as $key => $order) {
            if ($key > 0) {
                $startRow = $lastEndRow;
            }

            foreach($this->orderItems->where('orderId', $order->id) as $item) {
                if($startRow < $lastEndRow) {
                    $phpSpreadSheet->insertNewRowBefore($lastEndRow, 1);
                    $this->rowNumber ++;
                }

                $phpSpreadSheet->setCellValue('E' . $lastEndRow, $item->packageName ?? $item->description);
                $phpSpreadSheet->setCellValue('F' . $lastEndRow, ucfirst($item->roomType));
                $phpSpreadSheet->setCellValue('G' . $lastEndRow, $item->pax);
                $phpSpreadSheet->setCellValue('H' . $lastEndRow, $item->price);
                $phpSpreadSheet->setCellValue('I' . $lastEndRow, $item->totalPrice);
                $lastEndRow ++;
            }
        }

        $this->rowNumber --;
    }

    private function addInvoiceRows($phpSpreadSheet)
    {
        $this->invoices = InvoiceUmrohTrip::query()
            ->join('order_umroh_trips', 'order_umroh_trips.id', '=', 'invoice_umroh_trips.order_umroh_trip_id')
            ->where('order_umroh_trips.umroh_trip_id', $this->umrohTrip->id)
            ->select([
                'order_umroh_trips.id as orderId',
                'invoice_umroh_trips.invoice_no',
                'invoice_umroh_trips.created_at',
                'invoice_umroh_trips.description',
                'invoice_umroh_trips.payment_date',
                'invoice_umroh_trips.payment_method',
                'invoice_umroh_trips.other_bank',
                'invoice_umroh_trips.payment_amount',
                'invoice_umroh_trips.payment_note',
            ])
            ->orderByRaw('order_umroh_trips.id ASC, invoice_umroh_trips.id ASC')
            ->get();

            $this->refunds = RefundOrderUmrohTrip::query()
            ->join('order_umroh_trips', 'order_umroh_trips.id', '=', 'refund_order_umroh_trips.order_umroh_trip_id')
            ->where('order_umroh_trips.umroh_trip_id', $this->umrohTrip->id)
            ->select([
                'order_umroh_trips.id as orderId',
                'refund_order_umroh_trips.refund_no',
                'refund_order_umroh_trips.created_at',
                'refund_order_umroh_trips.description',
                'refund_order_umroh_trips.refund_date',
                'refund_order_umroh_trips.payment_method',
                DB::raw('NULL as other_bank'),
                'refund_order_umroh_trips.refund_amount',
                'refund_order_umroh_trips.payment_note',
            ])
            ->orderByRaw('order_umroh_trips.id ASC, refund_order_umroh_trips.id ASC')
            ->get();

        $lastEndRow = self::START_ROW;
        $rowsItem = 0;
        foreach($this->orders as $order) {
            if ($rowsItem > 0) {
                $lastEndRow += $rowsItem;
            }

            $rowsItem = $this->orderItems->where('orderId', $order->id)->count();
            foreach($this->invoices->where('orderId', $order->id) as $invoice) {
                if($rowsItem <= 0) {
                    $phpSpreadSheet->insertNewRowBefore($lastEndRow, 1);
                    $this->rowNumber ++;
                }

                $paymentDate = null;
                if ($invoice->payment_date != null) {
                    $paymentDate = Date::dateTimeToExcel($invoice->payment_date);
                }

                $paymentMethod = (! empty($invoice->other_bank))? $invoice->other_bank: $invoice->payment_method;
                $phpSpreadSheet->setCellValue('J' . $lastEndRow, $invoice->invoice_no);
                $phpSpreadSheet->setCellValue('K' . $lastEndRow, Date::dateTimeToExcel($invoice->created_at));
                $phpSpreadSheet->setCellValue('L' . $lastEndRow, $invoice->description);
                $phpSpreadSheet->setCellValue('M' . $lastEndRow, $paymentDate);
                $phpSpreadSheet->setCellValue('N' . $lastEndRow, ($invoice->payment_amount > 0) ? $invoice->payment_amount : null);
                $phpSpreadSheet->setCellValue('O' . $lastEndRow, $paymentMethod);
                $phpSpreadSheet->setCellValue('P' . $lastEndRow, $invoice->payment_note);
                $lastEndRow ++;
                $rowsItem --;
            }

            foreach($this->refunds->where('orderId', $order->id) as $refund) {
                if($rowsItem <= 0) {
                    $phpSpreadSheet->insertNewRowBefore($lastEndRow, 1);
                    $this->rowNumber ++;
                }

                $paymentDate = null;
                if ($refund->refund_date != null) {
                    $paymentDate = Date::dateTimeToExcel($refund->refund_date);
                }

                $phpSpreadSheet->setCellValue('J' . $lastEndRow, $refund->refund_no);
                $phpSpreadSheet->setCellValue('K' . $lastEndRow, Date::dateTimeToExcel($refund->created_at));
                $phpSpreadSheet->setCellValue('L' . $lastEndRow, $refund->description);
                $phpSpreadSheet->setCellValue('M' . $lastEndRow, $paymentDate);
                $phpSpreadSheet->setCellValue('N' . $lastEndRow, ($refund->refund_amount > 0) ? -$refund->refund_amount : null);
                $phpSpreadSheet->setCellValue('O' . $lastEndRow, $refund->payment_method);
                $phpSpreadSheet->setCellValue('P' . $lastEndRow, $refund->payment_note);
                $lastEndRow ++;
                $rowsItem --;
            }
        }
    }

    private function setAutoWidthColumn($phpSpreadSheet)
    {
        $phpSpreadSheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $phpSpreadSheet->getStyle('D')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $phpSpreadSheet->getStyle('G')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $phpSpreadSheet->getStyle('K')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $phpSpreadSheet->getStyle('M')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $phpSpreadSheet->getStyle('J')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $phpSpreadSheet->getStyle('O')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $columns = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S'];
        foreach($columns as $column) {
            $phpSpreadSheet->getColumnDimension($column)->setAutoSize(true);
            $phpSpreadSheet->getStyle($column)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }
    }

    private function headers($phpSpreadSheet)
    {
        $phpSpreadSheet
            ->mergeCells('A'.(self::START_ROW - 3).':B'.(self::START_ROW - 3))
            ->setCellValue('A'.(self::START_ROW - 3), "Generated At: " .  date('d M Y H:i:s'))
        ;
        $phpSpreadSheet
            ->setCellValue('A6', "Order No")
            ->setCellValue('B6', "Nama Akun")
            ->setCellValue('C6', "Sales")
            ->mergeCells('A6:A7')
            ->mergeCells('B6:B7')
            ->mergeCells('C6:C7')
            ->mergeCells('D6:D7')
            ->mergeCells('Q6:Q7')
            ->mergeCells('R6:R7')
            ->mergeCells('S6:S7')
            ->mergeCells('E6:I6')
            ->mergeCells('J6:P6')
            ->setCellValue('E6', "Order Item")
            ->setCellValue('E7', "Deskripsi")
            ->setCellValue('F7', "Room Type")
            ->setCellValue('G7', "Pax")
            ->setCellValue('H7', "Harga")
            ->setCellValue('I7', "Total Harga")
            ->setCellValue('J6', "Pembayaran")
            ->setCellValue('J7', "Invoice No")
            ->setCellValue('K7', "Tanggal Invoice")
            ->setCellValue('L7', "Deskripsi")
            ->setCellValue('M7', "Tanggal Bayar")
            ->setCellValue('N7', "Nominal")
            ->setCellValue('O7', "Bank")
            ->setCellValue('P7', "Notes")
            ->setCellValue('Q6', "Total Order")
            ->setCellValue('R6', "Total Dana Masuk")
            ->setCellValue('S6', "Sisa Pembayaran")
            ;
        $phpSpreadSheet->getCell('D6')->setValue("Jumlah\nParticipant");
        $phpSpreadSheet->getStyle('D6')->getAlignment()->setWrapText(true);

        $phpSpreadSheet->getStyle('A'.(self::START_ROW - 2).':' .self::END_COLUMN.(self::START_ROW - 1))
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function startCell(): string
    {
        return 'A' . self::START_ROW;
    }
}
