<?php

namespace App\Mail\GenerateLetter;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\UmrohTrip;

class ErrorGenerateLetterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $errorMessage;
    public $umrohTripId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($errorMessage, $umrohTripId)
    {
        $this->errorMessage = $errorMessage;
        $this->umrohTripId = $umrohTripId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        
        $umrohTrip = UmrohTrip::findOrFail($this->umrohTripId);
        $subject = "Surat Jejak Imani - {$umrohTrip->title}";

        return $this
            ->subject($subject)
            ->from('no-reply@jejakimani.com', 'Jejak Imani')
            ->view('emails.generate-letter.error-generate-letter');
    }
}
