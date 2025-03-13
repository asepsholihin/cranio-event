<?php

namespace App\Exports;

use App\Models\Equipment;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Carbon\Carbon;

class EquipmentExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents, WithColumnFormatting
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
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'SKU',
            'Gender',
            'Ukuran',
            'Paket',
            'Jenis',
            'Kategori',
            'Unit',
            'QTY',
            'Harga per unit',
            'Apakah Item Manasik?',
        ];
    }

    /**
     * @var Equipment $equipment
     */
    public function map($equipment): array
    {
        $this->rowNumber += 1;

        $gender = 'Keduanya';
        if($equipment->gender == 1) {
            $gender = 'Laki-laki';
        } else if($equipment->gender == 2) {
            $gender = 'Perempuan';
        }

        return [
            $this->rowNumber,
            $equipment->name,
            $equipment->sku_id,
            $gender,
            ($equipment->size_name) ?? 'Tidak ada size',
            ($equipment->package_type_name) ?? 'All Packages',
            $equipment->category_name,
            ($equipment->trip_category_name) ?? 'All Trip',
            $equipment->unit_name,
            $equipment->qty,
            $equipment->price_per_unit,
            ($equipment->is_manasik) ? 'Iya' : 'Bukan',
            $equipment->id,

        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        $query = Equipment::select([
            'equipments.*',
            'packages.name as package_type_name',
            'equipment_sizes.name as size_name',
            'equipment_units.name as unit_name',
            'equipment_categories.name as category_name',
            'web_categories.name as trip_category_name'
        ])
        ->leftjoin('packages', 'equipments.package_type', 'packages.id')
        ->leftjoin('equipment_sizes', 'equipments.size', 'equipment_sizes.id')
        ->leftjoin('equipment_units', 'equipments.unit_id', 'equipment_units.id')
        ->leftjoin('equipment_categories', 'equipments.category_id', 'equipment_categories.id')
        ->leftjoin('web_categories', 'equipments.trip_category_id', 'web_categories.id');
        if (!empty($this->request['package_type'])) {
            $query->where('package_type', $this->request['package_type']);
        }
        if (!empty($this->request['category_id'])) {
            $query->where('category_id', $this->request['category_id']);
        }
        if (!empty($this->request['trip_category_id'])) {
            $query->where('trip_category_id', $this->request['trip_category_id']);
        }
        if (!empty($this->request['gender'])) {
            $query->where('gender', $this->request['gender']);
        }
        if (!empty($this->request['is_manasik'])) {
            $query->where('is_manasik', $this->request['is_manasik']);
        }
        $query->orderBy('equipments.sku_id', 'desc');

        return $query;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $phpSpreadSheet = $event->sheet->getDelegate();
                $phpSpreadSheet->getColumnDimension('M')->setVisible(false);
                $phpSpreadSheet->getColumnDimension('M')->setAutoSize(false)->setWidth(0);

                // GENDER
                $configGender = "Keduanya,Laki-laki,Perempuan";
                $objValidation = $phpSpreadSheet->getCell('D2')->getDataValidation();
                $objValidation->setType(DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Pilihan tidak sesuai.');
                $objValidation->setPromptTitle('Pilihan');
                $objValidation->setPrompt('Silahkan pilih dari pilihan yang tersedia');
                $objValidation->setFormula1('"' . $configGender . '"');
                for ($i = 3; $i <= $this->rowNumber; $i++) {
                    $phpSpreadSheet->getCell("D{$i}")->setDataValidation(clone $objValidation);
                }

                // SIZE
                $sizes = DB::table('equipment_sizes')->where('status', 1)->whereNull('deleted_at')->pluck('name');
                $sizes_ = "Tidak ada size,";
                foreach ($sizes as $value) {
                    $sizes_ .= $value . ",";
                }
                $configSize = $sizes_;
                $objValidation = $phpSpreadSheet->getCell('E2')->getDataValidation();
                $objValidation->setType(DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Pilihan tidak sesuai.');
                $objValidation->setPromptTitle('Pilihan');
                $objValidation->setPrompt('Silahkan pilih dari pilihan yang tersedia');
                $objValidation->setFormula1('"' . $configSize . '"');
                for ($i = 3; $i <= $this->rowNumber; $i++) {
                    $phpSpreadSheet->getCell("E{$i}")->setDataValidation(clone $objValidation);
                }

                // PACKAGE
                $configPackage = "All Packages,Sapphire,Emerald,Ruby,Onyx,Yaqin Umroh,Lebih Hemat,Sapphire Plus,Gold,Silver";
                $objValidation = $phpSpreadSheet->getCell('F2')->getDataValidation();
                $objValidation->setType(DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Pilihan tidak sesuai.');
                $objValidation->setPromptTitle('Pilihan');
                $objValidation->setPrompt('Silahkan pilih dari pilihan yang tersedia');
                $objValidation->setFormula1('"' . $configPackage . '"');
                for ($i = 3; $i <= $this->rowNumber; $i++) {
                    $phpSpreadSheet->getCell("F{$i}")->setDataValidation(clone $objValidation);
                }

                // CATEGORY
                $categories = DB::table('equipment_categories')->where('status', 1)->whereNull('deleted_at')->pluck('name');
                $categories_ = "";
                foreach ($categories as $value) {
                    $categories_ .= $value . ",";
                }
                $configCategory = rtrim($categories_, ",");
                $objValidation = $phpSpreadSheet->getCell('G2')->getDataValidation();
                $objValidation->setType(DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Pilihan tidak sesuai.');
                $objValidation->setPromptTitle('Pilihan');
                $objValidation->setPrompt('Silahkan pilih dari pilihan yang tersedia');
                $objValidation->setFormula1('"' . $configCategory . '"');
                for ($i = 3; $i <= $this->rowNumber; $i++) {
                    $phpSpreadSheet->getCell("G{$i}")->setDataValidation(clone $objValidation);
                }

                // TRIP CATEGORY
                $tripcategories = DB::table('web_categories')->where('status', 1)->pluck('name');
                $tripcategories_ = "All Trip,";
                foreach ($tripcategories as $value) {
                    $tripcategories_ .= $value . ",";
                }
                $configTripCategory = $tripcategories_;
                $objValidation = $phpSpreadSheet->getCell('H2')->getDataValidation();
                $objValidation->setType(DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Pilihan tidak sesuai.');
                $objValidation->setPromptTitle('Pilihan');
                $objValidation->setPrompt('Silahkan pilih dari pilihan yang tersedia');
                $objValidation->setFormula1('"' . $configTripCategory . '"');
                for ($i = 3; $i <= $this->rowNumber; $i++) {
                    $phpSpreadSheet->getCell("H{$i}")->setDataValidation(clone $objValidation);
                }

                // UNIT
                $units = DB::table('equipment_units')->where('status', 1)->whereNull('deleted_at')->pluck('name');
                $units_ = "";
                foreach ($units as $value) {
                    $units_ .= $value . ",";
                }
                $configUnit = rtrim($units_, ",");
                $objValidation = $phpSpreadSheet->getCell('I2')->getDataValidation();
                $objValidation->setType(DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Pilihan tidak sesuai.');
                $objValidation->setPromptTitle('Pilihan');
                $objValidation->setPrompt('Silahkan pilih dari pilihan yang tersedia');
                $objValidation->setFormula1('"' . $configUnit . '"');
                for ($i = 3; $i <= $this->rowNumber; $i++) {
                    $phpSpreadSheet->getCell("I{$i}")->setDataValidation(clone $objValidation);
                }

                // MANASIK
                $configManasik = "Bukan,Iya";
                $objValidation = $phpSpreadSheet->getCell('L2')->getDataValidation();
                $objValidation->setType(DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Pilihan tidak sesuai.');
                $objValidation->setPromptTitle('Pilihan');
                $objValidation->setPrompt('Silahkan pilih dari pilihan yang tersedia');
                $objValidation->setFormula1('"' . $configManasik . '"');
                for ($i = 3; $i <= $this->rowNumber; $i++) {
                    $phpSpreadSheet->getCell("L{$i}")->setDataValidation(clone $objValidation);
                }
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            
        ];
    }
}
