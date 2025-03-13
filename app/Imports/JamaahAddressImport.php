<?php

namespace App\Imports;

use App\Exceptions\ErrorMessageException;
use App\Models\Participant;
use App\Models\ParticipantUmrohTrip;
use App\Models\MasterAddress;
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

class ParticipantAddressImport implements ToCollection, SkipsEmptyRows, WithStartRow, WithValidation, SkipsOnFailure, WithMultipleSheets
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
                if($row[3])
                    $update_column['gender'] = (strtoupper($row[3]) == 'L') ? 1 : 2;
                if($row[6])
                    $update_column['home_province'] = $this->checkHomeProvince($row[6], $row[1]);
                if($row[7])
                    $update_column['home_city'] = $this->checkHomeCity($row[7], $row[6], $row[1]);
                if($row[8])
                    $update_column['home_kecamatan'] = strtoupper($row[8]);
                if($row[9])
                    $update_column['home_kelurahan'] = strtoupper($row[9]);
                if($row[10])
                    $update_column['home_postalcode'] = $row[10];
                if($row[11])
                    $update_column['home_address'] = $row[11];

                Participant::updateOrCreate(
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

    public function checkHomeProvince($value, $name)
    {
        if($value != null || $value != '') {
            $provinces = MasterAddress::select('province')->groupBy('province')->orderBy('province', 'ASC')->pluck('province')->toArray();
        
            $value = strtoupper($value);
            $value = rtrim($value, " ");
            if(in_array($value, $provinces)){
                return strtoupper($value);
            } else {
                Log::error('Error check package: Provinsi tidak sesuai');
                throw new ErrorMessageException("Provinsi {$value} tidak sesuai - a/n {$name}");
            }
        }
    }

    public function checkHomeCity($value, $province, $name)
    {
        if(($province != '' || $province != null) && ($value != null || $value != '')) {
            $province = strtoupper($province);
        $cities = MasterAddress::select('city')->where('province', $province)->groupBy('city')->orderBy('city', 'ASC')->pluck('city')->toArray();

        $value = strtoupper($value);
        $value = str_replace("KABUPATEN","KAB.",$value);
        $value = rtrim($value, " ");

        if(in_array($value, $cities)){
            return strtoupper($value);
        } else {
            Log::error('Error check package: Kabupaten tidak sesuai');
            throw new ErrorMessageException("Kabupaten {$value} tidak sesuai ".json_encode($cities)." - a/n {$name}");
        }
        }
    }
}
