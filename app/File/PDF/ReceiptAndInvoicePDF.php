<?php
namespace App\File\PDF;

use App\Models\InvoiceUmrohTrip;
use App\Models\OrderItemUmrohTrip;
use App\Models\OrderUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\RefundOrderUmrohTrip;
use App\Models\DiscountOrderUmrohTrip;
use App\Models\WebSale;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use PDF;
use DB;

class ReceiptAndInvoicePDF
{
    const CURRENCIES = [
        'IDR' => 'Rp',
        'USD' => 'USD',
    ];

    private $pdf;
    private $data;
    private $invoice;

    public function __construct(OrderUmrohTrip $order, InvoiceUmrohTrip $invoice)
    {
        App::setLocale('id');
        $this->invoice = $invoice;
        $umrohTrip = UmrohTrip::join('package_umroh_trips', 'umroh_trips.id' ,'package_umroh_trips.umroh_trip_id')
        ->select(['title','umroh_trips.category_id', 'package_umroh_trips.name as package_name', 'currency','departure_at','invoice_due_date'])->find($order->umroh_trip_id);
        $currency = "IDR";
        if($umrohTrip) {
            $currency = $umrohTrip->currency;
        }
        $orderItems = OrderItemUmrohTrip::join('package_umroh_trips', 'order_item_umroh_trips.package_umroh_trip_id' ,'package_umroh_trips.id')->where('order_umroh_trip_id', $order->id)->select(['package_umroh_trips.name','package_umroh_trips.id as package_umroh_trip_id'])->groupBy('package_umroh_trips.id')->get();
        $totalPax = OrderItemUmrohTrip::where('order_umroh_trip_id', $order->id)->whereNotNull('room_type')->sum('pax');
        $invoices = InvoiceUmrohTrip::where('order_umroh_trip_id', $order->id)
            ->where('id', '<=', $invoice->id)
            ->where('status', '!=', 3)
            ->orderBy('id')
            ->get();
        $refunds = RefundOrderUmrohTrip::where('order_umroh_trip_id', $order->id)
            ->orderBy('id')
            ->get();
        $tripCategory = 1;
        $tripSubCategory = 5;
        $maxPaidAt = "";
        $currency = "IDR";
        if($umrohTrip) {
            $tripSubCategory = 5;
            if(count($orderItems) > 0) {
                $package = DB::table('package_umroh_trips')->where('id', $orderItems[0]['package_umroh_trip_id'])->first();
                $tripSubCategory = $package->sub_category_id;
            }
            $currency = $umrohTrip->currency;
            $tripCategory = $umrohTrip->category_id;
            $maxPaidAt = Carbon::createFromFormat('Y-m-d', $umrohTrip->departure_at)->subDays($umrohTrip->invoice_due_date);
        }

        // Order Item for Invoice
        $orderItemsForInvoice = OrderItemUmrohTrip::where('order_umroh_trip_id', $order->id)->orderByRaw('package_umroh_trip_id ASC, id ASC')->get()->toArray();
        if ($order->total_discount > 0) {
            $discount_per_pax = ($order->total_discount / $order->total_pax_trip);
            $discountItem = array(
                "id" => 0,
                "booking_pax" => 0,
                "description" => $order->discount_notes,
                "pax" => $order->total_pax_trip,
                "price" => -1 * $discount_per_pax,
                "total_price" => (int) -1 * $order->total_discount,
                "room_type" => "-",
            );
            array_push($orderItemsForInvoice, $discountItem);
        }

        $discounts = DiscountOrderUmrohTrip::where('order_umroh_trip_id', $order->id)->orderBy('id')->get();
        if ($discounts) {
            foreach ($discounts as $discount) {
                $discountItem = array(
                    "id" => $discount->id,
                    "booking_pax" => 0,
                    "description" => $discount->discount_notes,
                    "pax" => $discount->pax,
                    "price" => (int) -1 * $discount->discount_per_pax,
                    "total_price" => (int) -1 * $discount->total_discount,
                    "room_type" => "-",
                );
                array_push($orderItemsForInvoice, $discountItem);
            }
        }
        $orderItemsForInvoice = json_decode(json_encode($orderItemsForInvoice));

        $sales = WebSale::select('sales_name', 'whatsapp_number')->find($order->sales_id);

        $data = [
            'order' => $order,
            'invoice' => $invoice,
            'currency' => self::CURRENCIES[$currency],
            'umrohTrip' => $umrohTrip,
            'orderItems' => $orderItems,
            'totalPax' => $totalPax,
            'invoices' => $invoices,
            'refunds' => $refunds,
            'orderItemsForInvoice' => $orderItemsForInvoice,
            'maxPaidAt' => $maxPaidAt,
            'invoiceDueDate' => $umrohTrip->invoice_due_date ?? 35,
            'tripCategory' => $tripCategory,
            'tripSubCategory' => $tripSubCategory,
            'sales' => $sales
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.receipt_and_invoice', $data);
        $this->pdf->setOption('enable-local-file-access', true)
            ->setOption('margin-top', '0cm')
            ->setOption('margin-left', '0cm')
            ->setOption('margin-right', '0cm')
            ->setOption('margin-bottom', '0cm');
    }

    public function download()
    {
        return $this->pdf->download(strtoupper($this->invoice->name).'_RECEIPT_'.str_replace('INV', 'KWT', str_replace('/','_',$this->invoice->invoice_no)).'.pdf');
    }

    public function html()
    {
        return view('pdf.receipt_and_invoice', $this->data)->render();
    }

    public function output()
    {
        return $this->pdf->output();
    }

    public function stream()
    {
        return $this->pdf->stream();
    }
}
