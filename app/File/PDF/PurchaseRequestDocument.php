<?php
namespace App\File\PDF;

use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class PurchaseRequestDocument
{
    private $pdf;
    private $purchaseRequest;
    private $data;

    public function __construct($purchaseRequest)
    {
        App::setLocale('id');
        $this->purchaseRequest = $purchaseRequest;

        $data = [
            'purchaseRequest' => $purchaseRequest,
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.purchase_request_document', $data);
        $this->pdf->setOption('enable-local-file-access', false);
    }

    public function download()
    {
        return $this->pdf->download('PURCHASE REQUEST '.strtoupper($this->purchaseRequest->purchase_request_number).'.pdf');
    }

    public function html()
    {
        return view('pdf.purchase_request_document', $this->data)->render();
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
