<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Equipment;
use Image;

class StoreEquipmentRequest extends FormRequest
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
            'name' => 'required',
            'gender' => 'required',
            'status' => 'required',
            'image' => 'file|mimes:jpg,png,webp|max:1536',
            'sku_id' => 'required',
            'category_id' => 'required',
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
        $new_logo = $this->new_logo;

        $status_value = 0;
        if ($status == "true") {
            $status_value = 1;
        }

        $package_type = null;
        if($this->package_type) {
            $package_type = $this->package_type;
        }

        $trip_category_id = null;
        if($this->trip_category_id) {
            $trip_category_id = $this->trip_category_id;
        }

        $size = null;
        if($this->size) {
            $size = $this->size;
        }

        // $new_logo_value = 0;
        // if ($new_logo == "true") {
        //     $new_logo_value = 1;
        // }
        $this->merge(['status' => $status_value, 'new_logo' => $new_logo, 'package_type' => $package_type, 'size' => $size, 'trip_category_id' => $trip_category_id]);
        if ($this->hasFile(Equipment::IMAGE)) {
            $profilePhotoPath = $this->file(Equipment::IMAGE)->store(Equipment::DIR_IMAGE);
            $img = Image::make($this->file(Equipment::IMAGE))
                    ->resize(200, null, function ($constraint) {$constraint->aspectRatio();})
                    ->encode('webp');
            Storage::put(Equipment::DIR_THUMBNAIL . $profilePhotoPath, $img);
            $this->merge(['image_path' => $profilePhotoPath]);
        }
    }
}
