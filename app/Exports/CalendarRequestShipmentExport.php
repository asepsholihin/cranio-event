<?php

namespace App\Exports;

use App\Models\CalendarRequest;
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
use Carbon\Carbon;

class CalendarRequestShipmentExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents
{
    private $rowNumber;
    private $request;

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
        return $rows->transform(function ($calendarRequest) {
            return $calendarRequest;
        });
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nama Penerima',
            'Alamat',
            'Kota/Kab',
            'Kode Pos',
            'Kecamatan',
            'Provinsi',
            'Nama Participant',
            'Kontak/No HP'
        ];
    }

    /**
    * @var CalendarRequest $calendarRequest
    */
    public function map($calendarRequest): array
    {
        $this->rowNumber += 1;
        return [
            $this->rowNumber,
            $calendarRequest->recipient_name,
            $calendarRequest->address,
            $calendarRequest->city,
            $calendarRequest->postalcode,
            $calendarRequest->province,
            $calendarRequest->subdistrict,
            $calendarRequest->name,
            $calendarRequest->recipient_phone ?? $calendarRequest->no_hp,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = CalendarRequest::select('*');
        if (!empty($this->request['packingDate'])) {
            $dateXplode = explode('to', $this->request['packingDate']);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
            $query->whereBetween('calendar_requests.process_date', [$start, $end]);
        }
        if (!empty($this->request['requestDate'])) {
            $dateXplode = explode('to', $this->request['requestDate']);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
            $query->whereBetween('calendar_requests.created_at', [$start, $end]);
        }
        if (!empty($this->request['status'])) {
            $query->where('calendar_requests.status', $this->request['status']);
        }
        $query->orderByRaw('id ASC, process_date ASC, pickup_date ASC');
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
                $phpSpreadSheet->getColumnDimension('B')->setAutoSize(false)->setWidth(35);
                $phpSpreadSheet->getColumnDimension('C')->setAutoSize(false)->setWidth(60);
                $phpSpreadSheet->getColumnDimension('H')->setAutoSize(false)->setWidth(35);
            },
        ];
    }
}
