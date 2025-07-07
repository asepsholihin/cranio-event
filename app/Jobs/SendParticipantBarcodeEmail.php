<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use App\Models\EventAttendance;
use App\Models\Participant;
use App\Mail\EventAttandance\BarcodeRegistration;

class SendParticipantBarcodeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $event;
    protected $participant;

    public function __construct($eventId, $participantId)
    {
        $this->event = EventAttendance::find($eventId);
        $this->participant = Participant::find($participantId);
    }

    public function handle()
    {
        $email = new BarcodeRegistration($this->event, $this->participant);
        Mail::to($this->participant->email)->queue($email);
    }
}
