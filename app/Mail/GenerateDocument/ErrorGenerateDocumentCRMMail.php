<?php

namespace App\Mail\GenerateDocument;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\UmrohTrip;

class ErrorGenerateDocumentCRMMail extends Mailable
{
    use Queueable, SerializesModels;

    public $document;
    public $errorMessage;
    public $umrohTrip;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($document, $errorMessage, $umrohTrip)
    {
        $this->document = $document;
        $this->errorMessage = $errorMessage;
        $this->umrohTrip = $umrohTrip;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->document == "certificate") {
            $subject = "Jejak Imani - Certificate {$this->umrohTrip}";
        }

        return $this
            ->subject($subject)
            ->from('no-reply@jejakimani.com', 'Jejak Imani')
            ->view('emails.generate-document.error-generate-document');
    }
}
