<?php

namespace App\Mail\GenerateLetter;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\UmrohTrip;

class GenerateLetterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $documentLink;
    public $umrohTripId;
    public $page;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($documentLink, $umrohTripId, $page=null)
    {
        $this->documentLink = $documentLink;
        $this->umrohTripId = $umrohTripId;
        $this->page = $page;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $part = "";
        if(!empty($this->page)) {
            $part = " Part " . $this->page; 
        }

        $umrohTrip = UmrohTrip::findOrFail($this->umrohTripId);
        $subject = "Surat Jejak Imani - {$umrohTrip->title}" . $part;
        
        return $this
            ->subject($subject)
            ->from('no-reply@jejakimani.com', 'Jejak Imani')
            ->view('emails.generate-letter.generate-letter');
    }
}
