<?php

namespace App\Exports;

use App\Models\Inquiry;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InquiryExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping
{
    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Whatsapp',
            'Email',
            'Source Page',
            'Isi Message',
            'Tanggal (yyyy-mm-dd)'
        ];
    }

    /**
     * @var Inquiry $inquiry
     */
    public function map($inquiry): array
    {
        return [
            $inquiry->full_name,
            $inquiry->wa_number,
            $inquiry->email,
            $inquiry->from_page,
            $inquiry->message,
            $inquiry->created_at
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        return Inquiry::select([
            'full_name',
            'wa_number',
            'email',
            'message',
            'from_page',
            'created_at'
        ])->whereNull('deleted_at')->orderByRaw('from_page');
    }
}
