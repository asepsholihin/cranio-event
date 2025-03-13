<?php
namespace App\File\PDF;

use App\Models\InvoiceUmrohTrip;
use App\Models\OrderItemUmrohTrip;
use App\Models\OrderUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\RefundOrderUmrohTrip;
use App\Models\DiscountOrderUmrohTrip;
use App\Models\ParticipantUmrohTrip;
use App\Models\WebSale;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class InvoiceBookingSeatParticipantPDF
{
    private $pdf;
    private $invoice;

    public function __construct(OrderUmrohTrip $order, InvoiceUmrohTrip $invoice, $participantId)
    {
        App::setLocale('id');
        $this->invoice = $invoice;
        $participantUmrohTrip = ParticipantUmrohTrip::select('participant.name','participant_umroh_trips.infants')->where('participant_umroh_trips.id', $participantId)->join('participant', 'participant.id', 'participant_umroh_trips.participant_id')->first();
        if($participantUmrohTrip->infants == 2) {
            $orderItems = OrderItemUmrohTrip::select('order_item_umroh_trips.*')->join('participant_umroh_trips', 'order_item_umroh_trips.id', 'participant_umroh_trips.order_item_umroh_trip_id')->where('participant_umroh_trips.id', $participantId)->where('order_item_umroh_trips.order_umroh_trip_id', $order->id)->where('pax_infants', 0)->whereNotNull('order_item_umroh_trips.room_type')->orderByRaw('order_item_umroh_trips.package_umroh_trip_id ASC, order_item_umroh_trips.id ASC')->get();
        } else {
            $orderItems = OrderItemUmrohTrip::select('order_item_umroh_trips.*')->join('participant_umroh_trips', 'order_item_umroh_trips.id', 'participant_umroh_trips.order_item_umroh_trip_id')->where('participant_umroh_trips.id', $participantId)->where('order_item_umroh_trips.order_umroh_trip_id', $order->id)->where('pax_infants', 1)->whereNotNull('order_item_umroh_trips.room_type')->orderByRaw('order_item_umroh_trips.package_umroh_trip_id ASC, order_item_umroh_trips.id ASC')->get();
        }
        foreach ($orderItems as $item) {
            $infants = 2;
            if($item->pax_infants == 1) {
                $infants = 1;
            }
            if($item->room_type != null) {
                $item->price = intval(ParticipantUmrohTrip::where('order_item_umroh_trip_id', $item->id)->where('infants', $infants)->first()->price_per_pax ?? 0);
            } else {
                $item->price = $item->price;
            }
        }
        $orderItems = $orderItems->toArray();

        $orderItemsParticipant = OrderItemUmrohTrip::where('order_umroh_trip_id', $order->id)->where('assigned_participant', 'iLIKE', '%' . $participantId . '%')->orderByRaw('package_umroh_trip_id ASC, id ASC')->get()->toArray();
        $orderItems = array_merge($orderItems, $orderItemsParticipant);

        $umrohTrip = UmrohTrip::select(['currency', 'departure_at','category_id','invoice_due_date'])->find($order->umroh_trip_id);
        $currency = "IDR";
        $tripCategory = 1;
        $maxPaidAt = "";
        if($umrohTrip) {
            $currency = $umrohTrip->currency;
            $tripCategory = $umrohTrip->category_id;
            $maxPaidAt = Carbon::createFromFormat('Y-m-d', $umrohTrip->departure_at)->subDays($umrohTrip->invoice_due_date);
        }
        $invoices = InvoiceUmrohTrip::where('order_umroh_trip_id', $order->id)
            ->where('id', '<=', $invoice->id)
            ->where('status', '!=', 3)
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
        
        $discounts = DiscountOrderUmrohTrip::where('order_umroh_trip_id', $order->id)->where('assigned_participant', 'iLIKE', '%' . $participantId . '%')->orderBy('id')->get();
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
        
        $refunds = RefundOrderUmrohTrip::where('order_umroh_trip_id', $order->id)
        ->orderBy('id')
        ->get();

        $sales = WebSale::select('sales_name', 'whatsapp_number')->find($order->sales_id);

        $data = [
            "participant" => $participantUmrohTrip,
            'order' => $order,
            'invoice' => $invoice,
            'orderItems' => $orderItems,
            'invoices' => $invoices,
            'refunds' => $refunds,
            'currency' => $currency,
            'maxPaidAt' => $maxPaidAt,
            'invoiceDueDate' => $umrohTrip->invoice_due_date ?? 35,
            'tripCategory' => $tripCategory,
            'sales' => $sales
        ];

        //return view('invoice.booking-seat', $data)->render();
        $this->pdf = PDF::loadView('pdf.invoice_participant', $data);
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
        return $this->pdf->download(strtoupper($this->invoice->name).'_INVOICE_'.$this->invoice->invoice_no.'.pdf');
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
