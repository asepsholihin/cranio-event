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

class ParticipantUmrohImportSiskopatuhExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents
{
    private $umrohId;
    private $rowNumber;

    public function __construct($umrohId)
    {
        $this->umrohId = $umrohId;
        $this->rowNumber = 0;
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
            $participant->nik = $this->nik($participant->nik);

            return $participant;
        });
    }

    private function nik($nik)
    {
        return "'".$nik;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Participant',
            'NIK',
            'Nomor Passport',
            'Kode Siskopatuh',
            'Ticket Number',
            'Insurance Number',
            'Visa Number',
            'Nomor Porsi',
            'Nomor SPPH'
        ];
    }

    /**
    * @var ParticipantUmrohTrip $participant
    */
    public function map($participant): array
    {
        $this->rowNumber += 1;
        return [
            $this->rowNumber,
            strtoupper($participant->full_name_vaccine ?? $participant->name),
            $participant->nik,
            $participant->no_passport,
            $participant->code_siskopatuh,
            $participant->ticket_number,
            $participant->insurance_number,
            $participant->visa_number,
            $participant->nomor_porsi,
            $participant->nomor_spph,
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
                'participant.name',
                'participant.full_name_vaccine',
                'participant.nik',
                'participant.no_passport',
                'participant_umroh_trips.code_siskopatuh',
                'participant_umroh_trips.ticket_number',
                'participant_umroh_trips.insurance_number',
                'participant_umroh_trips.visa_number',
                'participant_umroh_trips.nomor_porsi',
                'participant_umroh_trips.nomor_spph',
                DB::raw('(CASE 
                    WHEN package_umroh_trips.name iLIKE \'%Rub%\' THEN \'c\' 
                    WHEN package_umroh_trips.name iLIKE \'%Emerald%\' THEN \'b\' 
                    WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'a\' 
                    ELSE \'c\' END
                ) AS package_type'),
            ])
            ->whereNull('participant_umroh_trips.code_siskopatuh')
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
            },
        ];
    }
}
