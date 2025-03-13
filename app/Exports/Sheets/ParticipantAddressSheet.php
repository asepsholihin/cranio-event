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

class ParticipantAddressSheet implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents, WithColumnFormatting, WithTitle
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
            'Nama Lengkap Sesuai KTP',
            'No. HP',
            'Gender',
            'Ukuran Outer',
            'Paket',
            'Provinsi Domisili',
            'Kota Domisili',
            'Kecamatan Domisili',
            'Kelurahan Domisili',
            'Kode Pos',
            'Alamat Domisili',
            'participant_id',
        ];
    }

    /**
    * @var Participant $participant
    */
    public function map($participant): array
    {
        $this->rowNumber += 1;
        return [
            $this->rowNumber,
            $participant->name,
            "'".$participant->no_hp,
            ($participant->gender == 1) ? 'L' : 'P',
            $participant->body_size,
            $participant->package_name,
            $participant->home_province,
            $participant->home_city,
            $participant->home_kecamatan,
            $participant->home_kelurahan,
            $participant->home_postalcode,
            $participant->home_address,
            $participant->id
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = Participant::select(['participant.*', 'package_umroh_trips.name as package_name',
        DB::raw('
            (CASE 
                WHEN package_umroh_trips.name iLIKE \'%Silver%\' THEN \'e\'
                WHEN package_umroh_trips.name iLIKE \'%Rub%\' THEN \'d\' 
                WHEN package_umroh_trips.name iLIKE \'%Emerald%\' THEN \'c\' 
                WHEN package_umroh_trips.name iLIKE \'%Gold%\' THEN \'b\' 
                WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'a\' 
                ELSE \'f\' END
            ) AS package_type'
        )])
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
                $phpSpreadSheet->getColumnDimension('M')->setVisible(false);
                $phpSpreadSheet->getColumnDimension('M')->setAutoSize(false)->setWidth(0);

            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            // 'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            // 'AP' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            // 'AQ' => NumberFormat::FORMAT_DATE_DDMMYYYY,
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
