<?php

namespace App\Exports;

use App\Models\OrderUmrohTrip;
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

class BookingOrderExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents
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
        return $rows->transform(function ($orderUmrohTrip) {
            return $orderUmrohTrip;
        });
    }

    public function headings(): array
    {
        return [
            'No.',
            'Tanggal Booking',
            'Kode Booking',
            'Nama Akun',
            'Nomor HP',
            'Keberangkatan',
            'Total Pax',
            'Nama Sales',
            'Status',
            'Total Amount',
            'Due Amount',
        ];
    }

    /**
    * @var OrderUmrohTrip $orderUmrohTrip
    */
    public function map($orderUmrohTrip): array
    {
        $this->rowNumber += 1;
        $status = "";
        if (request()->query('statusOrder','') == 'need_payment_xendit') {
            $status = "Belum bayar xendit";
        }
        if (request()->query('statusOrder','') == 'need_verify_finance') {
            $status = "Belum verifikasi finance";    
        }
        if (request()->query('statusOrder','') == 'need_upload_credit') {
            $status = "Belum upload bukti transfer";  
        }
        if (request()->query('statusOrder','') == 'need_pax_assign') {
            $status = "Belum pax assign";  
        }
        if (request()->query('statusOrder','') == 'need_reference') {
            $status = "Belum set reference";  
        }
        return [
            $this->rowNumber,
            ($orderUmrohTrip->transaction_date) ? Carbon::parse($orderUmrohTrip->transaction_date)->isoFormat('D MMMM Y') : null,
            $orderUmrohTrip->order_no,
            $orderUmrohTrip->name,
            $orderUmrohTrip->no_hp,
            $orderUmrohTrip->umroh_trip_title,
            $orderUmrohTrip->total_pax_trip,
            $orderUmrohTrip->sales_name,
            $status,
            $orderUmrohTrip->total_payment,
            $orderUmrohTrip->due_payment
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = OrderUmrohTrip::join('invoice_umroh_trips', 'order_umroh_trips.initial_invoice', '=', 'invoice_umroh_trips.invoice_no')
        ->leftjoin('umroh_trips', 'order_umroh_trips.umroh_trip_id', '=', 'umroh_trips.id')
        ->select(['order_umroh_trips.*', 'umroh_trips.title as umroh_trip_title']);

            // DATE_PART(\'MONTH\', AGE(CURRENT_DATE, order_umroh_trips.created_at))
        
        if (! empty(request()->query('umrohTripId'))) {
            $query->where('order_umroh_trips.umroh_trip_id', request()->query('umrohTripId'));
        }

        if (request()->query('statusPayment','') == 'unpaid_dp') {
            $query->where('paid', '<', self::DOWN_PAYMENT);
        }

        if (request()->query('statusPayment','') == 'unpaid') {
            $query->where('order_umroh_trips.due_payment', '>', 0);
        }

        if (request()->query('statusPayment','') == 'unpaid_h35') {
            $query->whereRaw("umroh_trips.departure_at <= now() + INTERVAL '35 DAYS' and order_umroh_trips.due_payment > 0");
        }
        
        if (request()->query('statusPayment','') == 'paid') {
            $query->where('order_umroh_trips.due_payment', '<=', 0);
        }
        
        if (request()->query('salesId')) {
            $query->where('order_umroh_trips.sales_id', request()->query('salesId'));
        }
        
        if (request()->query('salesName')) {
            $salesName = request()->query('salesName');
            if(request()->query('salesName') == "empty") {
                $salesName = "";
            }
            
            if(request()->query('salesName') == "Rekomendasi") {
                $queryRecommendation = WebSale::select(['sales_name','department_id']);
                $queryRecommendation->join('users', 'users.id', 'web_sales.user_id')->where('users.department_id', '!=', 3);
                $ordersRecommendation = $queryRecommendation->groupBy('sales_name','department_id')->pluck('sales_name');
                $query->whereIn('sales_name', $ordersRecommendation);
            } else {
                $query->where('order_umroh_trips.sales_name', $salesName);
            }
        }
        
        if (request()->query('month')) {
            $date = Carbon::parse(request()->query('month')."-01");
            $start = $date->startOfMonth()->format('Y-m-d H:i:s');
            $end = $date->endOfMonth()->format('Y-m-d H:i:s');
            $query->whereBetween('umroh_trips.departure_at', [$start, $end]);
        }

        if(request()->query('date')) {
            $dateXplode = explode('to', request()->query('date'));
            $exDateStart = str_replace('/','-',$dateXplode[0]);
            if(isset($dateXplode[1]))
                $exDateEnd = str_replace('/','-',$dateXplode[1]);

            $start = date('Y-m-d 00:00:00', strtotime($exDateStart));
            $end = date('Y-m-d 24:00:00', strtotime($exDateEnd??$exDateStart));
            $query->whereBetween('order_umroh_trips.created_at', [$start, $end]);
        }

        if (request()->query('paymentMethod','') == 'xendit') {
            $query->where('order_umroh_trips.book_with_xendit', true);
        }
        if (request()->query('paymentMethod','') == 'manual') {
            $query->where('order_umroh_trips.book_with_xendit', false);
        }

        if (request()->query('badalUmroh','') == 'badal') {
            $query->where('order_umroh_trips.is_badal', true);
        }

        if (request()->query('statusOrder','') == 'need_payment_xendit') {
            $query->where(DB::raw('(SELECT (CASE WHEN invoice_url IS NOT NULL and status=1 THEN 1 ELSE 0 END) FROM invoice_umroh_trips as iut WHERE iut.order_umroh_trip_id=order_umroh_trips.id and iut.deleted_at IS NULL ORDER BY id DESC limit 1)'), '>', 0);
        }
        if (request()->query('statusOrder','') == 'need_verify_finance') {
            $query->where(DB::raw('(SELECT (CASE WHEN invoice_url IS NULL and status=1 and credit_image_receipt IS NOT NULL THEN 1 ELSE 0 END) FROM invoice_umroh_trips as iut WHERE iut.order_umroh_trip_id=order_umroh_trips.id and iut.deleted_at IS NULL ORDER BY id DESC limit 1)'), '>', 0);
        }
        if (request()->query('statusOrder','') == 'need_upload_credit') {
            $query->where(DB::raw('(SELECT (CASE WHEN invoice_url IS NULL and status=1 and credit_image_receipt IS NULL THEN 1 ELSE 0 END) FROM invoice_umroh_trips as iut WHERE iut.order_umroh_trip_id=order_umroh_trips.id and iut.deleted_at IS NULL ORDER BY id DESC limit 1)'), '>', 0);
        }
        if (request()->query('statusOrder','') == 'need_pax_assign') {
            $query->where(DB::raw('(CASE WHEN paid > 0 and booking_pax_trip > 0 AND is_badal=false THEN 1 ELSE 0 END)'), '>', 0);
        }
        if (request()->query('statusOrder','') == 'need_reference') {
            $query->where(DB::raw('(CASE WHEN refer_participant_id IS NULL AND is_badal=false THEN 1 ELSE 0 END)'), '>', 0);
        }

        if (request()->query('initialInvoicePaid','') == true) {
            $query->leftjoin('credit_image_receipts', 'invoice_umroh_trips.id', 'credit_image_receipts.invoice_umroh_trip_id');
            $query->where(function($q){
                $q->where('order_umroh_trips.paid', '>', 0);
                $q->orwhereNotNull('credit_image_receipts.id');
            });
        }

        if (request()->query('category')) {
            if(request()->query('category') == "umroh") {
                $query->where('umroh_trips.category_id', 1);
            }
            if(request()->query('category') == "haji") {
                $query->where('umroh_trips.category_id', 2);
            }
            if(request()->query('category') == "badal") {
                $query->where('order_umroh_trips.is_badal', true);
            }
            if(request()->query('category') == "jelajah_dunya") {
                $query->where('umroh_trips.category_id', 3);
            }
        }

        // Bukan Kantor Pusat
        if(in_array(8, auth()->user()->department_ids)) {
            // Kepala Cabang
            $query->join('users', 'users.id', 'order_umroh_trips.created_by_user_id');
            $query->where('users.office_id', auth()->user()->office_id);
        } else {
            if(in_array(3, auth()->user()->department_ids)) {
                $query->where('order_umroh_trips.created_by_user_id', auth()->user()->id);
            }
        }
        $query->orderBy('id', 'DESC');
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
