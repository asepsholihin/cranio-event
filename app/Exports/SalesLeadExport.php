<?php

namespace App\Exports;

use App\Models\SalesLead;
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

class SalesLeadExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents
{
    const START_ROW = 1;
    private $rowNumber;

    public function __construct()
    {
        $this->rowNumber = 0;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'A' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'B' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'D' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'E' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            'I' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
        ];
    }

    public function prepareRows($rows)
    {
        return $rows->transform(function ($salesLead) {
            return $salesLead;
        });
    }

    public function headings(): array
    {
        return [
            'No.',
            'Date',
            'Sales',
            'Lead Source',
            'Package Type',
            'Nama Participant',
            'No. HP',
            'Kota',
            'Status',
            'Respon',
        ];
    }

    /**
    * @var SalesLead $salesLead
    */
    public function map($salesLead): array
    {
        $this->rowNumber += 1;
        $status = "COLD";
        if ($salesLead->status == 1)
            $tatus = 'COLD';
        if ($salesLead->status == 2) 
            $tatus = 'WARM';
        if ($salesLead->status == 3) 
            $tatus = 'HOT';
        if ($salesLead->status == 4) 
            $tatus = 'NOT DEAL';

        return [
            $this->rowNumber,
            ($salesLead->date) ? Carbon::parse($salesLead->date)->isoFormat('D MMMM Y') : null,
            $salesLead->sales_name,
            $salesLead->lead_source,
            $salesLead->package_type,
            $salesLead->name,
            $salesLead->no_hp,
            $salesLead->city,
            $status,
            $salesLead->lead_response,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = SalesLead::select(['sales_leads.*', 'master_lead_sources.title as lead_source', 'sales_leads.lead_response', 'master_package_types.title as package_type', 'log_performance_invoices.id as performance_invoice_id']);
        $query->join('master_lead_sources', 'sales_leads.lead_source_id', 'master_lead_sources.id');
        $query->join('master_package_types', 'sales_leads.package_type_id', 'master_package_types.id');
        $query->leftjoin('log_performance_invoices', 'sales_leads.id', 'log_performance_invoices.sales_lead_id');

        if(auth()->user()->id != 1) {
            // Bukan Admin
            if(in_array(9, auth()->user()->department_ids) || in_array(1, auth()->user()->department_ids)) {
                // Sales Manager
            } else if(in_array(8, auth()->user()->department_ids)) {
                // Kepala Cabang
                $query->join('users', 'users.id', 'sales_leads.created_by')->join('master_office_user', 'master_office_user.user_id', 'users.id');
                $query->whereIn('master_office_user.office_id', auth()->user()->office_ids);
            } else {
                $query->where('sales_leads.created_by', auth()->user()->id);
            }
        }
        if (!empty(request()->query('status'))) {
            $query->where('status', request()->query('status'));
        }
        if (!empty(request()->query('city'))) {
            $query->where('city', request()->query('city'));
        }
        if (!empty(request()->query('date'))) {
            $dateXplode = explode('to', request()->query('date'));
            $start = date('Y-m-d', strtotime($dateXplode[0]));
            $end = date('Y-m-d', strtotime($dateXplode[1]??$dateXplode[0]));
            $query->whereBetween('sales_leads.created_at', [$start, $end]);
        }
        if (request()->query('q', '')) {
            $search = '%' . request()->query('q') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('sales_name', 'like', $search)
                    ->orWhere('name', 'like', $search)
                    ->orWhere('city', 'like', $search)
                    ->orWhere('no_hp', 'like', $search);
            });
        }
        $query->orderByRaw('created_at DESC');
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
                $phpSpreadSheet->getStyle('A1:J1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFFF00'); 
                $phpSpreadSheet->getStyle("A1:J" . ($this->rowNumber + self::START_ROW))->applyFromArray([
                    'font' => [
                        'name' => 'Mulish',
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);
                $phpSpreadSheet->getStyle("A1:J" . ($this->rowNumber + self::START_ROW))->applyFromArray([
                    'font' => [
                        'name' => 'Mulish',
                    ]
                ]); 
            },
        ];
    }
}
