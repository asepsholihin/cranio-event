<?php

namespace App\File\PDF\Finance;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class InvoicePDF
{
    private $pdf;
    private $invoice;
    private $data;

    public function __construct(Invoice $invoice)
    {
        App::setLocale('id');
        $this->invoice = $invoice;
        $items = InvoiceDetail::where('invoice_id', $invoice->id)->orderByRaw('description ASC, id ASC')->get()->toArray();
        $maxPaidAt = Carbon::parse($invoice->created_at)->addDays(35);
        $items = json_decode(json_encode($items));
        $currency = "Rp";
        $data = [
            'invoice' => $invoice,
            'items' => $items,
            'currency' => $currency,
            'maxPaidAt' => $maxPaidAt,
        ];
        $this->data = $data;

        // return view('pdf.finance.invoice', $data)->render();
        $this->pdf = PDF::loadView('pdf.finance.invoice', $data);
        $this->pdf->setOption('enable-local-file-access', true);
    }

    public function html()
    {
        return view('pdf.finance.invoice', $this->data)->render();
    }

    public function download()
    {
        return $this->pdf->download(strtoupper($this->invoice->name) . '_INVOICE_' . $this->invoice->invoice_no . '.pdf');
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
