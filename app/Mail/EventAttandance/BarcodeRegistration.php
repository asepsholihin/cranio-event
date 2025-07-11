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
use Intervention\Image\Facades\Image;

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
        $barcodeBase64 = \DNS2D::getBarcodePNG($this->participant->barcode, 'QRCODE', 6); // skala 6 agar cukup besar
        $barcodeBinary = base64_decode($barcodeBase64);

        // Buat image dari barcode
        $barcodeImage = Image::make($barcodeBinary);

        // Atur ukuran barcode (misalnya 300x300) — resize
        $barcodeImage->resize(512, 512);

        // Tambahkan nama di bawah barcode
        $namaPeserta = $this->participant->name;
        $namaInstansi = $this->hospital->account_hospital;

        // Tentukan font path (gunakan default system font atau upload font .ttf sendiri jika diperlukan)
        $fontPath = storage_path('private_assets/fonts/Mulish-Regular.ttf'); // kamu bisa letakkan font di `public/fonts`

        // Buat canvas baru (dengan padding dan ruang teks)
        $padding = 20;
        $textHeight = 80;

        $canvas = Image::canvas(
            $barcodeImage->width() + $padding * 2,
            $barcodeImage->height() + $padding * 2 + $textHeight,
            '#ffffff'
        );

        // Tempel barcode ke tengah atas
        $canvas->insert($barcodeImage, 'top-left', $padding, $padding);

        // 5. Tambahkan teks NAMA
        $canvas->text($namaPeserta, $canvas->width() / 2, $barcodeImage->height() + $padding + 15, function ($font) use($fontPath) {
            $font->file($fontPath); // pastikan font ada
            $font->size(20);
            $font->color('#000000');
            $font->align('center');
            $font->valign('top');
        });

        // 6. Tambahkan teks INSTANSI
        $canvas->text($namaInstansi, $canvas->width() / 2, $barcodeImage->height() + $padding + 15 + 30, function ($font) use($fontPath) {
            $font->file($fontPath);
            $font->size(18);
            $font->color('#444444');
            $font->align('center');
            $font->valign('top');
        });

        // 7. Tambahkan teks Supported
        $canvas->text("Cranio System is Developed with ♥ by www.akusolusi.com", $canvas->width() / 2, $barcodeImage->height() + $padding + 45 + 30, function ($font) use($fontPath) {
            $font->file($fontPath);
            $font->size(12);
            $font->color('#777777');
            $font->align('center');
            $font->valign('top');
        });

        // Simpan ke file sementara
        $filename = $namaPeserta . '.png';
        $filePath = storage_path("app/{$filename}");
        $canvas->save($filePath);

        // Embed ke email (Laravel >=10 pakai Symfony Mailer)
        $this->withSymfonyMessage(function (Email $message) use ($filePath, &$cid) {
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
