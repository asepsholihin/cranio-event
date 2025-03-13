<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Facility;
use Image;

class StoreFacilityRequest extends FormRequest
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
            'package_type' => 'required|max:255',
            'title' => 'required',
            'status' => 'required',
            'icon' => 'file|mimes:jpg,png,webp|max:1536'
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        $status = $this->status;

        $status_value = 0;
        if ($status == "true") {
            $status_value = 1;
        }
        $this->merge(['status' => $status_value]);

        if ($this->hasFile(Facility::ICON)) {
            $profilePhotoPath = $this->file(Facility::ICON)->store(Facility::DIR_ICON);
            $img = Image::make($this->file(Facility::ICON))
                    ->resize(200, null, function ($constraint) {$constraint->aspectRatio();})
                    ->encode('webp');
            Storage::put(Facility::DIR_THUMBNAIL . $profilePhotoPath, $img);
            $this->merge(['image_icon' => $profilePhotoPath]);
        }
    }
}
