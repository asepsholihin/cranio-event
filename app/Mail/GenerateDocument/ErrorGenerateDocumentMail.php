<?php

namespace App\Mail\GenerateDocument;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\UmrohTrip;

class ErrorGenerateDocumentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $document;
    public $errorMessage;
    public $umrohTripId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($document, $errorMessage, $umrohTripId)
    {
        $this->document = $document;
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
        $subject = "Jejak Imani - Document";
        if($this->document == "luggage_tag") {
            $subject = "Jejak Imani - Luggage Tag {$umrohTrip->title}";
        } elseif($this->document == "koper_tag") {
            $subject = "Jejak Imani - Koper Tag {$umrohTrip->title}";
        } elseif($this->document == "id_card") {
            $subject = "Jejak Imani - ID Card {$umrohTrip->title}";
        } elseif($this->document == "certificate") {
            $subject = "Jejak Imani - Certificate {$umrohTrip->title}";
        } elseif($this->document == "attendance_tag") {
            $subject = "Jejak Imani - Attendance Tag {$umrohTrip->title}";
        } elseif($this->document == "participant_documents") {
            $subject = "Jejak Imani - Participant Documents {$umrohTrip->title}";
        } elseif($this->document == "passport") {
            $subject = "Jejak Imani - Participant Passports {$umrohTrip->title}";
        }

        return $this
            ->subject($subject)
            ->from('no-reply@jejakimani.com', 'Jejak Imani')
            ->view('emails.generate-document.error-generate-document');
    }
}
