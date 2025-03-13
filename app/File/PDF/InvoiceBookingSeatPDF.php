<?php

namespace App\File\PDF;

use App\Models\InvoiceUmrohTrip;
use App\Models\OrderItemUmrohTrip;
use App\Models\OrderUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\RefundOrderUmrohTrip;
use App\Models\DiscountOrderUmrohTrip;
use App\Models\OrderSpecialRequest;
use App\Models\WebSale;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use DB;

class InvoiceBookingSeatPDF
{
    private $pdf;
    private $invoice;
    private $data;
    
    public function __construct(OrderUmrohTrip $order, InvoiceUmrohTrip $invoice)
    {
        App::setLocale('id');
        $this->invoice = $invoice;
        $orderItems = OrderItemUmrohTrip::where('order_umroh_trip_id', $order->id)->orderByRaw('package_umroh_trip_id ASC, id ASC')->get()->toArray();
        $umrohTrip = UmrohTrip::select(['currency', 'departure_at', 'category_id','invoice_due_date'])->find($order->umroh_trip_id);
        $currency = "IDR";
        $tripCategory = 1;
        $tripSubCategory = 5;
        $maxPaidAt = "";
        if($umrohTrip) {
            $package = DB::table('package_umroh_trips')->where('id', $orderItems[0]['package_umroh_trip_id'])->first();
            $tripSubCategory = $package->sub_category_id ?? 5;
            $currency = $umrohTrip->currency;
            $tripCategory = $umrohTrip->category_id;
            $maxPaidAt = Carbon::createFromFormat('Y-m-d', $umrohTrip->departure_at)->subDays($umrohTrip->invoice_due_date);
        }
        $invoices = InvoiceUmrohTrip::where('order_umroh_trip_id', $order->id)
            ->where('id', '<=', $invoice->id)
            ->where('status', '!=', 3)
            ->orderBy('id')
            ->get();

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
            array_push($orderItems, $discountItem);
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
                array_push($orderItems, $discountItem);
            }
        }

        // $requests = OrderSpecialRequest::where('order_umroh_trip_id', $order->id)->orderBy('id')->get();
        // if ($requests) {
        //     foreach ($requests as $request) {
        //         $requestItem = array(
        //             "id" => $request->id,
        //             "booking_pax" => 0,
        //             "description" => $request->description,
        //             "pax" => $request->pax,
        //             "price" => 0,
        //             "total_price" => 0,
        //             "price_per_pax" => null,
        //         );
        //         array_push($orderItems, $requestItem);
        //     }
        // }

        $orderItems = json_decode(json_encode($orderItems));

        $refunds = RefundOrderUmrohTrip::where('order_umroh_trip_id', $order->id)
            ->orderBy('id')
            ->get();

        $sales = WebSale::select('sales_name', 'whatsapp_number')->find($order->sales_id);

        $data = [
            'order' => $order,
            'invoice' => $invoice,
            'orderItems' => $orderItems,
            'invoices' => $invoices,
            'refunds' => $refunds,
            'currency' => $currency,
            'maxPaidAt' => $maxPaidAt,
            'invoiceDueDate' => $umrohTrip->invoice_due_date ?? 35,
            'tripCategory' => $tripCategory,
            'tripSubCategory' => $tripSubCategory,
            'sales' => $sales
        ];
        $this->data = $data;
        //return view('invoice.booking-seat', $data)->render();
        $this->pdf = PDF::loadView('pdf.invoice', $data);
        $this->pdf->setOption('enable-local-file-access', true)
        ->setOption('page-width', '21cm')
        ->setOption('page-height', '29.7cm')
        ->setOption('margin-top', '1cm')
        ->setOption('margin-left', '1cm')
        ->setOption('margin-right', '1cm')
        ->setOption('margin-bottom', '1cm');
    }

    public function download()
    {
        return $this->pdf->download(strtoupper($this->invoice->name) . '_INVOICE_' . $this->invoice->invoice_no . '.pdf');
    }

    public function html()
    {
        return view('pdf.invoice', $this->data)->render();
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
