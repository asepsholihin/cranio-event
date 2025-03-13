<?php

namespace App\Imports;

use App\Exceptions\ErrorMessageException;
use App\Models\Equipment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class EquipmentImport implements ToCollection, SkipsEmptyRows, WithStartRow, WithValidation, SkipsOnFailure, WithMultipleSheets
{
    use Importable, SkipsFailures;

    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    public function startRow(): int
    {
        return 2;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows){
            foreach($rows as $row) {
                $update_column = array();
                if($row[1])
                    $update_column['name'] = $row[1];
                if($row[2])
                    $update_column['sku_id'] = $row[2];
                if($row[3])
                    $update_column['gender'] =  $this->checkGender($row[3], $row[1]);
                if($row[4])
                    $update_column['size'] = $this->checkSize($row[4], $row[1]);
                if($row[5])
                    $update_column['package_type'] = $this->checkPackage($row[5], $row[1]);
                if($row[6])
                    $update_column['category_id'] = $this->checkCategory($row[6], $row[1]);
                if($row[7])
                    $update_column['trip_category'] = $this->checkTripCategory($row[7], $row[1]);
                if($row[8])
                    $update_column['unit_id'] = $this->checkUnit($row[8], $row[1]);
                if($row[11])
                    $update_column['is_manasik'] = ($row[11] == 'Bukan') ? false : true;

                Equipment::updateOrCreate(
                    [
                        'id' => $row[12]
                    ],
                    $update_column
                );
            }
        });
    }

    public function rules(): array
    {
        return [
            '12' => 'required',
        ];
    }
    
    public function customValidationMessages()
    {
        return [
            '12.required' => 'Missing ID',
        ];
    }

    public function checkGender($value, $name)
    {
        $value = strtoupper($value);
        if($value == 'KEDUANYA') {
            return 3;
        }
        if($value != null || $value != '') {
            $value = rtrim($value, " ");
            if(in_array($value, array('KEDUANYA','LAKI-LAKI','PEREMPUAN'))){
                $gender = 3;
                if($value=='LAKI-LAKI') {
                    $gender = 1;
                } 
                if($value=='PEREMPUAN') {
                    $gender = 2;
                }
                return $gender;
            } else {
                Log::error('Error check: Gender tidak sesuai');
                throw new ErrorMessageException("Gender {$value} tidak sesuai - a/n {$name}");
            }
        }
    }

    public function checkSize($value, $name)
    {
        if($value == 'Tidak ada size') {
            return null;
        }
        if($value != null || $value != '') {
            $value = rtrim($value, " ");
            $size = DB::table('equipment_sizes')->where('name', $value)->first();
            if($size){
                return $size->id;
            } else {
                Log::error('Error check: Gender tidak sesuai');
                throw new ErrorMessageException("Gender {$value} tidak sesuai - a/n {$name}");
            }
        }
    }

    public function checkPackage($value, $name)
    {
        if($value == 'All Packages') {
            return null;
        }
        if($value != null || $value != '') {
            $value = rtrim($value, " ");
            $package = DB::table('packages')->where('name', $value)->first();
            if($package){
                return $package->id;
            } else {
                Log::error('Error check: Package tidak sesuai');
                throw new ErrorMessageException("Package {$value} tidak sesuai - a/n {$name}");
            }
        }
    }

    public function checkCategory($value, $name)
    {
        if($value != null || $value != '') {
            $value = rtrim($value, " ");
            $category = DB::table('equipment_categories')->where('name', $value)->first();
            if($category){
                return $category->id;
            } else {
                Log::error('Error check: Jenis tidak sesuai');
                throw new ErrorMessageException("Jenis {$value} tidak sesuai - a/n {$name}");
            }
        }
    }

    public function checkTripCategory($value, $name)
    {
        if($value == 'All Trip') {
            return null;
        }
        if($value != null || $value != '') {
            $value = rtrim($value, " ");
            $category = DB::table('web_categories')->where('name', $value)->first();
            if($category){
                return $category->id;
            } else {
                Log::error('Error check: Kategori tidak sesuai');
                throw new ErrorMessageException("Kategori {$value} tidak sesuai - a/n {$name}");
            }
        }
    }

    public function checkUnit($value, $name)
    {
        if($value != null || $value != '') {
            $value = rtrim($value, " ");
            $unit = DB::table('equipment_units')->where('name', $value)->first();
            if($unit){
                return $unit->id;
            } else {
                Log::error('Error check: Unit tidak sesuai');
                throw new ErrorMessageException("Unit {$value} tidak sesuai - a/n {$name}");
            }
        }
    }


}
