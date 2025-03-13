<?php
namespace App\File\PDF;

use App\Models\InvoiceUmrohTrip;
use App\Models\OrderItemUmrohTrip;
use App\Models\OrderUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\WebSale;
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class ReceiptPDF
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
        $umrohTrip = UmrohTrip::join('package_umroh_trips', 'umroh_trips.id' ,'package_umroh_trips.umroh_trip_id')->select(['title','umroh_trips.category_id', 'package_umroh_trips.name as package_name', 'currency'])->find($order->umroh_trip_id);
        $currency = "IDR";
        if($umrohTrip) {
            $currency = $umrohTrip->currency;
        }
        $orderItems = OrderItemUmrohTrip::join('package_umroh_trips', 'order_item_umroh_trips.package_umroh_trip_id' ,'package_umroh_trips.id')->where('order_umroh_trip_id', $order->id)->select('package_umroh_trips.name')->groupBy('package_umroh_trips.name')->get();
        $totalPax = OrderItemUmrohTrip::where('order_umroh_trip_id', $order->id)->whereNotNull('room_type')->sum('pax');
        $sales = WebSale::select('sales_name', 'whatsapp_number')->find($order->sales_id);

        $data = [
            'order' => $order,
            'invoice' => $invoice,
            'currency' => self::CURRENCIES[$currency],
            'umrohTrip' => $umrohTrip,
            'orderItems' => $orderItems,
            'totalPax' => $totalPax,
            'sales' => $sales
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.receipt', $data);
        $this->pdf->setOption('enable-local-file-access', true)
            ->setOption('page-width', '16cm')
            ->setOption('page-height', '23cm')
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
        return view('pdf.receipt', $this->data)->render();
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
