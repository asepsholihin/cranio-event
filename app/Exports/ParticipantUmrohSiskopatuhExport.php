<?php

namespace App\Exports;

use App\Models\ParticipantUmrohTrip;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Style;

class ParticipantUmrohSiskopatuhExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents
{
    private $umrohId;

    public function __construct($umrohId)
    {
        $this->umrohId = $umrohId;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
        ];
    }

    public function prepareRows($rows)
    {
        return $rows->transform(function ($participant) {
            $participant->gender = ($participant->gender == 1)? 'Laki-laki':'Perempuan';
            $participant->married_status = $this->marriedStatus($participant->married_status);
            $participant->nationality = $this->nationality($participant->nationality);
            $participant->nik = $this->nik($participant->nik);
            $participant->title = $this->title($participant->title);

            return $participant;
        });
    }

    private function marriedStatus($status)
    {
        if ($status == '1') {
            return 'Menikah';
        }

        if ($status == '2') {
            return 'Belum Menikah';
        }

        if ($status == '3') {
            return 'Janda';
        }

        return 'Duda';
    }

    private function nationality($nationality)
    {
        if ($nationality == 'Indonesia') {
            return 'WNI';
        }

        return 'WNA';
    }

    private function nik($nik)
    {
        return "'".$nik;
    }

    private function title($title)
    {
        if (str_contains($title, 'Mr')) { 
            return 'Tuan';
        }

        if (str_contains($title, 'Ms')) { 
            return 'Nona';
        }

        if (str_contains($title, 'Mrs')) { 
            return 'Nyonya';
        }

        if (str_contains($title, 'Mstr')) { 
            return 'Tuan';
        }

        if (str_contains($title, 'Miss')) { 
            return 'Nona';
        }

        return '';
    }

    public function headings(): array
    {
        return [
            'Title',
            'Nama (Sesuai Dengan nama Pada Kartu Vaksin)',
            'Nama Ayah',
            'Jenis Identitas',
            'No Identitas',
            'Nama Paspor',
            'No Paspor',
            'Tanggal Dikeluarkan Paspor(yyyy-mm-dd)',
            'Kota Paspor',
            'Tempat Lahir',
            'Tanggal Lahir(yyyy-mm-dd)',
            'Alamat',
            'Provinsi',
            'Kabupaten',
            'Kecamatan',
            'Kelurahan',
            'No. Telepon',
            'No Hp',
            'KewargaNegaraan',
            'Status Pernikahan',
            'Pendidikan',
            'Pekerjaan',
            'Provider Visa',
            'No Visa',
            'Tanggal Berlaku Visa (yyyy-mm-dd)',
            'Tanggal Akhir  Visa (yyyy-mm-dd)',
            'Asuransi',
            'No Polis',
            'Tanggal Input Polis (yyyy-mm-dd)',
            'Tanggal Awal Polis (yyyy-mm-dd)',
            'Tanggal Akhir Polis (yyyy-mm-dd)',
        ];
    }

    /**
    * @var ParticipantUmrohTrip $participant
    */
    public function map($participant): array
    {
        return [
            $participant->title,
            strtoupper($participant->full_name_vaccine ?? $participant->name),
            $participant->fathers_name,
            "NIK",
            $participant->nik,
            $participant->name_in_passport,
            $participant->no_passport,
            $participant->passport_published_date,
            str_ireplace("imigrasi","",$participant->passport_held_by),
            $participant->birth_place,
            $participant->birth_date,
            $participant->ktp_address,
            $participant->ktp_province,
            $participant->ktp_city,
            $participant->ktp_kecamatan,
            $participant->ktp_kelurahan,
            null,
            $participant->no_hp,
            $participant->nationality,
            $participant->married_status,
            $participant->education,
            $participant->job,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return ParticipantUmrohTrip::join('participants', 'participant_umroh_trips.participant_id', '=', 'participant.id')
            ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', '=', 'package_umroh_trips.id')
            ->join('umroh_trips', 'participant_umroh_trips.umroh_trip_id', '=', 'umroh_trips.id')
            ->select([
                'participant.title',
                'participant.name',
                'participant.full_name_vaccine',
                'participant.fathers_name',
                'participant.nik',
                'participant.name_in_passport',
                'participant.no_passport',
                'participant.passport_published_date',
                'participant.passport_held_by',
                'participant.birth_place',
                'participant.birth_date',
                'participant.ktp_address',
                'participant.ktp_province',
                'participant.ktp_city',
                'participant.ktp_kecamatan',
                'participant.ktp_kelurahan',
                'participant.no_hp',
                'participant.nationality',
                'participant.married_status',
                'participant.education',
                'participant.job',
                DB::raw('(CASE 
                    WHEN package_umroh_trips.name iLIKE \'%Rub%\' THEN \'c\' 
                    WHEN package_umroh_trips.name iLIKE \'%Emerald%\' THEN \'b\' 
                    WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'a\' 
                    ELSE \'c\' END
                ) AS package_type'),
                DB::raw('(CASE WHEN participant_umroh_trips.without_ticket = 1 THEN \'z\' ELSE \'a\' END) AS without_ticket'),
            ])
            ->where('participant_umroh_trips.role_type', 1)
            ->where('participant_umroh_trips.umroh_trip_id', $this->umrohId)
            ->orderByRaw('no_urut ASC NULLS LAST');
    }

     /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $phpSpreadSheet = $event->sheet->getDelegate();
                $phpSpreadSheet->getRowDimension('1')->setRowHeight(40, 'pt');

                $participant = ParticipantUmrohTrip::join('participants', 'participant_umroh_trips.participant_id', '=', 'participant.id')
                ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', '=', 'package_umroh_trips.id')
                ->join('umroh_trips', 'participant_umroh_trips.umroh_trip_id', '=', 'umroh_trips.id')
                ->select([
                    'participant.title',
                    'participant.name',
                    'participant.fathers_name',
                    'participant.nik',
                    'participant.name_in_passport',
                    'participant.no_passport',
                    'participant.passport_published_date',
                    'participant.passport_held_by',
                    'participant.birth_place',
                    'participant.birth_date',
                    'participant.ktp_address',
                    'participant.ktp_province',
                    'participant.ktp_city',
                    'participant.ktp_kecamatan',
                    'participant.ktp_kelurahan',
                    'participant.no_hp',
                    'participant.nationality',
                    'participant.married_status',
                    'participant.education',
                    'participant.job',
                    DB::raw('(CASE 
                        WHEN package_umroh_trips.name iLIKE \'%Rub%\' THEN \'c\' 
                        WHEN package_umroh_trips.name iLIKE \'%Emerald%\' THEN \'b\' 
                        WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'a\' 
                        ELSE \'c\' END
                    ) AS package_type'),
                    DB::raw('(CASE WHEN participant_umroh_trips.without_ticket = 1 THEN \'z\' ELSE \'a\' END) AS without_ticket'),
                ])
                ->where('participant_umroh_trips.role_type', 1)
                ->where('participant_umroh_trips.umroh_trip_id', $this->umrohId)
                ->orderByRaw('no_urut ASC NULLS LAST')->get();

                $i = 2;
                foreach ($participant as $k => $v) {
                    if($v->without_ticket == 'z') {
                        $event->sheet->getStyle('A'.($i+$k).':AE'.($i+$k))->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'fccc49']
                            ]
                        ]);
                    }
                }
            },
        ];
    }
}
