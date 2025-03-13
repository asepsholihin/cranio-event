<?php

namespace App\Mail\EventAttandance;

use App\Models\AttendanceOpenRegistration;
use App\Models\EventOpenRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class OpenRegistration extends Mailable
{
    use Queueable, SerializesModels;

    public $attendee;
    public $eventData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(AttendanceOpenRegistration $attendee)
    {
        $this->attendee = $attendee;
        $this->eventData = EventOpenRegistration::find($attendee->event_open_registration_id);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(Str::replace("\n", ' ', $this->eventData->name))
            ->from('no-reply@jejakimani.com', 'Jejak Imani')
            ->view('emails.event-attendance.open_registration')
            ->attachFromStorage(AttendanceOpenRegistration::S3_PATH_BARCODE . $this->attendee->barcode . '.jpg', 'registration-barcode.jpg', [
                'mime' => 'image/jpeg'
            ]);
    }
}
