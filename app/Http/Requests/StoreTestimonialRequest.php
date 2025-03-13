<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Storage;
use Image;

class StoreTestimonialRequest extends FormRequest
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
            'customer_name' => 'required',
            'customer_job' => 'required',
            'testimony' => 'required',
            'publish_status' => 'required',
            'image' => 'file|mimes:jpg,png,webp|max:1536'
        ];
    }

    public function messages()
    {
        return [
            'image' => 'Gambar terlalu besar, maximal ukuran 1.5MB'
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
        $this->merge(['publish_status' => $publish_status_value]);

        if ($this->hasFile(Testimonial::IMAGE)) {
            $imageMake = Image::make($this->file(Testimonial::IMAGE));
            $img = (string) $imageMake
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->encode('webp',100);

            $profilePhotoPath =  Testimonial::DIR_IMAGE . pathinfo($this->file(Testimonial::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';

            Storage::put($profilePhotoPath, $img);
            $this->merge(['customer_photo' => $profilePhotoPath]);
        }
    }
}
