<?php

namespace App\Exports;

use App\Models\LogLetter;
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

class LogLetterExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents
{
    private $request;
    private $rowNumber;

    public function __construct($request)
    {
        $this->request = $request;
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
        return $rows->transform(function ($logLetter) {
            return $logLetter;
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Surat',
            'Tanggal Surat',
            'Perihal',
            'Nama Surat',
            'Tujuan',
            'PIC'
        ];
    }

    /**
    * @var LogLetter $logLetter
    */
    public function map($logLetter): array
    {
        $this->rowNumber += 1;
        return [
            $this->rowNumber,
            $logLetter->letter_number,
            $logLetter->generate_date,
            $logLetter->purpose,
            $logLetter->name,
            '',
            $logLetter->generated_by_name
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = LogLetter::orderByRaw('created_at ASC NULLS LAST');
        $query->select(['log_letters.*', 'users.name as generated_by_name']);
        $query->join('users', 'users.id', 'log_letters.generated_by');

        $search = '%' . $this->request['q'].'%';
        if(!empty($this->request['q'])) {
            $query->where(function($q) use($search) {
                $q->where('letter_number', 'like', $search)
                ->orWhere('purpose', 'like', $search);
            });
        }
        if(!empty($this->request['generatedBy'])) {
            $query->where('users.name', 'like', $this->request['generatedBy']);
        }
        if(!empty($this->request['date'])) {
            $dateXplode = explode('to', $this->request['date']);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
            $query->whereBetween('log_letters.generate_date', [$start, $end]);
        }
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
                $phpSpreadSheet->getRowDimension('1')->setRowHeight(40, 'pt');
            },
        ];
    }
}
