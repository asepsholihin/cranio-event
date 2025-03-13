<?php

namespace App\Mail\Auth;

use App\Models\VerificationEmailPhoneParticipant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEMailByCode extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $verify;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(VerificationEmailPhoneParticipant $verify)
    {
        $this->verify = $verify;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->verify->increment('sent_times');
        return $this
        ->subject("Kode Verifikasi - Jejak Imani")
        ->from('no-reply@jejakimani.com', 'Jejak Imani')
        ->view('emails.auth.verification_email_code');
    }
}
