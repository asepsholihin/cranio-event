<?php

namespace App\Http\Requests\ImageSlider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\WebItinerary;
use Image;

class StoreWebItineraryRequest extends FormRequest
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
            'package_duration' => 'required|numeric|min:1|max:100'
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        if ($this->hasFile(WebItinerary::IMAGE)) {
            try {
                $filePath = $this->file(WebItinerary::IMAGE)->store(WebItinerary::DIR_IMAGE);
                $this->merge(['image_url' => $filePath]);
            } catch (\Throwable $th) {
            }
        }
        // if ($this->hasFile(WebFooterLogo::IMAGE)) {
        //     $img =  (string) Image::make($this->file(WebFooterLogo::IMAGE))->encode('webp');
        //     $profilePhotoPath =  WebFooterLogo::DIR_IMAGE . pathinfo($this->file(WebFooterLogo::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
        //     $storeImage = Storage::put($profilePhotoPath, $img);
        //     $this->merge(['image_url' => $profilePhotoPath]);
        // }
    }
}
