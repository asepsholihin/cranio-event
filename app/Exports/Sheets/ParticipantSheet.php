<?php

namespace App\Exports\Sheets;

use App\Models\Participant;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ParticipantSheet implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents, WithColumnFormatting, WithTitle
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
            'Umur',
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
            'Pendidikan',
            'Apakah Dokter? (Ya/Tidak)',
            'Spesialisasi',
            'Pekerjaan',
            'Nama Instansi/Perusahaan',
            'Golongan Darah',
            'Resus',
            'Riwayat Penyakit? (Ada/Tidak Ada)',
            'Keterangan Penyakit',
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
            'Apakah Tenaga Kesehatan? (Ya/Tidak)',
            'Apakah TNI/POLRI? (Ya/Tidak)',
            'participant_id',
            'participant_umroh_trip_id',
            'ji_code',
        ];
    }

    /**
    * @var Participant $participant
    */
    public function map($participant): array
    {
        $this->rowNumber += 1;

        $married_status = '';
        if($participant->married_status == 1) $married_status = 'Menikah';
        if($participant->married_status == 2) $married_status = 'Belum Menikah';
        if($participant->married_status == 3) $married_status = 'Cerai';

        return [
            $participant->no_urut,
            $participant->title,
            $participant->front_title,
            $participant->name,
            $participant->back_title,
            $participant->fathers_name,
            ($participant->gender == 1) ? 'Pria' : 'Wanita',
            $participant->birth_place,
            Date::dateTimeToExcel(\Carbon\Carbon::parse($participant->birth_date)),
            $participant->birth_date ? \Carbon\Carbon::createFromFormat('Y-m-d', $participant->birth_date)->age . ' Tahun' : null,
            "'".$participant->nik,
            $married_status,
            $participant->nationality,
            "'".$participant->no_hp,
            $participant->email,
            $participant->instagram,
            $participant->ktp_province,
            $participant->ktp_city,
            $participant->ktp_kecamatan,
            $participant->ktp_kelurahan,
            strtoupper($participant->ktp_address),
            $participant->education,
            ($participant->is_doctor == 1) ? 'Ya' : 'Tidak',
            $participant->doctor_specialist,
            $participant->job,
            $participant->company_name,
            $participant->blood_type,
            null,
            $participant->medical_record,
            $participant->medical_description,
            $participant->emergency_contact ? "'".$participant->emergency_contact : null,
            $participant->emergency_contact_name,
            $participant->emergency_relation,
            $participant->emergency_address,
            ($participant->have_passport == 1) ? 'Ya' : 'Tidak',
            $participant->name_in_passport,
            $participant->no_passport,
            Date::dateTimeToExcel(\Carbon\Carbon::parse($participant->passport_published_date)),
            Date::dateTimeToExcel(\Carbon\Carbon::parse($participant->passport_expired_date)),
            $participant->passport_held_by,
            $participant->booking_order_no,
            $participant->package_name,
            ucfirst($participant->room_type),
            $participant->body_size,
            ($participant->infants == 1) ? 'Y' : 'T',
            ($participant->is_nakes == 1) ? 'Ya' : 'Tidak',
            ($participant->is_tni_polri == 1) ? 'Ya' : 'Tidak',
            $participant->id,
            $participant->participant_umroh_trip_id,
            $participant->ji_code
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = Participant::select(['participant.*',
        'participant_umroh_trips.no_urut',
        'participant_umroh_trips.id as participant_umroh_trip_id',
        DB::raw('
            (CASE 
                WHEN package_umroh_trips.name iLIKE \'%Silver%\' THEN \'e\'
                WHEN package_umroh_trips.name iLIKE \'%Rub%\' THEN \'d\' 
                WHEN package_umroh_trips.name iLIKE \'%Emerald%\' THEN \'c\' 
                WHEN package_umroh_trips.name iLIKE \'%Gold%\' THEN \'b\' 
                WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'a\' 
                ELSE \'f\' END
            ) AS package_type'
        ),
        'participant_umroh_trips.room_type',
        'participant_umroh_trips.booking_order_no',
        'package_umroh_trips.name as package_name'])
        ->leftJoin('participant_umroh_trips', 'participant.id', '=', 'participant_umroh_trips.participant_id')
        ->join('package_umroh_trips', 'package_umroh_trips.id', '=', 'participant_umroh_trips.package_umroh_trip_id')
        ->join('umroh_trips', 'umroh_trips.id', '=', 'participant_umroh_trips.umroh_trip_id');
        if (! empty($this->umrohTripId)) {
            // $query->whereIn('participant_umroh_trips.role_type', array(1,4));
            $query->where('participant_umroh_trips.umroh_trip_id', $this->umrohTripId);
        }
        $query->orderByRaw('no_urut ASC, package_type ASC, suggest_booking_order ASC NULLS LAST, participant.name ASC');
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
                $phpSpreadSheet->getColumnDimension('AV')->setVisible(false);
                $phpSpreadSheet->getColumnDimension('AV')->setAutoSize(false)->setWidth(0);
                $phpSpreadSheet->getColumnDimension('AW')->setVisible(false);
                $phpSpreadSheet->getColumnDimension('AW')->setAutoSize(false)->setWidth(0);

                $arrayCols = ["B1","D1","G1","K1","M1","N1"];
                $phpSpreadSheet->getStyle("AV1:AX1")->applyFromArray([
                    'font' => ['color' => ['rgb' => 'ad0c0c']],'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'startColor' => ['rgb' => 'ffb8b8']]
                ]);
                foreach ($arrayCols as $value) {
                    $phpSpreadSheet->getStyle($value)->applyFromArray([
                        'font' => ['color' => ['rgb' => 'ad0c0c']],'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'startColor' => ['rgb' => 'ffb8b8']]
                    ]);
                }

            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AL' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AM' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "Participant";
    }
}
