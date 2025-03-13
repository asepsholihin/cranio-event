<?php

namespace App\Imports;

use App\Exceptions\ErrorMessageException;
use App\Models\Participant;
use App\Models\ParticipantCRM;
use App\Models\ParticipantCRMTransactionBackdateHistories;
use App\Models\MasterAddress;
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

class ParticipantCRMImport implements ToCollection, SkipsEmptyRows, WithStartRow, WithValidation, SkipsOnFailure
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
        foreach ($rows as $row) {
            $update_column = array();
            if ($row[1])
                $update_column['ji_code'] = $row[1];
            if ($row[2])
                $update_column['name'] = strtoupper($row[2]);
            if ($row[4])
                $update_column['no_hp'] = $this->transformHP(str_replace("'","",$row[4]), $row[2]);
            if ($row[5])
                $update_column['email'] = $row[5];
            if ($row[6])
                $update_column['address'] = $row[6];
            if ($row[7])
                $update_column['city'] = $this->checkCity($row[7], $row[8], $row[2]);
            if ($row[8])
                $update_column['province'] = $this->checkProvince($row[8], $row[2]);
            if ($row[9])
                $update_column['birth_date'] = $this->transformDate($row[9], $row[2], 'Tanggal Lahir');
            if ($row[10])
                $update_column['gender'] = (strtoupper($row[10]) == 'L') ? 1 : 2;
            if ($row[11])
                $update_column['job'] = $row[11];
            if ($row[12])
                $update_column['latest_trip_name'] = $row[12];
            if ($row[13])
                $update_column['latest_trip_package'] = $row[13];
            if ($row[14])
                $update_column['total_transaction'] = intval($row[14]);
            if ($row[15])
                $update_column['latest_trip_year'] = $row[15];
            if ($row[16])
                $update_column['total_trip'] = intval($row[16]);
            if ($row[17])
                $update_column['instagram'] = $row[17];
            if ($row[18])
                $update_column['facebook'] = $row[18];
            if ($row[19])
                $update_column['linkedin'] = $row[19];
            if ($row[20])
                $update_column['twitter'] = $row[20];
            if ($row[21])
                $update_column['interest'] = $row[21];
            if ($row[22])
                $update_column['other_information'] = $row[22];
            if ($row[23])
                $update_column['education'] = $this->transformEducation($row[23], $row[2]);
            if ($row[24])
                $update_column['job_description'] = $row[24];
            if ($row[3])
                $update_column['nik'] = $row[3];

            $update_column['need_merge'] = 0;
            $update_column['parent_account'] = 0;
            if (Str::startsWith($row[2], '*')) {
                $update_column['parent_account'] = 1;
                $update_column['name'] = strtoupper(Str::replaceFirst('*', '', $row[2]));
            }

            DB::transaction(function()use($row, $update_column) {
                $checkIfImportSameFile = ParticipantCRM::where('nik', $row[3])
                ->join('participant_crm_transaction_backdate_histories', 'participant_crm_transaction_backdate_histories.participant_crm_id', 'participant_crm.id')
                ->where('participant_crm_transaction_backdate_histories.umroh_trip_name', $row[12])
                ->first();
                if(!$checkIfImportSameFile) {
                    $checkParticipantCRMExist = ParticipantCRM::where('nik', $row[3])->first();
                    if($checkParticipantCRMExist) {
                        $totalTransaction = is_string($row[14]) ? 0 : $row[14];
                        $totalTrip = is_string($row[16]) ? 0 : $row[16];
                        $dataUpdate = [];
                        if(array_key_exists('name', $update_column)){
                            if(!empty($update_column['name'])){
                                $dataUpdate['name'] = $update_column['name'];
                            }
                        }
                        if(array_key_exists('no_hp', $update_column)){
                            if(!empty($update_column['no_hp'])){
                                $dataUpdate['no_hp'] = $update_column['no_hp'];
                            }
                        }
                        if(array_key_exists('email', $update_column)){
                            if(!empty($update_column['email'])){
                                $dataUpdate['email'] = $update_column['email'];
                            }
                        }
                        if(array_key_exists('address', $update_column)){
                            if(!empty($update_column['address'])){
                                $dataUpdate['address'] = $update_column['address'];
                            }
                        }
                        if(array_key_exists('city', $update_column)){
                            if(!empty($update_column['city'])){
                                $dataUpdate['city'] = $update_column['city'];
                            }
                        }
                        if(array_key_exists('gender', $update_column)){
                            if(!empty($update_column['gender'])){
                                $dataUpdate['gender'] = $update_column['gender'];
                            }
                        }
                        if(array_key_exists('province', $update_column)){
                            if(!empty($update_column['province'])){
                                $dataUpdate['province'] = $update_column['province'];
                            }
                        }
                        if(array_key_exists('birth_date', $update_column)){
                            if(!empty($update_column['birth_date'])){
                                $dataUpdate['birth_date'] = $update_column['birth_date'];
                            }
                        }
                        if(array_key_exists('job', $update_column)){
                            if(!empty($update_column['job'])){
                                $dataUpdate['job'] = $update_column['job'];
                            }
                        }
                        if(array_key_exists('instagram', $update_column)){
                            if(!empty($update_column['instagram'])){
                                $dataUpdate['instagram'] = $update_column['instagram'];
                            }
                        }
                        if(array_key_exists('facebook', $update_column)){
                            if(!empty($update_column['facebook'])){
                                $dataUpdate['facebook'] = $update_column['facebook'];
                            }
                        }
                        if(array_key_exists('linkedin', $update_column)){
                            if(!empty($update_column['linkedin'])){
                                $dataUpdate['linkedin'] = $update_column['linkedin'];
                            }
                        }
                        if(array_key_exists('twitter', $update_column)){
                            if(!empty($update_column['twitter'])){
                                $dataUpdate['twitter'] = $update_column['twitter'];
                            }
                        }
                        if(array_key_exists('interest', $update_column)){
                            if(!empty($update_column['interest'])){
                                $dataUpdate['interest'] = $update_column['interest'];
                            }
                        }
                        if(array_key_exists('other_information', $update_column)){
                            if(!empty($update_column['other_information'])){
                                $dataUpdate['other_information'] = $update_column['other_information'];
                            }
                        }
                        if(array_key_exists('education', $update_column)){
                            if(!empty($update_column['education'])){
                                $dataUpdate['education'] = $update_column['education'];
                            }
                        }
                        if(array_key_exists('job_description', $update_column)){
                            if(!empty($update_column['job_description'])){
                                $dataUpdate['job_description'] = $update_column['job_description'];
                            }
                        }

                        $dataUpdate['total_transaction'] = $checkParticipantCRMExist->total_transaction + (int) $totalTransaction;
                        $dataUpdate['total_trip'] = $checkParticipantCRMExist->total_trip + (int) $totalTrip;
                        $checkParticipantCRMExist->update($dataUpdate);
                    } else {
                        $update_column['created_from'] = 3;
                        $participantCreated = Participant::where('nik', $row[3])->first();
                        if(!$participantCreated) {
                            $participantCreated = Participant::create($update_column);
                        }
                        $update_column['participant_id'] = $participantCreated->id;
                        $checkParticipantCRMExist = ParticipantCRM::create($update_column);
                    }

                    $insrtDataTransaction = [
                        'participant_crm_id' => $checkParticipantCRMExist->id,
                        'package_name' => $update_column['latest_trip_package'],
                        'umroh_trip_name' => $update_column['latest_trip_name'],
                        'total_transaction' => $update_column['total_transaction']??0,
                    ];
                    if(array_key_exists('latest_trip_year', $update_column)){
                        if(!empty($update_column['latest_trip_year'])){
                            $insrtDataTransaction['year'] = $update_column['latest_trip_year'];
                        }
                    }
                    ParticipantCRMTransactionBackdateHistories::create($insrtDataTransaction);
                }
            });
        }
    }

    public function rules(): array
    {
        return [
            '2' => 'required|string',
            '3' => 'required',
            '9' => 'required',
            '10' => 'required|in:L,P',
            '12' => 'required',
            '13' => 'required',
            '15' => 'required',
        ];
    }

    private function transformHP($phoneNumber, $name)
    {
        if (Str::startsWith($phoneNumber, '0')) {
            $phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $phoneNumber);
        }

        if (Str::startsWith($phoneNumber, '8')) {
            $phoneNumber = Participant::PREFIX_PHONE_NUMBER . $phoneNumber;
        }

        if(!is_numeric($phoneNumber)) {
            Log::error('Error transformHP: tidak sesuai');
            throw new ErrorMessageException("No. HP harus angka - a/n: {$name}");
        }

        return $phoneNumber;
    }

    private function transformEducation($value, $name)
    {
        $educationAllows = array('SD/MI','SMP/MTS','SMA/MA','D1','D2','D3','D4/S1','S2','S3','BELUM SEKOLAH');

        $availableEducation = "";
        foreach ($educationAllows as $education) {
            $availableEducation .= $education . ", ";
        }
        $availableEducation = rtrim($availableEducation, ', ');

        if(Str::contains(strtolower($value), 'belum terdeteksi')) {
            return $value;
        } else {
            if(in_array($value, $educationAllows)) {
                return $value;
            } else {
                Log::error('Error transformEducation: tidak sesuai');
                throw new ErrorMessageException("Pendidikan harus berisi ({$availableEducation}) - a/n: {$name}");
            }
        }
    }

    public function checkProvince($value, $name)
    {
        if(Str::contains(strtolower($value), 'belum terdeteksi')) {
            return $value;
        } else {
            $provinces = MasterAddress::select('province')->groupBy('province')->orderBy('province', 'ASC')->pluck('province')->toArray();

            $value = strtoupper($value);
            $value = rtrim($value, " ");
            if(in_array($value, $provinces)){
                return $value;
            } else {
                Log::error('Error check package: Provinsi tidak sesuai');
                throw new ErrorMessageException("Provinsi {$value} tidak sesuai - a/n {$name}");
            }
        }
    }

    public function checkCity($value, $province, $name)
    {
        if(Str::contains(strtolower($value), 'belum terdeteksi')) {
            return $value;
        } else {
            $province = strtoupper($province);
            $cities = MasterAddress::select('city')->where('province', $province)->groupBy('city')->orderBy('city', 'ASC')->pluck('city')->toArray();

            $value = strtoupper($value);
            $value = str_replace("KABUPATEN","KAB.",$value);
            $value = rtrim($value, " ");

            if(in_array($value, $cities)){
                return $value;
            } else {
                Log::error('Error check package: Kabupaten tidak sesuai');
                throw new ErrorMessageException("Kabupaten {$value} tidak sesuai - a/n {$name}");
            }
        }
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '2.required' => 'Nama Lengkap harus diisi',
            '3.required' => 'NIK harus diisi',
            '9.required' => 'Tanggal Lahir harus diisi',
            '10.required' => 'Jenis Kelamin harus diisi',
            '10.in' => 'Jenis Kelamin harus (L/P)',
            '12.required' => 'Keberangkatan harus diisi',
            '13.required' => 'Jenis Paket harus diisi',
            '15.required' => 'Tahun Keberangkatan harus diisi',
        ];
    }
}
