<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\OtherPackage;
use Illuminate\Support\Facades\Storage;
use Image;

class StoreOtherPackageRequest extends FormRequest
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
            'package_name' => 'required',
            'url' => 'required',
            'publish_status' => 'required',
            'package_image' => 'file|mimes:jpg,png,webp|max:1536'
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        $publish_status = $this->publish_status;

        $publish_status_value = 0;
        if ($publish_status == "true") {
            $publish_status_value = 1;
        }
        $this->merge(['publish_status' => $publish_status_value, 'category' => 'all']);

        if ($this->hasFile(OtherPackage::IMAGE)) {
            $profilePhotoPath = $this->file(OtherPackage::IMAGE)->store(OtherPackage::DIR_IMAGE);
            $img = Image::make($this->file(OtherPackage::IMAGE))
                ->resize(150, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 50);
            Storage::put(OtherPackage::DIR_THUMBNAIL . $profilePhotoPath, $img);

            $this->merge(['image' => $profilePhotoPath]);
        }
    }
}
