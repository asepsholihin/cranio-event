<?php

namespace App\Http\Requests\ImageSlider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Support\StorageAttributes;
use Illuminate\Validation\Rule;
use App\Models\WebPartner;
use Image;

class StoreWebPartnerRequest extends FormRequest
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

        if ($this->hasFile(WebPartner::IMAGE)) {
            $imageMake = Image::make($this->file(WebPartner::IMAGE));

            $img =  (string) Image::make($this->file(WebPartner::IMAGE))->encode('webp');
            $imgSmall =  (string) $imageMake
            ->resize(500, null, function ($constraint) {$constraint->aspectRatio();})
            ->encode('webp',90);
            $profilePhotoPath =  WebPartner::DIR_IMAGE . pathinfo($this->file(WebPartner::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            $profilePhotoSmallPath =  WebPartner::DIR_IMAGE . StorageAttributes::SMALL_IMG_PREFIX . pathinfo($this->file(WebPartner::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            Storage::put($profilePhotoPath, $img);
            Storage::put($profilePhotoSmallPath, $imgSmall);
            $this->merge(['image_url' => $profilePhotoPath]);
        }
    }
}
