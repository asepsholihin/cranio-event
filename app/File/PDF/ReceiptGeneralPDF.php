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

class ReceiptGeneralPDF
{
    const CURRENCIES = [
        'IDR' => 'Rp',
        'USD' => 'USD',
    ];

    private $pdf;
    private $data;

    public function __construct($order)
    {
        App::setLocale('id');
        $currency = "IDR";

        $data = [
            'order' => $order,
            'currency' => self::CURRENCIES[$currency],
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.receipt_general', $data);
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
        return view('pdf.receipt_general', $this->data)->render();
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
