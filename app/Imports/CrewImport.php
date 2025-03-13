<?php

namespace App\Imports;

use App\Exceptions\ErrorMessageException;
use App\Models\Participant;
use App\Models\Crew;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class CrewImport implements ToCollection, SkipsEmptyRows, WithStartRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

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
                if($row[9])
                    $update_column['nik'] = str_replace("'","",$row[9]);
                if($row[10])
                    $update_column['married_status'] = $this->merriedStatus($row[10]);
                if($row[11])
                    $update_column['nationality'] = $row[11];
                if($row[12])
                    $update_column['no_hp'] = $this->transformHP(str_replace("'","",$row[12]));
                if($row[13])
                    $update_column['email'] = $row[13];
                if($row[14])
                    $update_column['instagram'] = $row[14];
                if($row[15])
                    $update_column['ktp_province'] = $row[15];
                if($row[16])
                    $update_column['ktp_city'] = $row[16];
                if($row[17])
                    $update_column['ktp_kecamatan'] = $row[17];
                if($row[18])
                    $update_column['ktp_kelurahan'] = $row[18];
                if($row[19])
                    $update_column['ktp_address'] = $row[19];
                if($row[20])
                    $update_column['home_province'] = $row[20];
                if($row[21])
                    $update_column['home_city'] = $row[21];
                if($row[22])
                    $update_column['home_kecamatan'] = $row[22];
                if($row[23])
                    $update_column['home_kelurahan'] = $row[23];
                if($row[24])
                    $update_column['home_address'] = $row[24];
                if($row[25])
                    $update_column['education'] = $row[25];
                if($row[26])
                    $update_column['is_doctor'] = (Str::lower($row[26]) == 'ya') ? 1 : 2;
                if($row[27])
                    $update_column['doctor_specialist'] = $row[27];
                if($row[28])
                    $update_column['job'] = $row[28];
                if($row[29])
                    $update_column['company_name'] = $row[29];
                if($row[30] && $row[31])
                    $update_column['blood_type'] = $row[30] . $row[31];
                if($row[32])
                    $update_column['medical_record'] = $row[32];
                if($row[33])
                    $update_column['emergency_contact'] = $this->transformHP(str_replace("'","",$row[33]));
                if($row[34])
                    $update_column['emergency_contact_name'] = $row[34];
                if($row[35])
                    $update_column['emergency_relation'] = $row[35];
                if($row[36])
                    $update_column['emergency_address'] = $row[36];
                if($row[37])
                    $update_column['have_passport'] = (Str::lower($row[37]) == 'ya') ? 1 : 2;
                if($row[38])
                    $update_column['name_in_passport'] = $row[38];
                if($row[39])
                    $update_column['no_passport'] = $row[39];
                if($row[40])
                    $update_column['passport_published_date'] = $this->transformDate($row[40], $row[3], 'Tanggal Terbit Passport');
                if($row[41])
                    $update_column['passport_expired_date'] = $this->transformDate($row[41], $row[3], 'Tanggal Kadaluarsa Passport');
                if($row[42])
                    $update_column['passport_held_by'] = $row[42];
                if($row[43])
                    $update_column['suggest_booking_order'] = $row[43];
                if($row[44])
                    $update_column['suggest_package'] = ucfirst($row[44]);
                if($row[45])
                    $update_column['suggest_room'] = strtolower($row[45]);
                if($row[46])
                    $update_column['body_size'] = $row[46];
                if($row[47])
                    $update_column['infants'] = (Str::lower($row[47]) == 'y') ? 1 : 2;
                    
                $update_column['created_from'] = Participant::CREATED_FROM_SPA;
                $participant = Participant::updateOrCreate(
                    [
                        'nik' => $row[9]
                    ],
                    $update_column
                );

                $crew_column = array(
                    'participant_id' => $participant->id,
                    'name' => $participant->name,
                    'email' => $participant->email,
                    'password' => 'admin1234',
                    'no_hp' => $participant->no_hp,
                    'gender' => $participant->gender,
                    'profile_photo_path' => $participant->profile_photo_path
                );

                if($row[49])
                    $crew_column['crew_role_id'] = $this->crewRole($row[49]);
                if($row[50])
                    $crew_column['location'] = $row[50];

                Crew::updateOrCreate(
                    [
                        'participant_id' => $participant->id
                    ],
                    $crew_column
                );
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
            '10' => 'in:Menikah,Belum Menikah,Cerai',
            '12' => 'required',
            '26' => 'in:Ya,Tidak',
            '32' => 'nullable|in:Ada,Tidak Ada',
            '37' => 'in:Ya,Belum',
            '44' => 'nullable|in:Ruby,Emerald,Sapphire,VIP,Plus Bintang 4,Plus Bintang 5,Lebih Hemat',
            '45' => 'nullable|in:Double,Triple,Quad,Queen,Single',
            '46' => 'required',
            '49' => 'required|in:Tour Leader,Mutawwif,Handling,Crew'
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

    private function crewRole($role) {
        if ($role == 'Tour Leader') {
            return 1;
        }

        if ($role == 'Mutawwif') {
            return 2;
        }

        if ($role == 'Handling') {
            return 3;
        }

        if ($role == 'Crew') {
            return 4;
        }
        
        return 4;
    }

    /**
     * @return array
     */
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
            '10.in' => 'Status Menikah harus (Menikah/Belum Menikah/Cerai)',
            '12.required' => 'No HP / Whatsapp harus di-isi',
            '26.in' => 'Apakah Dokter? harus (Ya/Tidak)',
            '32.in' => 'Riwayat Penyakit? harus (Ada/Tidak Ada)',
            '37.in' => 'Apakah Punya Passport? harus (Ya/Belum)',
            '44.in' => 'Package harus (Ruby/Emerald/Sapphire/VIP/Plus)',
            '45.in' => 'Room Type harus (Double/Triple/Quad/Queen/Single)',
            '46.required' => 'Ukuran Badan harus di-isi',
            '49' => 'Role harus (Tour Leader/Mutawwif/Handling/Crew)'
        ];
    }
}
