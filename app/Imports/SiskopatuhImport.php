<?php

namespace App\Imports;

use App\Exceptions\ErrorMessageException;
use App\Models\Participant;
use App\Models\ParticipantUmrohTrip;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class SiskopatuhImport implements ToCollection, SkipsEmptyRows, WithStartRow, WithValidation
{
    use Importable, SkipsFailures;

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
                $participant = Participant::where('nik', str_replace("'", "", $row[2]))->first();
                if($participant) {
                    $siskopatuh_id = ParticipantUmrohTrip::SISKOPATUH_URI . $row[4];

                    $update_column = array();
                    if($row[4])
                        $update_column['code_siskopatuh'] = $row[4];
                    if($row[5])
                        $update_column['ticket_number'] = $row[5];
                    if($row[6])
                        $update_column['insurance_number'] = $row[6];
                    if($row[7])
                        $update_column['visa_number'] = $row[7];
                    if($row[8])
                        $update_column['nomor_porsi'] = $row[8];
                    if($row[9])
                        $update_column['nomor_spph'] = $row[9];

                    $update_column['siskopatuh_id'] = $siskopatuh_id;

                    ParticipantUmrohTrip::where('participant_id', $participant->id)->update($update_column);
                }
            }
        });
    }

    public function rules(): array
    {
        return [
            '2' => 'required',
            // '4' => 'required',
            // '7' => 'required'
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '2.required' => 'NIK harus di-isi',
            '4.required' => 'Kode Siskopatuh harus di-isi',
            '7.required' => 'Nomor Visa harus di-isi',
        ];
    }
}
