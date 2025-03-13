<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Package;
use Image;

class StorePackageRequest extends FormRequest
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
            'name' => 'required|max:255',
            'package_stars' => 'required|numeric|min:1|max:5',
            'status' => 'required',
            'document_style' => 'required',
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
        $color = "#d7b143";
        if ($this->document_style == "ruby") {
            $color = '#dc3545';
        }
        if ($this->document_style == "emerald") {
            $color = '#28a745';
        }
        if ($this->document_style == "sapphire") {
            $color = '#007bff';
        }
        if ($this->document_style == "lebih_hemat") {
            $color = '#d7b143';
        }
        $this->merge(['color' => $color, 'show_homepage' => 1]);

        if ($this->hasFile(Package::ICON)) {
            $img =  (string) Image::make($this->file(Package::ICON))->encode('webp');
            $profilePhotoPath =  Package::DIR_ICON . pathinfo($this->file(Package::ICON)->hashName(), PATHINFO_FILENAME) . '.webp';
            $storeImage = Storage::put($profilePhotoPath, $img);
            $this->merge(['image_icon' => $profilePhotoPath]);
        }
    }
}
