<?php

namespace App\Imports;

use App\Exceptions\ErrorMessageException;
use App\Models\Participant;
use App\Models\EventAttendance;
use App\Models\Attendance;
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

class ParticipantEventImport implements ToCollection, SkipsEmptyRows, WithStartRow, WithValidation, SkipsOnFailure
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
                $update_column = array();
                if($row[5]) {
                    $update_column['package_umroh_trip_id'] = $row[7];
                    $update_column['group_bus'] = $row[3];
                    $update_column['departure_from'] = $row[5];
                }
                    
                $attendance = Attendance::find($row[6]);
                $attendance->update($update_column);
            }
        });
    }

    public function rules(): array
    {
        return [
            '5' => 'nullable|in:Rumah,Hotel Manasik',
            '6' => 'required',
            '7' => 'required'
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '5' => 'Pilihan harus (Rumah / Hotel Manasik)',
            '6.required' => 'EVENT_ID harus di-isi',
            '7.required' => 'ATTENDANCE_ID harus di-isi',
        ];
    }
}
