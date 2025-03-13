<?php
namespace App\File\Word;

use App\Models\Participant;
use App\Models\ParticipantUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\PackageUmrohTrip;
use App\Models\ParticipantLetterInformation;
use App\Models\OrderItemUmrohTrip;
use App\Models\LogLetter;
use App\Models\WebCategory;
use Illuminate\Support\Str;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;
use App\Support\NumberFormat;
use App\Support\General;

class LetterParticipant
{
    private $participantUmrohTrip;
    private $umrohTripId;
    private $category;
    private $filename;
    private $materai = false;

    public function __construct($yearLetter, ParticipantUmrohTrip $participantUmrohTrip, $umrohTripId, $category)
    {
        App::setLocale('id');
        $this->participantUmrohTrip = $participantUmrohTrip;
        $this->umrohTripId = $umrohTripId;
        $this->category = $category;
        
        $umrohTrip = UmrohTrip::findOrFail($this->umrohTripId);
        $packageUmrohTrip = PackageUmrohTrip::find($participantUmrohTrip->package_umroh_trip_id);
        $participant = Participant::find($participantUmrohTrip->participant_id);
        $letterInformation = ParticipantLetterInformation::where('participant_id', $participantUmrohTrip->participant_id)->first();
        $order = OrderItemUmrohTrip::join('order_umroh_trips', 'order_umroh_trips.id', 'order_item_umroh_trips.order_umroh_trip_id')->where('order_no', $participantUmrohTrip->booking_order_no)->where('package_umroh_trip_id', $participantUmrohTrip->package_umroh_trip_id)->first();
        $additionalOrder = OrderItemUmrohTrip::where('assigned_participant', 'like', '%' . $participantUmrohTrip->id . '%')->sum('price') ?? 0;

        $bulanRomawi = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $bulan      = $bulanRomawi[date('n')];
        $tahun      = date('Y');
        $tahun_hijr = General::tahunHijriah();
        $tanggal    = Carbon::now()->isoFormat('D MMMM Y');
        $hari_ini   = Carbon::now()->isoFormat('dddd');
        $bulan_ini  = Carbon::now()->isoFormat('MMMM');
        $tanggal_ini    = date('d');
        $ejaan_tanggal  = NumberFormat::terbilang(date('d'));
        $ejaan_tahun    = NumberFormat::terbilang(date('Y'));
        $letterYearHijriah  = General::tahunHijriah($yearLetter);
        $letterYearMasehi   = $yearLetter;

        $ktpAddress     = ($participant->ktp_address) ? $participant->ktp_address . ", " : ""; 
        $ktpAddress     = Str::replace('Rt', 'RT', $ktpAddress);
        $ktpAddress     = Str::replace('Rw', 'RW', $ktpAddress);
        $ktpKelurahan   = ($participant->ktp_kelurahan) ? "Kel. " . ucwords(strtolower($participant->ktp_kelurahan)) . ", " : ""; 
        $ktpKecamatan   = ($participant->ktp_kecamatan) ? "Kec. " . ucwords(strtolower($participant->ktp_kecamatan)) . ", " : ""; 
        $ktpCity        = ($participant->ktp_city) ? ucwords(strtolower($participant->ktp_city)) . ", " : ""; 
        $ktpProvince    = ($participant->ktp_province) ? ucwords(strtolower($participant->ktp_province)) : ""; 
        $ktpPostalCode  = $participant->ktp_postalcode;
        $address        = $participant->ktp_address ? strtoupper($ktpAddress . $ktpKelurahan . $ktpKecamatan . $ktpCity . $ktpProvince) : $participant->home_address;
        $address_short  = $participant->ktp_address ? rtrim(strtoupper($ktpAddress), ', ') : rtrim(strtoupper($participant->home_address), ', ');
        $tripCategory   = WebCategory::find($umrohTrip->category_id)->name ?? "";
        $age            = Carbon::parse($participant->birth_date)->age;

        if (request()->get('category') == 'Surat Pengantar Pembuatan Paspor Baru') {
            $path = storage_path('private_assets/surat_pengantar_pembuatan_paspor_baru.docx');
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, "surat_passport");
            $templateProcessor = new TemplateProcessor($path);
            $templateProcessor->setValue('trip_category', $tripCategory);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('month', $bulan);
            $templateProcessor->setValue('year', $tahun);
            $templateProcessor->setValue('letter_number', $letterNumber);
            $templateProcessor->setValue('name', strtoupper($participant->name));
            $templateProcessor->setValue('birth_place', strtoupper(strtolower($participant->birth_place)));
            $templateProcessor->setValue('birth_date', strtoupper(Carbon::parse($participant->birth_date)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('address', strtoupper($address));
            $templateProcessor->setValue('nik', $participant->kitas_number ?? $participant->nik);
            $templateProcessor->setValue('departure_at', Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('return_at', Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('departure_at_capital', strtoupper(Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('return_at_capital', strtoupper(Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('destination_name', $this->removeSpecialChar($letterInformation->destination_imigration ?? '-'));
            $templateProcessor->setValue('destination_address', $this->removeSpecialChar($letterInformation->address_imigration ?? 'Di Tempat'));

            $this->filename = "Surat Pengantar Pembuatan Paspor Baru - " . $participant->name . "";
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }
        if (request()->get('category') == 'Surat Pengantar Perpanjang Paspor') {
            $path = storage_path('private_assets/surat_pengantar_perpanjang_paspor.docx');
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, "surat_perpanjang_passport");
            $templateProcessor = new TemplateProcessor($path);
            $templateProcessor->setValue('trip_category', $tripCategory);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('month', $bulan);
            $templateProcessor->setValue('year', $tahun);
            $templateProcessor->setValue('letter_number', $letterNumber);
            $templateProcessor->setValue('name', strtoupper($participant->name));
            $templateProcessor->setValue('birth_place', strtoupper(strtolower($participant->birth_place)));
            $templateProcessor->setValue('birth_date', strtoupper(Carbon::parse($participant->birth_date)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('address', strtoupper($address));
            $templateProcessor->setValue('nik', $participant->kitas_number ?? $participant->nik);
            $templateProcessor->setValue('departure_at', Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('return_at', Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('departure_at_capital', strtoupper(Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('return_at_capital', strtoupper(Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('destination_name', $this->removeSpecialChar($letterInformation->destination_imigration ?? '-'));
            $templateProcessor->setValue('destination_address', $this->removeSpecialChar($letterInformation->address_imigration ?? 'Di Tempat'));

            $this->filename = "Surat Pengantar Perpanjang Paspor - " . $participant->name . "";
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }
        if (request()->get('category') == 'Surat Pengantar Penggantian Paspor') {
            $path = storage_path('private_assets/surat_pengantar_penggantian_paspor.docx');
            $templateProcessor = new TemplateProcessor($path);
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, "surat_penggantian_passport");
            $templateProcessor->setValue('trip_category', $tripCategory);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('month', $bulan);
            $templateProcessor->setValue('year', $tahun);
            $templateProcessor->setValue('letter_number', $letterNumber);
            $templateProcessor->setValue('name', strtoupper($participant->name));
            $templateProcessor->setValue('birth_place', strtoupper(strtolower($participant->birth_place)));
            $templateProcessor->setValue('birth_date', strtoupper(Carbon::parse($participant->birth_date)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('address', strtoupper($address));
            $templateProcessor->setValue('nik', $participant->kitas_number ?? $participant->nik);
            $templateProcessor->setValue('departure_at', Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('return_at', Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('departure_at_capital', strtoupper(Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('return_at_capital', strtoupper(Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('destination_name', $this->removeSpecialChar($letterInformation->destination_imigration ?? '-'));
            $templateProcessor->setValue('destination_address', $this->removeSpecialChar($letterInformation->address_imigration ?? 'Di Tempat'));

            $this->filename = "Surat Pengantar Penggantian Paspor - " . $participant->name . "";
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }
        if (request()->get('category') == 'Surat Pengantar Penambahan Nama Paspor') {
            $path = storage_path('private_assets/surat_pengantar_penambahan_nama_paspor.docx');
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, "surat_penambahan_passport");
            $templateProcessor = new TemplateProcessor($path);
            $templateProcessor->setValue('trip_category', $tripCategory);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('month', $bulan);
            $templateProcessor->setValue('year', $tahun);
            $templateProcessor->setValue('letter_number', $letterNumber);
            $templateProcessor->setValue('name', strtoupper($participant->name));
            $templateProcessor->setValue('birth_place', strtoupper(strtolower($participant->birth_place)));
            $templateProcessor->setValue('birth_date', strtoupper(Carbon::parse($participant->birth_date)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('address', strtoupper($address));
            $templateProcessor->setValue('nik', $participant->kitas_number ?? $participant->nik);
            $templateProcessor->setValue('departure_at', Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('return_at', Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('departure_at_capital', strtoupper(Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('return_at_capital', strtoupper(Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('destination_name', $this->removeSpecialChar($letterInformation->destination_imigration ?? '-'));
            $templateProcessor->setValue('destination_address', $this->removeSpecialChar($letterInformation->address_imigration ?? 'Di Tempat'));

            $this->filename = "Surat Pengantar Penambahan Nama Paspor - " . $participant->name . "";
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }
        if (request()->get('category') == 'Surat Izin') {
            $path = storage_path('private_assets/surat_pernyataan_izin.docx');
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, "surat_izin");
            $templateProcessor = new TemplateProcessor($path);
            $templateProcessor->setValue('trip_category', $tripCategory);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('month', $bulan);
            $templateProcessor->setValue('year', $tahun);
            $templateProcessor->setValue('letter_number', $letterNumber);
            $templateProcessor->setValue('name', strtoupper($participant->name));
            $templateProcessor->setValue('packageUmrohTrip', ucwords(strtolower($packageUmrohTrip->name)));
            $templateProcessor->setValue('trip', ucwords(strtolower($umrohTrip->title)));
            $templateProcessor->setValue('departure_at', Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('return_at', Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('departure_at_capital', strtoupper(Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('return_at_capital', strtoupper(Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y')));

            $this->filename = 'Surat Pernyataan atau Izin - ' . $participant->name;
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }
        if (request()->get('category') == 'Surat Pernyataan dan Jaminan') {
            $path = storage_path('private_assets/surat_pernyataan_jaminan.docx');
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, "surat_pernyataan");
            $templateProcessor = new TemplateProcessor($path);
            $templateProcessor->setValue('trip_category', $tripCategory);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('month', $bulan);
            $templateProcessor->setValue('year', $tahun);
            $templateProcessor->setValue('letter_number', $letterNumber);
            $templateProcessor->setValue('name', strtoupper($participant->name));
            $templateProcessor->setValue('birth_place', strtoupper(strtolower($participant->birth_place)));
            $templateProcessor->setValue('birth_date', strtoupper(Carbon::parse($participant->birth_date)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('address', strtoupper($address));
            $templateProcessor->setValue('nik', $participant->kitas_number ?? $participant->nik);
            $templateProcessor->setValue('departure_at', Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('return_at', Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('departure_at_capital', strtoupper(Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('return_at_capital', strtoupper(Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('destination_name', $this->removeSpecialChar($letterInformation->destination_kemenag ?? 'Kantor Kementerian Agama'));
            $templateProcessor->setValue('destination_address', $this->removeSpecialChar($letterInformation->address_kemenag ?? 'Di Tempat'));

            $this->filename = 'Surat Pernyataan dan Jaminan - ' . $participant->name;
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }
        if (request()->get('category') == 'Surat Rekomendasi Meningitis') {
            $path = storage_path('private_assets/surat_rekomendasi_meningitis.docx');
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, "surat_meningitis");
            $templateProcessor = new TemplateProcessor($path);
            $templateProcessor->setValue('trip_category', $tripCategory);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('month', $bulan);
            $templateProcessor->setValue('year', $tahun);
            $templateProcessor->setValue('letter_number', $letterNumber);
            $templateProcessor->setValue('name', strtoupper($participant->name));
            $templateProcessor->setValue('birth_place', strtoupper(strtolower($participant->birth_place)));
            $templateProcessor->setValue('birth_date', strtoupper(Carbon::parse($participant->birth_date)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('address', strtoupper($address));
            $templateProcessor->setValue('nik', $participant->kitas_number ?? $participant->nik);
            $templateProcessor->setValue('trip', ucwords(strtolower($umrohTrip->title)));
            $templateProcessor->setValue('departure_at', Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('return_at', Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('departure_at_capital', strtoupper(Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('return_at_capital', strtoupper(Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('destination_name', $this->removeSpecialChar($letterInformation->destination_hospital ?? 'Rumah Sakit'));
            $templateProcessor->setValue('destination_address', $this->removeSpecialChar($letterInformation->address_hospital ?? 'Di Tempat'));

            $this->filename = 'Surat Rekomendasi Meningitis - ' . $participant->name;
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }
        if (request()->get('category') == 'Surat Keterangan Participant') {
            $path = storage_path('private_assets/surat_keterangan_participant.docx');
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, "surat_keterangan");
            $templateProcessor = new TemplateProcessor($path);
            $templateProcessor->setValue('trip_category', $tripCategory);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('month', $bulan);
            $templateProcessor->setValue('year', $tahun);
            $templateProcessor->setValue('letter_number', $letterNumber);
            $templateProcessor->setValue('name', strtoupper(strtolower($participant->name)));
            $templateProcessor->setValue('job', strtoupper(strtolower($participant->job)));
            $templateProcessor->setValue('packageUmrohTrip', ucwords(strtolower($packageUmrohTrip->name)));
            $templateProcessor->setValue('trip', ucwords(strtolower($umrohTrip->title)));
            $templateProcessor->setValue('departure_at', Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('return_at', Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('departure_at_capital', strtoupper(Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('return_at_capital', strtoupper(Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('destination_name', $this->removeSpecialChar($letterInformation->destination_permission ?? 'Pimpinan Perusahaan'));
            $templateProcessor->setValue('destination_address', $this->removeSpecialChar($letterInformation->address_permission ?? 'Di Tempat'));

            $this->filename = 'Surat Keterangan Participant - ' . $participant->name;
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }
        if (request()->get('category') == 'Surat Perjanjian Perjalanan Umrah') {
            $path = storage_path('private_assets/surat_perjanjian_perjalanan_ibadah_umroh_jejakimani.docx');
            $templateProcessor = new TemplateProcessor($path);
            $templateProcessor->setValue('trip_category', $tripCategory);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('name', strtoupper($participant->name));
            $templateProcessor->setValue('address', strtoupper($address));
            $templateProcessor->setValue('nik', $participant->kitas_number ?? $participant->nik);
            $templateProcessor->setValue('trip', strtoupper(strtolower($umrohTrip->title)));
            $templateProcessor->setValue('packageUmrohTrip', ucwords(strtolower($packageUmrohTrip->name)));
            $templateProcessor->setValue('departure_at', Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('return_at', Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('departure_at_capital', strtoupper(Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('return_at_capital', strtoupper(Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('job', strtoupper(strtolower($participant->job)));
            $templateProcessor->setValue('total_days', $umrohTrip->total_days);
            $templateProcessor->setValue('price', NumberFormat::separatorAmount($participantUmrohTrip->price_after_discount + $additionalOrder));
            $templateProcessor->setValue('hotel_makkah', $packageUmrohTrip->hotel_makkah);
            $templateProcessor->setValue('hotel_madinah', $packageUmrohTrip->hotel_madinah);
            $templateProcessor->setValue('nights_in_makkah', $packageUmrohTrip->nights_in_makkah);
            $templateProcessor->setValue('nights_in_madinah', $packageUmrohTrip->nights_in_madinah);
            $quota = 0;
            if ($participantUmrohTrip->room_type == "double") {
                $quota = 2;
            }
            if ($participantUmrohTrip->room_type == "triple") {
                $quota = 3;
            }
            if ($participantUmrohTrip->room_type == "quad") {
                $quota = 4;
            }
            if ($participantUmrohTrip->room_type == "queen") {
                $quota = 5;
            }
            if ($participantUmrohTrip->room_type == "single") {
                $quota = 1;
            }
            $templateProcessor->setValue('quota', $quota);

            $this->filename = "Surat Perjanjian Perjalanan Ibadah Umroh - " . $participant->name . "";
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }
        if (request()->get('category') == 'Surat Perjanjian Perjalanan Umrah Kemenag') {
            $path = storage_path('private_assets/surat_perjanjian_perjalanan_ibadah_umroh_kemenag.docx');
            $templateProcessor = new TemplateProcessor($path);
            $templateProcessor->setValue('trip_category', $tripCategory);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('name', strtoupper($participant->name));
            $templateProcessor->setValue('nik', $participant->kitas_number ?? $participant->nik);
            $templateProcessor->setValue('address', strtoupper($address));
            $templateProcessor->setValue('job', strtoupper(strtolower($participant->job)));
            $templateProcessor->setValue('total_days', $umrohTrip->total_days);
            $templateProcessor->setValue('price', NumberFormat::separatorAmount($order->price ?? 0));
            $templateProcessor->setValue('hotel_makkah', $packageUmrohTrip->hotel_makkah);
            $templateProcessor->setValue('hotel_madinah', $packageUmrohTrip->hotel_madinah);
            $templateProcessor->setValue('nights_in_makkah', $packageUmrohTrip->nights_in_makkah);
            $templateProcessor->setValue('nights_in_madinah', $packageUmrohTrip->nights_in_madinah);
            $quota = 0;
            if ($participantUmrohTrip->room_type == "double") {
                $quota = 2;
            }
            if ($participantUmrohTrip->room_type == "triple") {
                $quota = 3;
            }
            if ($participantUmrohTrip->room_type == "quad") {
                $quota = 4;
            }
            if ($participantUmrohTrip->room_type == "queen") {
                $quota = 5;
            }
            if ($participantUmrohTrip->room_type == "single") {
                $quota = 1;
            }
            $templateProcessor->setValue('quota', $quota);

            $this->filename = "Surat Perjanjian Perjalanan Ibadah Umroh Kemenag - " . $participant->name . "";
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }
        if (request()->get('category') == 'Surat Pernyataan Covid') {
            $path = storage_path('private_assets/surat_pernyataan_covid.docx');
            $templateProcessor = new TemplateProcessor($path);
            $templateProcessor->setValue('trip_category', $tripCategory);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('name', strtoupper($participant->name));
            $templateProcessor->setValue('birth_place', strtoupper(strtolower($participant->birth_place)));
            $templateProcessor->setValue('birth_date', strtoupper(Carbon::parse($participant->birth_date)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('address', strtoupper($address));
            $templateProcessor->setValue('nik', $participant->kitas_number ?? $participant->nik);
            $templateProcessor->setValue('no_hp', $participant->no_hp);
            $templateProcessor->setValue('letterYearHijriah', $letterYearHijriah);
            $templateProcessor->setValue('letterYearMasehi', $letterYearMasehi);

            $this->filename = "Surat Pernyataan Covid - " . $participant->name . "";
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }
        if (Str::contains($category, 'Surat Perjanjian Perjalanan Haji Furoda')) {
            $path = storage_path('private_assets/surat_perjanjian_perjalanan_ibadah_haji_furoda.docx');
            $templateProcessor = new TemplateProcessor($path);
            $materai = "";
            if(Str::contains($category, 'Materai')){
                $materai = "Materai 10.000";
            }
            $templateProcessor->setValue('materai', $materai);
            $templateProcessor->setValue('trip_category', $tripCategory);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('name', strtoupper(strtolower($participant->name)));
            $templateProcessor->setValue('address', strtoupper($address));
            $templateProcessor->setValue('nik', $participant->kitas_number ?? $participant->nik);
            $templateProcessor->setValue('trip', ucwords(strtolower($umrohTrip->title)));
            $templateProcessor->setValue('packageUmrohTrip', ucwords(strtolower($packageUmrohTrip->name)));
            $templateProcessor->setValue('departure_at', Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('return_at', Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('departure_at_capital', strtoupper(Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('return_at_capital', strtoupper(Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('job', strtoupper(strtolower($participant->job)));
            $templateProcessor->setValue('total_days', $umrohTrip->total_days);
            $templateProcessor->setValue('price', NumberFormat::separatorAmount($order->price ?? 0));
            $templateProcessor->setValue('hotel_makkah', $packageUmrohTrip->hotel_makkah);
            $templateProcessor->setValue('hotel_madinah', $packageUmrohTrip->hotel_madinah);
            $templateProcessor->setValue('nights_in_makkah', $packageUmrohTrip->nights_in_makkah);
            $templateProcessor->setValue('nights_in_madinah', $packageUmrohTrip->nights_in_madinah);
            $templateProcessor->setValue('letterYearHijriah', $letterYearHijriah);
            $templateProcessor->setValue('letterYearMasehi', $letterYearMasehi);

            $this->filename = "Surat Perjanjian Perjalanan Ibadah Haji - " . $participant->name . "";
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }
        if (Str::contains($category, 'Surat Perjanjian Perjalanan Haji Khusus')) {
            $path = storage_path('private_assets/surat_perjanjian_perjalanan_ibadah_haji_khusus.docx');
            $templateProcessor = new TemplateProcessor($path);
            $materai = "";
            if(Str::contains($category, 'Materai')){
                $materai = "Materai 10.000";
            }
            $star_hotel_madinah = 5;
            if(str_contains($packageUmrohTrip->name, "Ruby")) {
                $star_hotel_madinah = 3;
            }
            $templateProcessor->setValue('materai', $materai);
            $templateProcessor->setValue('trip_category', $tripCategory);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('name', strtoupper($participant->name));
            $templateProcessor->setValue('name_capitalize', strtoupper(strtolower($participant->name)));
            $templateProcessor->setValue('address', strtoupper($address));
            $templateProcessor->setValue('nik', $participant->kitas_number ?? $participant->nik);
            $templateProcessor->setValue('trip', ucwords(strtolower($umrohTrip->title)));
            $templateProcessor->setValue('packageUmrohTrip', ucwords(strtolower($packageUmrohTrip->name)));
            $templateProcessor->setValue('departure_at', Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('return_at', Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y'));
            $templateProcessor->setValue('departure_at_capital', strtoupper(Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('return_at_capital', strtoupper(Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('job', strtoupper(strtolower($participant->job)));
            $templateProcessor->setValue('total_days', $umrohTrip->total_days);
            $templateProcessor->setValue('price', NumberFormat::separatorAmount($order->price ?? 0));
            $templateProcessor->setValue('hotel_makkah', $packageUmrohTrip->hotel_makkah);
            $templateProcessor->setValue('hotel_madinah', $packageUmrohTrip->hotel_madinah);
            $templateProcessor->setValue('star_hotel_madinah', $star_hotel_madinah);
            $templateProcessor->setValue('nights_in_makkah', $packageUmrohTrip->nights_in_makkah);
            $templateProcessor->setValue('nights_in_madinah', $packageUmrohTrip->nights_in_madinah);
            $templateProcessor->setValue('letterYearHijriah', $letterYearHijriah);
            $templateProcessor->setValue('letterYearMasehi', $letterYearMasehi);

            $this->filename = "Surat Perjanjian Perjalanan Ibadah Haji - " . $participant->name . "";
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }
        if (Str::contains($category, 'Surat Persetujuan Haji Khusus')) {
            $path = storage_path('private_assets/surat_persetujuan_haji_khusus.docx');
            $templateProcessor = new TemplateProcessor($path);
            $materai = "";
            if(Str::contains($category, 'Materai')){
                $materai = "Materai 10.000";
            }
            $templateProcessor->setValue('materai', $materai);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('name', strtoupper($participant->name));
            $templateProcessor->setValue('age', $age);
            $templateProcessor->setValue('job', strtoupper(strtolower($participant->job)));
            $templateProcessor->setValue('address', strtoupper($address));
            $templateProcessor->setValue('no_hp', $participant->no_hp);
            $templateProcessor->setValue('hari_ini', $hari_ini);
            $templateProcessor->setValue('bulan_ini', $bulan_ini);
            $templateProcessor->setValue('ejaan_tanggal', $ejaan_tanggal);
            $templateProcessor->setValue('ejaan_tahun', $ejaan_tahun);
            $templateProcessor->setValue('letterYearHijriah', $letterYearHijriah);
            $templateProcessor->setValue('letterYearMasehi', $letterYearMasehi);

            $this->filename = "Surat Persetujuan - " . $participant->name . "";
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }
        if ($category == 'Surat Kuasa Haji Khusus') {
            $path = storage_path('private_assets/surat_kuasa_perjanjian_pernyataan_haji_khusus.docx');
            $templateProcessor = new TemplateProcessor($path);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('year', $tahun);
            $templateProcessor->setValue('year_hijr', $tahun_hijr);
            $templateProcessor->setValue('name', strtoupper($participant->name));
            $templateProcessor->setValue('nik', $participant->kitas_number ?? $participant->nik);
            $templateProcessor->setValue('birth_place', strtoupper(strtolower($participant->birth_place)));
            $templateProcessor->setValue('birth_date', strtoupper(Carbon::parse($participant->birth_date)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('age', $age);
            $templateProcessor->setValue('job', strtoupper(strtolower($participant->job)));
            $templateProcessor->setValue('address', strtoupper($address));
            $templateProcessor->setValue('address_short', strtoupper($address_short));
            $templateProcessor->setValue('no_hp', $participant->no_hp);
            $templateProcessor->setValue('tanggal_ini', $tanggal_ini);
            $templateProcessor->setValue('ejaan_tahun', $ejaan_tahun);
            $templateProcessor->setValue('hari_ini', $hari_ini);
            $templateProcessor->setValue('bulan_ini', $bulan_ini);
            $templateProcessor->setValue('ejaan_tanggal', $ejaan_tanggal);
            $templateProcessor->setValue('ejaan_tahun', $ejaan_tahun);
            $templateProcessor->setValue('kelurahan', strtoupper($participant->ktp_kelurahan));
            $templateProcessor->setValue('kecamatan', strtoupper($participant->ktp_kecamatan));
            $templateProcessor->setValue('city', strtoupper($participant->ktp_city));
            $templateProcessor->setValue('province', strtoupper($participant->ktp_province));
            $templateProcessor->setValue('postalcode', $participant->ktp_postalcode);
            $templateProcessor->setValue('letterYearHijriah', $letterYearHijriah);
            $templateProcessor->setValue('letterYearMasehi', $letterYearMasehi);

            $this->filename = "Surat Kuasa, Perjanjian, Pernyataan - " . $participant->name . "";
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }
        if ($category == 'Surat Perjanjian Haji Khusus') {
            $path = storage_path('private_assets/surat_perjanjian_haji_khusus.docx');
            $templateProcessor = new TemplateProcessor($path);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('year', $tahun);
            $templateProcessor->setValue('year_hijr', $tahun_hijr);
            $templateProcessor->setValue('name', strtoupper($participant->name));
            $templateProcessor->setValue('nik', $participant->kitas_number ?? $participant->nik);
            $templateProcessor->setValue('birth_place', strtoupper(strtolower($participant->birth_place)));
            $templateProcessor->setValue('birth_date', strtoupper(Carbon::parse($participant->birth_date)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('age', $age);
            $templateProcessor->setValue('job', strtoupper(strtolower($participant->job)));
            $templateProcessor->setValue('address', strtoupper($address));
            $templateProcessor->setValue('address_short', strtoupper($address_short));
            $templateProcessor->setValue('no_hp', $participant->no_hp);
            $templateProcessor->setValue('tanggal_ini', $tanggal_ini);
            $templateProcessor->setValue('ejaan_tahun', $ejaan_tahun);
            $templateProcessor->setValue('hari_ini', $hari_ini);
            $templateProcessor->setValue('bulan_ini', $bulan_ini);
            $templateProcessor->setValue('ejaan_tanggal', $ejaan_tanggal);
            $templateProcessor->setValue('ejaan_tahun', $ejaan_tahun);
            $templateProcessor->setValue('kelurahan', strtoupper($participant->ktp_kelurahan));
            $templateProcessor->setValue('kecamatan', strtoupper($participant->ktp_kecamatan));
            $templateProcessor->setValue('city', strtoupper($participant->ktp_city));
            $templateProcessor->setValue('province', strtoupper($participant->ktp_province));
            $templateProcessor->setValue('postalcode', $participant->ktp_postalcode);
            $templateProcessor->setValue('letterYearHijriah', $letterYearHijriah);
            $templateProcessor->setValue('letterYearMasehi', $letterYearMasehi);

            $this->filename = "Surat Perjanjian - " . $participant->name . "";
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }
        if ($category == 'Surat Pemberitahuan Nomor Porsi') {
            $path = storage_path('private_assets/surat_pemberitahuan_nomor_porsi.docx');
            $letterNumber = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, "mou_surat_pemberitahuan_nomor_porsi");
            $templateProcessor = new TemplateProcessor($path);
            $templateProcessor->setValue('letter_number', $letterNumber);
            $templateProcessor->setValue('date', $tanggal);
            $templateProcessor->setValue('name', strtoupper($participant->name));
            $templateProcessor->setValue('nik', $participant->kitas_number ?? $participant->nik);
            $templateProcessor->setValue('birth_place', strtoupper(strtolower($participant->birth_place)));
            $templateProcessor->setValue('birth_date', strtoupper(Carbon::parse($participant->birth_date)->isoFormat('D MMMM Y')));
            $templateProcessor->setValue('address', strtoupper($address));
            $templateProcessor->setValue('nomor_porsi', $participantUmrohTrip->nomor_porsi);

            $this->filename = "Surat Pemberitahuan Nomor Porsi - " . $participant->name . "";
            $templateProcessor->saveAs(storage_path('app/' . $this->filename . '.docx'));
        }

    }

    public function download()
    {
        return response()->download(storage_path('app/' . $this->filename . '.docx'))->deleteFileAfterSend(true);
    }

    public function removeSpecialChar($text) {
        return htmlentities($text);
    }
}
