<?php

namespace App\Exports;

use App\Models\EquipmentDelivery;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
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

class SalesPerformanceDailyExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithEvents
{
    const START_ROW = 1;
    private $rowNumber;
    private $data;

    public function __construct($data)
    {
        $this->rowNumber = 0;
        $this->data = $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'A' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'C' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'D' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'E' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'F' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'G' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'H' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'I' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'J' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'K' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
        ];
    }

    public function headings(): array
    {
        $array = $this->data['fields'];
        array_unshift($array, 'Closing');
        array_unshift($array, 'Leads');
        array_unshift($array, 'Sales Name');
        array_unshift($array, 'No.');
        return $array;
    }

    public function array(): array
    {
        $array = array();
        foreach ($this->data['items'] as $row) {
            $this->rowNumber += 1;
            array_unshift($row['data'], $row['closing']);
            array_unshift($row['data'], $row['leads']);
            array_unshift($row['data'], $row['sales_name']);
            array_unshift($row['data'], $this->rowNumber);

            $data = array();
            foreach ($row['data'] as $value) {
                if($value == 0) {
                    $value = '-';
                }
                $data[] = $value;
            }
            $array[] = $data;
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
                $phpSpreadSheet->getRowDimension('1')->setRowHeight(40, 'pt');
                $phpSpreadSheet->getStyle('A1:K1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFFF00'); 
                $phpSpreadSheet->getStyle("A1:K" . ($this->rowNumber + self::START_ROW))->applyFromArray([
                    'font' => [
                        'name' => 'Mulish',
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);
                $phpSpreadSheet->getStyle("A1:K" . ($this->rowNumber + self::START_ROW))->applyFromArray([
                    'font' => [
                        'name' => 'Mulish',
                    ]
                ]); 
            },
        ];
    }
}
