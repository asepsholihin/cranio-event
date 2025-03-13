<?php
namespace App\File\PDF;

use App\Models\InvoiceUmrohTrip;
use App\Models\OrderItemUmrohTrip;
use App\Models\OrderUmrohTrip;
use App\Models\RefundOrderUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\DiscountOrderUmrohTrip;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class RefundBookingSeatPDF
{
    private $pdf;
    private $refund;

    public function __construct(OrderUmrohTrip $order, RefundOrderUmrohTrip $refund)
    {
        App::setLocale('id');
        $this->refund = $refund;
        $orderItems = OrderItemUmrohTrip::where('order_umroh_trip_id', $order->id)->orderByRaw('package_umroh_trip_id ASC, id ASC')->get()->toArray();
        $umrohTrip = UmrohTrip::select(['currency', 'departure_at'])->find($order->umroh_trip_id);
        $currency = $umrohTrip->currency;
        $maxPaidAt = Carbon::createFromFormat('Y-m-d', $umrohTrip->departure_at)->subDays(35);
        $refunds = RefundOrderUmrohTrip::where('order_umroh_trip_id', $order->id)
        ->where('id', '<=', $refund->id)
        ->orderBy('id')
        ->get();

        $invoices = InvoiceUmrohTrip::where('order_umroh_trip_id', $order->id)
            ->orderBy('id')
            ->get();

        if($order->total_discount > 0) {
            $discount_per_pax = ($order->total_discount/$order->total_pax_trip);
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
        if($discounts) {
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

        $orderItems = json_decode(json_encode($orderItems));

        $data = [
            'order' => $order,
            'refund' => $refund,
            'invoices' => $invoices,
            'currency' => $currency,
            'maxPaidAt' => $maxPaidAt,
            'refunds' => $refunds,
            'orderItems' => $orderItems
        ];

        $this->pdf = PDF::loadView('pdf.refund', $data);
        $this->pdf->setOption('enable-local-file-access', true);
    }

    public function download()
    {
        return $this->pdf->download(strtoupper($this->refund->name).'_REFUND_'.$this->refund->refund_no.'.pdf');
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
