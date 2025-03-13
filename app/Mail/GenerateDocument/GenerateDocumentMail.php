<?php

namespace App\Mail\GenerateDocument;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\UmrohTrip;

class GenerateDocumentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $document;
    public $documentLink;
    public $umrohTripId;
    public $page;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($document, $documentLink, $umrohTripId, $page=null)
    {
        $this->document = $document;
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
            $subject = "Jejak Imani - Participant Passports {$umrohTrip->title}" . $part;
        }
        
        return $this
            ->subject($subject)
            ->from('no-reply@jejakimani.com', 'Jejak Imani')
            ->view('emails.generate-document.generate-document');
    }
}
