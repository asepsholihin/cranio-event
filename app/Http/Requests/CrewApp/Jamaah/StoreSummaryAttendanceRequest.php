<?php

namespace App\Http\Requests\CrewApp\Participant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\SummaryAttendance;
use Image;

class StoreSummaryAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'umroh_trip_id' => 'required',
            'category_id' => 'required',
            'attendance_name' => 'required',
            'total_attendance' => 'required',
            'logs' => 'required',
            //'photo' => 'required|file|mimes:jpg,png,webp|max:1536'
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        if ($this->hasFile(SummaryAttendance::PHOTO)) {
            $filePath = $this->file(SummaryAttendance::PHOTO)->store(SummaryAttendance::DIR_PHOTO);
            $img = Image::make($this->file(SummaryAttendance::PHOTO))
                    ->resize(200, null, function ($constraint) {$constraint->aspectRatio();})
                    ->encode();
            Storage::put(SummaryAttendance::DIR_THUMBNAIL . $filePath, $img);
            $this->merge(['evidence' => $filePath]);
        }
    }
}
