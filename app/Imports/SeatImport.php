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

class SeatImport implements ToCollection, SkipsEmptyRows, WithStartRow, WithValidation
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
                $participant = Participant::where('no_passport', $row[2])->first();
                if($participant) {

                    $update_column = array();
                    if($row[3])
                        $update_column['departure_seat'] = $row[3];
                    if($row[4])
                        $update_column['return_seat'] = $row[4];

                    ParticipantUmrohTrip::where('participant_id', $participant->id)->update($update_column);
                }
            }
        });
    }

    public function rules(): array
    {
        return [
            '2' => 'required',
            // '3' => 'required',
            // '4' => 'required'
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '2.required' => 'Nomor Passport harus di-isi',
            '3.required' => 'Nomor Seat harus di-isi',
            '4.required' => 'Nomor Seat harus di-isi',
        ];
    }
}
