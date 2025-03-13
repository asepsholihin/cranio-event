<?php

namespace App\File\PDF\Finance;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
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
    private $invoice;
    private $data;

    public function __construct(Invoice $invoice)
    {
        App::setLocale('id');
        $this->invoice = $invoice;
        $items = InvoiceDetail::where('invoice_id', $invoice->id)->orderByRaw('description ASC, id ASC')->get()->toArray();
        $items = json_decode(json_encode($items));
        $currency = self::CURRENCIES['IDR'];
        $data = [
            'invoice' => $invoice,
            'items' => $items,
            'currency' => $currency,
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.finance.receipt', $data);
        $this->pdf->setOption('enable-local-file-access', true)
            ->setOption('page-width', '16cm')
            ->setOption('page-height', '23cm')
            ->setOption('margin-top', '0cm')
            ->setOption('margin-left', '0cm')
            ->setOption('margin-right', '0cm')
            ->setOption('margin-bottom', '0cm');
    }

    public function html()
    {
        return view('pdf.finance.receipt', $this->data)->render();
    }

    public function download()
    {
        return $this->pdf->download(strtoupper($this->invoice->name) . '_RECEIPT_' . str_replace('INV', 'KWT', str_replace('/', '_', $this->invoice->invoice_no)) . '.pdf');
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
