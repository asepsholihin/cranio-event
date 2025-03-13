<?php

namespace App\Imports;

use App\Exceptions\ErrorMessageException;
use App\Models\Participant;
use App\Models\LeadSource;
use App\Models\PackageType;
use App\Models\WebSale;
use App\Models\SalesLead;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class SalesLeadImport implements ToCollection, SkipsEmptyRows, WithStartRow, WithValidation, SkipsOnFailure, WithCalculatedFormulas
{
    use Importable, SkipsFailures;

    public function startRow(): int
    {
        return 4;
    }

    public function transformDate($value, $name, $columnName)
    {
        try {
            $date = \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
            $date = \Carbon\Carbon::parse($date)->format('Y-m-d');
            return $date;
        } catch (\Throwable $e) {
            Log::error('Error transformDate: ' . $e->getMessage());
            throw new ErrorMessageException("Invalid Date Format - {$columnName}: {$name}");
        }
    }

    public function transformLeadSource($value, $name, $columnName)
    {
        $leadSources = LeadSource::select('title')->pluck('title')->toArray();

        $availableLead = "";
        foreach ($leadSources as $lead) {
            $availableLead .= $lead . ", ";
        }
        $availableLead = rtrim($availableLead, ', ');

        if(in_array($value, $leadSources)) {
            $leadSource = LeadSource::select(['id', 'title'])->where('title', $value)->first();
            return $leadSource->id;
        } else {
            Log::error('Error Lead Source: tidak sesuai');
            throw new ErrorMessageException("Lead Source harus berisi ({$availableLead}) - a/n: {$name}");
        }
    }

    public function transformPackageType($value, $name, $columnName)
    {
        $packageTypes = PackageType::select('title')->pluck('title')->toArray();

        $availablePackage = "";
        foreach ($packageTypes as $package) {
            $availablePackage .= $package . ", ";
        }
        $availablePackage = rtrim($availablePackage, ', ');

        if(in_array($value, $packageTypes)) {
            $packageType = PackageType::select(['id', 'title'])->where('title', $value)->first();
            return $packageType->id;
        } else {
            Log::error('Error Package Type: tidak sesuai');
            throw new ErrorMessageException("Package Type ({$value}) belum ada di Master Package Type - a/n: {$name}");
        }
    }

    public function transformSales($value, $name, $columnName)
    {
        $sales = WebSale::select('sales_name')->pluck('sales_name')->toArray();

        $availableSales = "";
        foreach ($sales as $row) {
            $availableSales .= $row . ", ";
        }
        $availableSales = rtrim($availableSales, ', ');

        if(in_array($value, $sales)) {
            $webSales = WebSale::select(['id', 'sales_name'])->where('sales_name', $value)->first();
            return $webSales->id;
        } else {
            Log::error('Error Sales: tidak sesuai');
            throw new ErrorMessageException("Sales harus berisi ({$availableSales}) a/n: {$name}");
        }
    }

    private function transformHP($phoneNumber) 
    {
        $phoneNumber = Str::replace('-', '', $phoneNumber);
        if (Str::startsWith($phoneNumber, '0')) {
            $phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $phoneNumber);
        }
        if (Str::startsWith($phoneNumber, '8')) {
            $phoneNumber = Participant::PREFIX_PHONE_NUMBER . $phoneNumber;
        }
        return $phoneNumber;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows){
            $data = array();
            foreach($rows as $row) {

                $status = 1;
                if($row[8] == TRUE) {
                    $status = 1;
                }
                if($row[9] == TRUE) {
                    $status = 2;
                }
                if($row[10] == TRUE) {
                    $status = 3;
                }
                if($row[11] == TRUE) {
                    $status = 5;
                }

                $name = $row[6] ?? '-';

                $update_column = array();
                $update_column['date'] = $this->transformDate($row[1], $name, 'Date');
                $update_column['sales_id'] = $this->transformSales($row[2], $name, 'Sales Name');
                $update_column['sales_name'] = $row[2];
                $update_column['no_hp'] = $this->transformHP($row[3]);
                $update_column['lead_source_id'] = 0;//$this->transformLeadSource($row[4], $name, 'Leads Source');
                $update_column['lead_source'] = $row[4];
                $update_column['package_type_id'] = $this->transformPackageType($row[5], $name, 'Type Package');
                $update_column['name'] = $name;
                $update_column['city'] = $row[7];
                $update_column['status'] = $status;
                $update_column['lead_response_id'] = 0;
                $update_column['lead_response'] = $row[12];
                $update_column['created_by'] = auth()->user()->id ?? 1;
                $update_column['updated_by'] = auth()->user()->id ?? 1;
                
                $data[] = $update_column;
                
                $checkSalesLead = SalesLead::where('date', $update_column['date'])->where('no_hp', $update_column['no_hp'])->first();
                if(empty($checkSalesLead)) {
                    SalesLead::create($update_column);
                }
            }
        });
    }

    public function rules(): array
    {
        return [
            '0' => 'required',
            '1' => 'required',
            '2' => 'required',
            '4' => 'required',
            '12' => 'required'
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '0.required' => 'Day harus di-isi',
            '1.required' => 'Date harus di-isi',
            '2.required' => 'Sales Name harus di-isi',
            '4.required' => 'Source harus di-isi',
            '12.required' => 'Lead Response harus di-isi'
        ];
    }
}
