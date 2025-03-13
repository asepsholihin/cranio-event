<?php

namespace App\Mail\Invoices;

use App\File\PDF\InvoiceBookingSeatPDF;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingSeatInvoiceMail extends Mailable
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
        $invocePDF = new InvoiceBookingSeatPDF($this->order, $this->invoice);
        return $this
            ->subject("Jejak Imani - Invoice No {$this->invoice->invoice_no}")
            ->from('no-reply@jejakimani.com', 'Jejak Imani')
            ->view('emails.invoices.booking-order')
            ->attachData($invocePDF->output(), "{$this->invoice->invoice_no}.pdf", [
                'mime' => 'application/pdf',
            ]);
    }
}
