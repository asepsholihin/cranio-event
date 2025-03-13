<?php

namespace App\Mail\Refund;

use App\File\PDF\RefundBookingSeatPDF;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingSeatRefundMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $refund;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $refund)
    {
        $this->order = $order;
        $this->refund = $refund;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $invocePDF = new RefundBookingSeatPDF($this->order, $this->refund);
        return $this
            ->subject("Jejak Imani - Refund No {$this->refund->refund_no}")
            ->from('no-reply@jejakimani.com', 'Jejak Imani')
            ->view('emails.refund.booking-order')
            ->attachData($invocePDF->output(), "{$this->refund->refund_no}.pdf", [
                'mime' => 'application/pdf',
            ]);
    }
}
