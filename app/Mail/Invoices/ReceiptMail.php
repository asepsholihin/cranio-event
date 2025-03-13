<?php

namespace App\Mail\Invoices;

use App\File\PDF\InvoiceBookingSeatPDF;
use App\File\PDF\ReceiptPDF;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $invoice;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $invoice)
    {
        $this->order = $order;
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $receiptPDF = new ReceiptPDF($this->order, $this->invoice);
        $receiptNo = str_replace('INV', 'KWT', $this->invoice->invoice_no);
        return $this
            ->subject("Jejak Imani - Receipt No {$receiptNo}")
            ->from('no-reply@jejakimani.com', 'Jejak Imani')
            ->view('emails.invoices.receipt')
            ->attachData($receiptPDF->output(), "{$receiptNo}.pdf", [
                'mime' => 'application/pdf',
            ]);
    }
}
