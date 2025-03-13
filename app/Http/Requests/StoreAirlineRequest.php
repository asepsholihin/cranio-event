<?php

namespace App\Http\Requests;

use App\Models\Airline;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Image;

class StoreAirlineRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'status' => 'required',
            'icon' => 'file|mimes:jpg,png,webp|max:1536'
        ];
    }

    public function  passedValidation()
    {
        $status = $this->status;
        $this->merge(['status' => 0]);
        if ($status == "true") {
            $this->merge(['status' => 1]);
        }
        if ($this->hasFile(Airline::ICON)) {
            $img =  (string) Image::make($this->file(Airline::ICON))->encode('webp');
            $profilePhotoPath =  Airline::DIR_ICON . pathinfo($this->file(Airline::ICON)->hashName(), PATHINFO_FILENAME) . '.webp';
            $storeImage = Storage::put($profilePhotoPath, $img);
            $this->merge(['logo' => $profilePhotoPath]);
        }
    }
}
