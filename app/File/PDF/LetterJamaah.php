<?php
namespace App\File\PDF;

use App\Models\Participant;
use App\Models\ParticipantUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\PackageUmrohTrip;
use App\Models\ParticipantLetterInformation;
use App\Models\OrderItemUmrohTrip;
use App\Models\LogLetter;
use App\Models\WebCategory;
use Illuminate\Support\Str;
use SnappyImage;
use PDF;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use App\Support\NumberFormat;
use App\Support\General;

class LetterParticipant
{
    private $participantUmrohTrip;
    private $umrohTripId;
    private $category;
    private $pdf;
    private $filename;
    private $data;
    private $fileDownload;
    private $materai = "";
    private $yearLetter;

    public function __construct($yearLetter, ParticipantUmrohTrip $participantUmrohTrip, $umrohTripId, $category, $isUser=false)
    {
        App::setLocale('id');
        $this->participantUmrohTrip = $participantUmrohTrip;
        $this->umrohTripId = $umrohTripId;
        $this->category = $category;

        $umrohTrip          = UmrohTrip::findOrFail($this->umrohTripId);
        $packageUmrohTrip   = PackageUmrohTrip::find($this->participantUmrohTrip->package_umroh_trip_id);
        $participant             = Participant::find($this->participantUmrohTrip->participant_id);
        $participantUmrohTrip    = ParticipantUmrohTrip::where('umroh_trip_id', $this->umrohTripId)->where('participant_id', $this->participantUmrohTrip->participant_id)->first();
        $letterInformation  = ParticipantLetterInformation::where('participant_id', $this->participantUmrohTrip->participant_id)->first();
        $order              = OrderItemUmrohTrip::join('order_umroh_trips', 'order_umroh_trips.id', 'order_item_umroh_trips.order_umroh_trip_id')->where('order_no', $this->participantUmrohTrip->booking_order_no)->where('package_umroh_trip_id', $this->participantUmrohTrip->package_umroh_trip_id)->first();
        $additionalOrder    = OrderItemUmrohTrip::where('assigned_participant', 'like', '%'.$this->participantUmrohTrip->id.'%')->sum('price') ?? 0;
        $category           = $this->category;
        $tripCategory       = WebCategory::find($umrohTrip->category_id)->name ?? "";
        $letterNumber       = "";
        $ktpAddress         = ($participant->ktp_address) ? $participant->ktp_address . ", " : "";
        $ktpAddress         = Str::replace('Rt', 'RT', $ktpAddress);
        $ktpAddress         = Str::replace('Rw', 'RW', $ktpAddress);
        $ktpKelurahan       = ($participant->ktp_kelurahan) ? "Kel. " . ucwords(strtolower($participant->ktp_kelurahan)) . ", " : "";
        $ktpKecamatan       = ($participant->ktp_kecamatan) ? "Kec. " . ucwords(strtolower($participant->ktp_kecamatan)) . ", " : "";
        $ktpCity            = ($participant->ktp_city) ? ucwords(strtolower($participant->ktp_city)) . ", " : "";
        $ktpProvince        = ($participant->ktp_province) ? ucwords(strtolower($participant->ktp_province)) : "";
        $ktpPostalCode      = $participant->ktp_postalcode;
        $address            = $participant->ktp_address ? strtoupper($ktpAddress . $ktpKelurahan . $ktpKecamatan . $ktpCity . $ktpProvince) : $participant->home_address;
        $address_short      = $participant->ktp_address ? rtrim(strtoupper($ktpAddress), ', ') : rtrim(strtoupper($participant->home_address), ', ');
        $hari_ini           = Carbon::now()->isoFormat('dddd');
        $bulan_ini          = Carbon::now()->isoFormat('MMMM');
        $ejaan_tanggal      = NumberFormat::terbilang(date('d'));
        $ejaan_tahun        = NumberFormat::terbilang(date('Y'));
        $letterYearHijriah  = General::tahunHijriah($yearLetter);
        $letterYearMasehi   = $yearLetter;

        $letterType = "surat_pernyataan";
        $filename = 'surat_pernyataan_izin';
        $fileDownload = 'Surat Pernyataan atau Izin - ' . $participant->name;
        if ($category == 'Surat Izin') {
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, $letterType, $isUser);
        }
        if($category == 'Surat Pernyataan dan Jaminan') {
            $letterType = "surat_pernyataan";
            $filename = 'surat_pernyataan_jaminan';
            $fileDownload = 'Surat Pernyataan dan Jaminan - ' . $participant->name;
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, $letterType, $isUser);
        }
        if($category == 'Surat Rekomendasi Meningitis') {
            $letterType = "surat_meningitis";
            $filename = 'surat_rekomendasi_meningitis';
            $fileDownload = 'Surat Rekomendasi Meningitis - ' . $participant->name;
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, $letterType, $isUser);
        }
        if($category == 'Surat Pengantar Pembuatan Paspor Baru') {
            $letterType = "surat_passport";
            $filename = 'surat_pengantar_pembuatan_paspor_baru';
            $fileDownload = 'Surat Pengantar Pembuatan Paspor Baru - ' . $participant->name;
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, $letterType, $isUser);
        }
        if($category == 'Surat Pengantar Perpanjang Paspor') {
            $letterType = "surat_perpanjang_passport";
            $filename = 'surat_pengantar_perpanjang_paspor';
            $fileDownload = 'Surat Pengantar Perpanjang Paspor - ' . $participant->name;
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, $letterType, $isUser);
        }
        if($category == 'Surat Pengantar Penggantian Paspor') {
            $letterType = "surat_penggantian_passport";
            $filename = 'surat_pengantar_penggantian_paspor';
            $fileDownload = 'Surat Pengantar Penggantian Paspor - ' . $participant->name;
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, $letterType, $isUser);
        }
        if($category == 'Surat Pengantar Penambahan Nama Paspor') {
            $letterType = "surat_penambahan_passport";
            $filename = 'surat_pengantar_penambahan_nama_paspor';
            $fileDownload = 'Surat Pengantar Penambahan Nama Paspor - ' . $participant->name;
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, $letterType, $isUser);
        }
        if($category == 'Surat Keterangan Participant') {
            $letterType = "surat_keterangan";
            $filename = 'surat_keterangan_participant';
            $fileDownload = 'Surat Keterangan Participant - ' . $participant->name;
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, $letterType, $isUser);
        }
        if(Str::contains($category, 'Surat Perjanjian Perjalanan Umrah')) {
            $letterType = 'mou_surat_perjanjian_perjalanan_ibadah_umroh_jejakimani';
            $filename = 'surat_perjanjian_perjalanan_ibadah_umroh_jejakimani';
            $fileDownload = 'Surat Perjanjian Perjalanan Ibadah Umroh - ' . $participant->name;
        }
        if($category == 'Surat Perjanjian Perjalanan Umrah Kemenag') {
            $filename = 'surat_perjanjian_perjalanan_ibadah_umroh_kemenag';
            $fileDownload = 'Surat Perjanjian Perjalanan Ibadah Umroh Kemenag - ' . $participant->name;
        }
        if($category == 'Surat Pernyataan Covid') {
            $filename = 'surat_pernyataan_covid';
            $fileDownload = 'Surat Pernyataan Covid - ' . $participant->name;
        }
        if(Str::contains($category, 'Surat Perjanjian Perjalanan Haji Furoda')) {
            $letterType = 'mou_surat_perjanjian_perjalanan_ibadah_haji_furoda';
            $filename = 'surat_perjanjian_perjalanan_ibadah_haji_furoda';
            $fileDownload = 'Surat Perjanjian Perjalanan Haji - ' . $participant->name;
        }
        if(Str::contains($category, 'Surat Perjanjian Perjalanan Haji Khusus')) {
            $letterType = 'mou_surat_perjanjian_perjalanan_ibadah_haji_khusus';
            $filename = 'surat_perjanjian_perjalanan_ibadah_haji_khusus';
            $fileDownload = 'Surat Perjanjian Perjalanan Haji - ' . $participant->name;
        }
        if(Str::contains($category, 'Surat Persetujuan Haji Khusus')) {
            $letterType = 'mou_surat_persetujuan_haji_khusus';
            $filename = 'surat_persetujuan_haji_khusus';
            $fileDownload = 'Surat Persetujuan - ' . $participant->name;
        }
        if ($category == 'Surat Kuasa Haji Khusus') {
            $letterType = 'mou_surat_kuasa_haji_khusus';
            $filename = 'surat_kuasa_haji_khusus';
            $fileDownload = 'Surat Kuasa, Perjanjian, Pernyataan - ' . $participant->name;
        }
        if ($category == 'Surat Perjanjian Haji Khusus') {
            $letterType = 'mou_surat_perjanjian_haji_khusus';
            $filename = 'surat_perjanjian_haji_khusus';
            $fileDownload = 'Surat Perjanjian - ' . $participant->name;
        }
        if ($category == 'Surat Pemberitahuan Nomor Porsi') {
            $letterType = 'mou_surat_pemberitahuan_nomor_porsi';
            $filename = 'surat_pemberitahuan_nomor_porsi';
            $fileDownload = 'Surat Pemberitahuan Nomor Porsi - ' . $participant->name;
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, $letterType);
        }

        if(Str::contains($category, 'Materai')){
            $this->materai = "Materai 10.000";
        }

        $signed = LogLetter::select('sign_evidence')->where('participant_umroh_trip_id', $participantUmrohTrip->id)->where('letter_type', $letterType)->whereNotNull('sign_evidence')->first()->sign_evidence ?? null;

        $data = [
            'participant' => $participant,
            'participantUmrohTrip' => $participantUmrohTrip,
            'packageUmrohTrip' => $packageUmrohTrip,
            'umrohTrip' => $umrohTrip,
            'letterInformation' => $letterInformation,
            'order' => $order,
            'additionalOrder' => $additionalOrder,
            'letterNumber' => $letterNumber,
            'address' => $address,
            'address_short' => $address_short,
            'tripCategory' => $tripCategory,
            'hari_ini' => $hari_ini,
            'bulan_ini' => $bulan_ini,
            'ejaan_tanggal' => $ejaan_tanggal,
            'ejaan_tahun' => $ejaan_tahun,
            'signed' => $signed,
            'materai' => $this->materai,
            'letterYearHijriah' => $letterYearHijriah,
            'letterYearMasehi' => $letterYearMasehi
        ];

        $this->filename = $filename;
        $this->fileDownload = $fileDownload;
        $this->data = $data;

        $this->pdf = PDF::loadView('letter.'.$filename, $data);
        $this->pdf->setOption('enable-local-file-access', true)
            ->setOption('encoding', 'utf-8')
            ->setOption('page-size', 'A4')
            ->setOption('margin-top', '0cm')
            ->setOption('margin-left', '0cm')
            ->setOption('margin-right', '0cm')
            ->setOption('margin-bottom', '0cm')
            ->setOption('images', true);
    }

    public function html()
    {
        return view('letter.'.$this->filename, $this->data)->render();
    }

    public function download()
    {
        return $this->pdf->download($this->fileDownload.'.pdf');
    }

    public function output()
    {
        return $this->pdf->output();
    }

    public function stream()
    {
        return $this->pdf->stream();
    }
}
