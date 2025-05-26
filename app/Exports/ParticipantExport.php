<?php

namespace App\Exports;

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

class ParticipantExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents, WithColumnFormatting, WithTitle
{
    private $rowNumber;

    public function __construct()
    {
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
            'Jenis Kelamin (Pria/Wanita)',
            'Nomor KTP',
            'Whatsapp / No HP',
            'Email',
            'Hospital',
            'Polo Size',
            'Request',
            'Room Info'
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
            ($participant->gender == 1) ? 'Pria' : 'Wanita',
            "'".$participant->nik,
            "'".$participant->whatsapp,
            $participant->email,
            $participant->hospital,
            $participant->polo_size,
            $participant->request,
            $participant->room_number
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = Participant::select(['participants.*',
        DB::raw("(SELECT account_hospital FROM bookings JOIN participant_bookings ON bookings.id = participant_bookings.booking_id WHERE participant_bookings.participant_id = participants.id ORDER BY bookings.id DESC) as hospital"),
        ]);
        $query->orderByRaw('participants.name ASC');
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
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
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
