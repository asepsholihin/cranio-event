<?php
namespace App\File\PDF;

use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class PurchaseOrderDocument
{
    private $pdf;
    private $purchaseRequest;
    private $data;

    public function __construct($purchaseOrder)
    {
        App::setLocale('id');
        $this->purchaseOrder = $purchaseOrder;

        $data = [
            'purchaseOrder' => $purchaseOrder,
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.purchase_order_document', $data);
        $this->pdf->setOption('enable-local-file-access', true)
                   ->setOption('page-size', 'A4')
                   ->setOption('margin-bottom', '2cm')
                   ->setOption('footer-html', view('pdf._footer_title'));
    }

    public function download()
    {
        return $this->pdf->download('PURCHASE ORDER '.strtoupper($this->purchaseRequest->purchase_request_number).'.pdf');
    }

    public function html()
    {
        return view('pdf.purchase_order_document', $this->data)->render();
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
