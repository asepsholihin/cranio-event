<?php

namespace App\Exports\Sheets;

use App\Models\MasterAddress;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class MasterProvKabSheet implements FromArray, ShouldAutoSize, WithStyles, WithEvents, WithTitle
{
    private $rowNumber;

    public function __construct()
    {
        $this->rowNumber = 0;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A' => ['font' => ['bold' => true]],
        ];
    }

    // public function headings(): array
    // {
    //     $provinces = MasterAddress::select('province')->groupBy('province')->orderBy('province', 'ASC')->pluck('province')->toArray();
    //     return $provinces;
    // }

    public function array(): array
    {
        $provinces = MasterAddress::select('province')->groupBy('province')->orderBy('province', 'ASC')->pluck('province')->toArray();

        $array = array();
        foreach ($provinces as $province) {
            $cities = MasterAddress::select('city')->where('province', $province)->groupBy('province')->groupBy('city')->orderBy('city', 'ASC')->pluck('city')->toArray();
            array_unshift($cities, $province);
            $array[] = $cities;
        }

        return $array;
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

    /**
     * @return string
     */
    public function title(): string
    {
        return "ProvKab";
    }
}
