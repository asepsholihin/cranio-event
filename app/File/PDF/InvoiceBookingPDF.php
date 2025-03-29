<?php

namespace App\File\PDF;

use App\Models\Booking;
use App\Models\Participant;
use Carbon\Carbon;
use Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class InvoiceBookingPDF
{
    private $pdf;
    private $booking;
    private $data;

    public function __construct(Booking $booking)
    {
        App::setLocale('id');
        $this->booking = $booking;
        $participants = Participant::join('participant_bookings', 'participant_bookings.participant_id', 'participants.id')->where('participant_bookings.booking_id', $booking->id)->get();
        
        $currency = "Rp";
        $data = [
            'booking' => $booking,
            'participants' => $participants,
            'currency' => $currency,
        ];
        $this->data = $data;

        // return view('pdf.invoice-booking', $data)->render();
        $this->pdf = Pdf::loadView('pdf.invoice-booking', $data);
    }

    public function html()
    {
        return view('pdf.invoice-booking', $this->data)->render();
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
