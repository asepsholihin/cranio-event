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

class LetterTourCrew
{
    private $participantUmrohTrip;
    private $pdf;
    private $data;
    private $fileDownload;

    public function __construct(ParticipantUmrohTrip $participantUmrohTrip)
    {
        App::setLocale('id');
        $this->participantUmrohTrip = $participantUmrohTrip;

        $umrohTrip          = UmrohTrip::findOrFail($this->participantUmrohTrip->umroh_trip_id);
        $packageUmrohTrip   = PackageUmrohTrip::find($this->participantUmrohTrip->package_umroh_trip_id);
        $participant             = Participant::find($this->participantUmrohTrip->participant_id);
        $letterNumber       = LogLetter::generateLetterNumber($participant->id, $participantUmrohTrip->id, 'surat_tugas');

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
        
        $fileDownload = 'Surat Tugas TL_' . $participant->name .'_'. Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y');
        $job = "Tour Leader";
        if($this->participantUmrohTrip->role_type == 3) {
            $fileDownload = 'Surat Tugas Muthawwif_' . $participant->name .'_'. Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y'); 
            $job = "Muthawwif";
        }

        if($participant->gender == 1) {
            $gender = "Laki-laki";
        } else {
            $gender = "Perempuan";
        }

        $data = [
            'participants' => $participant,
            'participantUmrohTrip' => $participantUmrohTrip,
            'packageUmrohTrip' => $packageUmrohTrip,
            'umrohTrip' => $umrohTrip,
            'letterNumber' => $letterNumber,
            'job' => $job,
            'gender' => $gender,
            'address' => $address,
            'address_short' => $address_short,
        ];

        $this->fileDownload = $fileDownload;
        $this->data = $data;

        $this->pdf = PDF::loadView('letter.surat_tugas_tour_crew', $data);
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
        return view('letter.surat_tugas_tour_crew', $this->data)->render();
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
