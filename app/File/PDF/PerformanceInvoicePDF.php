<?php

namespace App\File\PDF;

use App\Models\LogPerformanceInvoice;
use App\Models\SalesLead;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class PerformanceInvoicePDF
{
    private $pdf;
    private $data;

    public function __construct($salesLeadId)
    {
        App::setLocale('id');
        $invoice = SalesLead::find($salesLeadId);
        $items = LogPerformanceInvoice::
        select(['log_performance_invoices.*', 'umroh_trips.title', 'umroh_trips.category_id', 'umroh_trips.currency', 'package_umroh_trips.name as package_name', 'umroh_trips.invoice_due_date'])
        ->join('umroh_trips', 'umroh_trips.id', 'log_performance_invoices.umroh_trip_id')
        ->join('package_umroh_trips', 'package_umroh_trips.id', 'log_performance_invoices.package_umroh_trip_id')
        ->where('sales_lead_id', $salesLeadId)->get();

        $currency = $items[0]->currency;
        $data = [
            'invoice' => $invoice,
            'items' => $items,
            'currency' => $currency,
        ];
        
        $this->data = $data;

        // return view('pdf.finance.invoice', $data)->render();
        $this->pdf = PDF::loadView('pdf.performance_invoice', $data);
        $this->pdf->setOption('enable-local-file-access', true);
    }

    public function html()
    {
        return view('pdf.performance_invoice', $this->data)->render();
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
