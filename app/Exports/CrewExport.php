<?php

namespace App\Exports;

use App\Models\Participant;
use App\Models\Crew;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CrewExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents, WithColumnFormatting
{
    private $umrohTripId;
    private $rowNumber;

    public function __construct($umrohTripId = null)
    {
        $this->umrohTripId = $umrohTripId;
        $this->rowNumber = 0;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Title (Mr/Ms/Mrs/Mstr/Miss)',
            'Gelar Depan',
            'Nama Lengkap Sesuai KTP',
            'Gelar Belakang',
            'Nama Ayah',
            'Jenis Kelamin (Pria/Wanita)',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Nomor KTP',
            'Status Nikah (Menikah/Belum Menikah/Cerai)',
            'Kewarganeraan',
            'Whatsapp / No HP',
            'Email',
            'Instagram',
            'Provinsi KTP',
            'Kota KTP',
            'Kecamatan KTP',
            'Kelurahan KTP',
            'Alamat KTP',
            'Provinsi Domisili',
            'Kota Domisili',
            'Kecamatan Domisili',
            'Kelurahan Domisili',
            'Alamat Domisili',
            'Pendidikan',
            'Apakah Dokter? (Ya/Tidak)',
            'Spesialisasi',
            'Pekerjaan',
            'Nama Instansi/Perusahaan',
            'Golongan Darah',
            'Resus',
            'Riwayat Penyakit? (Ada/Tidak Ada)',
            'Kontak Darurat',
            'Nama Kontak Darurat',
            'Relasi Kontak Darurat',
            'Alamat Kontak Darurat',
            'Punya Passport? (Ya/Belum)',
            'Nama Lengkap di Passport',
            'Nomor Passport',
            'Tanggal Terbit Passport',
            'Tanggal Kadaluarsa Passport',
            'Penerbit Passport',
            'Booking Order',
            'Package (Ruby/Emerald/Sapphire/VIP/Plus)',
            'Room Type (Double/Triple/Quad/Queen/Single)',
            'Ukuran Badan (S,M,L,XL)',
            'Infants',
            'participant_id',
            'Crew Role (Tour Leader, Mutawwif, Handling, Crew)',
            'Location'
        ];
    }

    /**
    * @var Participant $participant
    */
    public function map($participant): array
    {
        $this->rowNumber += 1;

        $crewRole = 'Crew';
        if($participant->crew_role_id == 1) 
            $crewRole = 'Tour Leader';
        if($participant->crew_role_id == 2) 
            $crewRole = 'Mutawwif';
        if($participant->crew_role_id == 3) 
            $crewRole = 'Handling';
        if($participant->crew_role_id == 4) 
            $crewRole = 'Crew';

        return [
            $this->rowNumber,
            $participant->title,
            $participant->front_title,
            $participant->name,
            $participant->back_title,
            $participant->fathers_name,
            ($participant->gender == 1) ? 'Pria' : 'Wanita',
            $participant->birth_place,
            Date::dateTimeToExcel(\Carbon\Carbon::parse($participant->birth_date)),
            "'".$participant->nik,
            ($participant->married_status == 1) ? 'Menikah' : 'Belum Menikah',
            $participant->nationality,
            "'".$participant->no_hp,
            $participant->email,
            $participant->instagram,
            $participant->ktp_province,
            $participant->ktp_city,
            $participant->ktp_kecamatan,
            $participant->ktp_kelurahan,
            $participant->ktp_address,
            $participant->home_province,
            $participant->home_city,
            $participant->home_kecamatan,
            $participant->home_kelurahan,
            $participant->home_address,
            $participant->education,
            ($participant->is_doctor == 1) ? 'Ya' : 'Tidak',
            $participant->doctor_specialist,
            $participant->job,
            $participant->company_name,
            $participant->blood_type,
            null,
            $participant->medical_record,
            "'".$participant->emergency_contact,
            $participant->emergency_contact_name,
            $participant->emergency_relation,
            $participant->emergency_address,
            ($participant->have_passport == 1) ? 'Ya' : 'Tidak',
            $participant->name_in_passport,
            $participant->no_passport,
            Date::dateTimeToExcel(\Carbon\Carbon::parse($participant->passport_published_date)),
            Date::dateTimeToExcel(\Carbon\Carbon::parse($participant->passport_expired_date)),
            $participant->passport_held_by,
            $participant->suggest_booking_order,
            $participant->suggest_package,
            ucfirst($participant->suggest_room),
            $participant->body_size,
            ($participant->infants == 1) ? 'Y' : 'T',
            $participant->id,
            $crewRole,
            $participant->location,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = Participant::select('participant.*','crews.location','crews.crew_role_id')
        ->join('crews', 'participant.id', '=', 'crews.participant_id')
        ->leftJoin('participant_umroh_trips', 'participant.id', '=', 'participant_umroh_trips.participant_id')
        ->leftJoin('umroh_trips', 'umroh_trips.id', '=', 'participant_umroh_trips.umroh_trip_id');
        if (! empty($this->umrohTripId)) {
            $query->where('participant_umroh_trips.umroh_trip_id', $this->umrohTripId);
        }
        $query->orderBy('participant.name', 'asc');
        return $query;
    }

     /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $phpSpreadSheet = $event->sheet->getDelegate();
                $phpSpreadSheet->getColumnDimension('AW')->setVisible(false);
                $phpSpreadSheet->getColumnDimension('AW')->setAutoSize(false)->setWidth(0);

            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AO' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AP' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
