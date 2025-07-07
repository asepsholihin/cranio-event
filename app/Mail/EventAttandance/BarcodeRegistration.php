<?php

namespace App\Mail\EventAttandance;

use App\Models\EventAttendance;
use App\Models\Participant;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Symfony\Component\Mime\Email;

class BarcodeRegistration extends Mailable
{
    use Queueable, SerializesModels;

    public $participant;
    public $hospital;
    public $eventData;
    public $barcode;
    public $barcodeCid;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(EventAttendance $eventData, Participant $participant)
    {
        $this->participant = $participant;
        $this->eventData = $eventData;
        $this->hospital = Booking::join('participant_bookings', 'bookings.id', 'participant_bookings.booking_id')->where('participant_bookings.participant_id', $participant->id)->orderBy('bookings.id', 'DESC')->first();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->participant->barcode == null) {
            $barcode = Str::uuid()->toString();
            $this->participant->barcode = $barcode;
            $this->participant->save();
        }

        // Generate barcode sebagai PNG base64
        $barcodeBase64 = \DNS2D::getBarcodePNG($this->participant->barcode, 'QRCODE');
        $barcodeBinary = base64_decode($barcodeBase64);

        // Simpan file sementara
        $filename = 'barcode_' . uniqid() . '.png';
        $filePath = storage_path("app/{$filename}");
        file_put_contents($filePath, $barcodeBinary);

        // Embed gambar dan simpan CID via withSwiftMessage
        $this->withSymfonyMessage(function (Email $message) use ($filePath, &$cid) {
            // Tambahkan sebagai attachment inline
            $cid = $message->embedFromPath($filePath);
        });

        return $this
            ->subject(Str::replace("\n", ' ', $this->eventData->name))
            ->from('no-reply@cranioindonesia.com', 'Cranio Indonesia')
            ->view('emails.event-attendance.barcode_registration')
            ->with([
                'barcodeCid' => $cid,
            ]);
    }
}
