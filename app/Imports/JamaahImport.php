<?php

namespace App\Imports;

use App\Exceptions\ErrorMessageException;
use App\Models\Participant;
use App\Models\ParticipantUmrohTrip;
use App\Models\Package;
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

class ParticipantImport implements ToCollection, SkipsEmptyRows, WithStartRow, WithValidation, SkipsOnFailure, WithMultipleSheets
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
    
    public function transformDate($value, $name, $columnName)
    {
        try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\Throwable $e) {
            Log::error('Error transformDate: ' . $e->getMessage());
            throw new ErrorMessageException("Invalid Date Format - {$columnName}: {$name}");
        }
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
                if($row[0])
                    $update_column['no_urut'] = $row[0];
                if($row[1])
                    $update_column['title'] = $row[1];
                if($row[2])
                    $update_column['front_title'] = $row[2];
                if($row[3])
                    $update_column['name'] = strtoupper($row[3]);
                if($row[4])
                    $update_column['back_title'] = $row[4];
                if($row[5])
                    $update_column['fathers_name'] = $row[5];
                if($row[6])
                    $update_column['gender'] = (Str::lower($row[6]) == 'pria') ? 1 : 2;
                if($row[7])
                    $update_column['birth_place'] = $row[7];
                if($row[3] && $row[8])
                    $update_column['birth_date'] = $this->transformDate($row[8], $row[3], 'Tanggal Lahir');
                if($row[10])
                    $update_column['nik'] = str_replace("'","",$row[10]);
                if($row[11])
                    $update_column['married_status'] = $this->merriedStatus($row[11]);
                if($row[12])
                    $update_column['nationality'] = $row[12];
                if($row[13])
                    $update_column['no_hp'] = $this->transformHP(str_replace("'","",$row[13]));
                if($row[14])
                    $update_column['email'] = $row[14];
                if($row[15])
                    $update_column['instagram'] = $row[15];
                if($row[16])
                    $update_column['ktp_province'] = $this->checkProvince($row[16], $row[3]);
                if($row[17])
                    $update_column['ktp_city'] = $this->checkCity($row[17], $row[16], $row[3]);
                if($row[18])
                    $update_column['ktp_kecamatan'] = strtoupper($row[18]);
                if($row[19])
                    $update_column['ktp_kelurahan'] = strtoupper($row[19]);
                if($row[20])
                    $update_column['ktp_address'] = $row[20];
                if($row[21])
                    $update_column['education'] = $row[21];
                if($row[22])
                    $update_column['is_doctor'] = (Str::lower($row[22]) == 'ya') ? 1 : 2;
                if($row[23])
                    $update_column['doctor_specialist'] = $row[23];
                if($row[24])
                    $update_column['job'] = $row[24];
                if($row[25])
                    $update_column['company_name'] = $row[25];
                if($row[26] && $row[27])
                    $update_column['blood_type'] = $row[26] . $row[27];
                if($row[28])
                    $update_column['medical_record'] = $row[28];
                if($row[29])
                    $update_column['medical_description'] = $row[29];
                if($row[30])
                    $update_column['emergency_contact'] = $this->transformHP(str_replace("'","",$row[30]));
                if($row[31])
                    $update_column['emergency_contact_name'] = $row[31];
                if($row[32])
                    $update_column['emergency_relation'] = $row[32];
                if($row[33])
                    $update_column['emergency_address'] = $row[33];
                if($row[34])
                    $update_column['have_passport'] = (Str::lower($row[34]) == 'ya') ? 1 : 2;
                if($row[35])
                    $update_column['name_in_passport'] = $row[35];
                if($row[36])
                    $update_column['no_passport'] = $row[36];
                if($row[37])
                    $update_column['passport_published_date'] = $this->transformDate($row[37], $row[3], 'Tanggal Terbit Passport');
                if($row[38])
                    $update_column['passport_expired_date'] = $this->transformDate($row[38], $row[3], 'Tanggal Kadaluarsa Passport');
                if($row[39])
                    $update_column['passport_held_by'] = $row[39];
                if($row[40])
                    $update_column['suggest_booking_order'] = $row[40];
                if($row[41])
                    $update_column['suggest_package'] = $this->checkPackage($row[41], $row[3], 'Package');
                if($row[42])
                    $update_column['suggest_room'] = strtolower($row[42]);
                if($row[43])
                    $update_column['body_size'] = $row[43];
                if($row[44])
                    $update_column['infants'] = (Str::lower($row[44]) == 'y') ? 1 : 2;
                if($row[45])
                    $update_column['is_nakes'] = (Str::lower($row[45]) == 'ya') ? 1 : 2;
                if($row[46])
                    $update_column['is_tni_polri'] = (Str::lower($row[46]) == 'ya') ? 1 : 2;
                    
                $currentParticipant = Participant::select('name','nik','ji_code')->where('id',$row[47])->first();
                if(($currentParticipant->ji_code != $row[49])){
                    Log::error('Error check participant tertukar: ' . $currentParticipant->name ." (JI CODE:".$currentParticipant->ji_code.") dengan ". $update_column['name'] ." (JI CODE:".$row[49].")");
                    throw new ErrorMessageException('Error check participant tertukar: ' . $currentParticipant->name ." (JI CODE:".$currentParticipant->ji_code.") dengan ". $update_column['name'] ." (JI CODE:".$row[49].")");
                }

                $update_column['created_from'] = Participant::CREATED_FROM_SPA;
                Participant::updateOrCreate(
                    [
                        'id' => $row[47]
                    ],
                    $update_column
                );

                ParticipantUmrohTrip::find($row[48])->update([
                    'no_urut' => $row[0]
                ]);
            }
        });
    }

    public function rules(): array
    {
        return [
            '1' => 'required|in:Mr,Ms,Mrs,Mstr,Miss',
            '3' => 'required|string',
            '6' => 'required|in:Pria,Wanita',
            '9' => 'required',
            '11' => 'in:Menikah,Belum Menikah,Cerai',
            '13' => 'required',
            '22' => 'in:Ya,Tidak',
            '28' => 'nullable|in:Ada,Tidak Ada',
            '34' => 'in:Ya,Belum',
            '42' => 'nullable|in:Double,Triple,Quad,Queen,Single',
            '43' => 'required',
            '45' => 'in:Ya,Tidak',
            '46' => 'in:Ya,Tidak',
            '47' => 'required',
            '48' => 'required',
        ];
    }
    
    public function customValidationMessages()
    {
        return [
            '1.required' => 'Title harus di-isi',
            '1.in' => 'Title harus (Mr/Ms/Mrs/Mstr/Miss)',
            '3.required' => 'Nama Lengkap harus di-isi',
            '3.string' => 'Nama Lengkap harus dengan huruf',
            '6.required' => 'Jenis Kelamin harus di-isi',
            '6.in' => 'Jenis Kelamin harus Pria/Wanita',
            '9.required' => 'NIK / Nomor KTP harus di-isi',
            '11.in' => 'Status Menikah harus (Menikah/Belum Menikah/Cerai)',
            '13.required' => 'No HP / Whatsapp harus di-isi',
            '22.in' => 'Apakah Dokter? harus (Ya/Tidak)',
            '28.in' => 'Riwayat Penyakit? harus (Ada/Tidak Ada)',
            '34.in' => 'Apakah Punya Passport? harus (Ya/Belum)',
            '42.in' => 'Room Type harus (Double/Triple/Quad/Queen/Single)',
            '43.required' => 'Ukuran Badan harus di-isi',
            '45.in' => 'Apakah Tenaga Kesehatan? harus (Ya/Tidak)',
            '46.in' => 'Apakah TNI/POLRI? harus (Ya/Tidak)',
        ];
    }

    private function transformHP($phoneNumber) 
    {
        if (Str::startsWith($phoneNumber, '0')) {
            $phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $phoneNumber);
        }

        if (Str::startsWith($phoneNumber, '8')) {
            $phoneNumber = Participant::PREFIX_PHONE_NUMBER . $phoneNumber;
        }

        return $phoneNumber;
    }

    private function merriedStatus($status) {
        if ($status == 'Menikah') {
            return 1;
        }

        if ($status == 'Belum Menikah') {
            return 2;
        }
        
        return 3;
    }

    public function checkPackage($value, $name, $columnName)
    {
        $packages = Package::select('name')->pluck('name')->toArray();

        $availablePackage = "";
        foreach ($packages as $package) {
            $availablePackage .= $package . ", ";
        }
        $availablePackage = rtrim($availablePackage, ', ');

        if(in_array($value, $packages)){
            return $value;
        } else {
            Log::error('Error check package: Paket tidak sesuai');
            throw new ErrorMessageException("Paket tidak sesuai ({$availablePackage}) - {$columnName}: {$name}");
        }
    }

    public function checkProvince($value, $name)
    {
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

    public function checkCity($value, $province, $name)
    {
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
