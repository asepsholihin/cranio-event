<?php

namespace App\Http\Requests\ImageSlider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\WebSlider;
use App\Support\StorageAttributes;
use Image;

class StoreWebSliderRequest extends FormRequest
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
            'title' => 'required',
            'description' => 'required',
            'slider_url' => 'required',
            'status' => 'required',
            'image' => 'file|mimes:jpg,png,webp|max:1536'
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
        $overlay = $this->overlay;

        $status_value = 0;
        if ($status == "true" || $status == 1) {
            $status_value = 1;
        }
        $this->merge(['status' => $status_value]);

        $overlay_value = 0;
        if ($overlay == "true" || $overlay == 1) {
            $overlay_value = 1;
        }
        $this->merge(['overlay' => $overlay_value]);

        if ($this->hasFile(WebSlider::IMAGE)) {
            $imageMake = Image::make($this->file(WebSlider::IMAGE));

            $img =  (string) $imageMake->encode('webp');
            $imgSmall =  (string) $imageMake
                ->resize(500, null, function ($constraint) {$constraint->aspectRatio();})
                ->encode('webp',90);

            $profilePhotoPath =  WebSlider::DIR_IMAGE . pathinfo($this->file(WebSlider::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            $profilePhotoSmallPath =  WebSlider::DIR_IMAGE . StorageAttributes::SMALL_IMG_PREFIX . pathinfo($this->file(WebSlider::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            
            Storage::put($profilePhotoPath, $img);
            Storage::put($profilePhotoSmallPath, $imgSmall);
            $this->merge(['image_url' => $profilePhotoPath]);
        }
    }
}
