<?php

namespace App\Mail\GenerateDocument;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ErrorJobMail extends Mailable
{
    use Queueable, SerializesModels;

    public $errorMessage;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
    
        $subject = "Jejak Imani - Job Worker";

        return $this
            ->subject($subject)
            ->from('no-reply@jejakimani.com', 'Jejak Imani')
            ->view('emails.error-job-message');
    }
}
