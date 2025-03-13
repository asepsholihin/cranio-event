<?php

namespace App\Http\Requests\ImageSlider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\WebFooterLogo;
use Image;

class StoreWebFooterLogoRequest extends FormRequest
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
            'url' => 'required',
            'order' => 'required|numeric|min:1|max:100',
            'status' => 'required',
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

        if ($this->hasFile(WebFooterLogo::IMAGE)) {
            $img =  (string) Image::make($this->file(WebFooterLogo::IMAGE))->encode('webp');
            $profilePhotoPath =  WebFooterLogo::DIR_IMAGE . pathinfo($this->file(WebFooterLogo::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            $storeImage = Storage::put($profilePhotoPath, $img);
            $this->merge(['image_url' => $profilePhotoPath]);
        }
    }
}
