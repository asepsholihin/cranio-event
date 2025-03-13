<?php

namespace App\Mail\GenerateDocument;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenerateDocumentSurveyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $documentLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $documentLink)
    {
        $this->subject = $subject;
        $this->documentLink = $documentLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->subject;
        
        return $this
            ->subject($subject)
            ->from('no-reply@jejakimani.com', 'Jejak Imani')
            ->view('emails.generate-document.generate-document');
    }
}
