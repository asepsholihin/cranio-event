<?php
namespace App\File\PDF;

use App\Models\UmrohTrip;
use App\Models\OrderUmrohTrip;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class InvoiceBookingSeatEmptyPDF
{
    private $pdf;
    private $data;

    public function __construct(OrderUmrohTrip $order)
    {
        App::setLocale('id');
        $umrohTrip = UmrohTrip::select(['currency', 'departure_at','invoice_due_date'])->find($order->umroh_trip_id);
        $currency = $umrohTrip->currency;
        $tripCategory = $umrohTrip->category_id;
        $maxPaidAt = Carbon::createFromFormat('Y-m-d', $umrohTrip->departure_at)->subDays($umrohTrip->invoice_due_date);
        
        $data = [
            'order' => $order,
            'currency' => $currency,
            'maxPaidAt' => $maxPaidAt,
            'tripCategory' => $tripCategory,
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.empty_invoice', $data);
        $this->pdf->setOption('enable-local-file-access', true);
    }

    public function download()
    {
        return $this->pdf->download('EMPTY_INVOICE.pdf');
    }

    public function html()
    {
        return view('pdf.empty_invoice', $this->data)->render();
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
