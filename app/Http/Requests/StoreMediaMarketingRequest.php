<?php

namespace App\Http\Requests;

use App\Models\MediaMarketing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Image;


class StoreMediaMarketingRequest extends FormRequest
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
            'title' => 'required',
            'category' => 'required',
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
        $status = 0;
        if ($this->status == "true") {
            $status = 1;
        }
        $this->merge(['status' => $status]);
        
        if(is_array($this->file_image)) {
            $images = array();
            foreach($this->file_image as $file) {
                if ($this->hasFile(MediaMarketing::IMAGE)) {
                    $imageMake = Image::make($file);
                    $img = (string) $imageMake
                        ->resize(2500, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })
                        ->encode('jpeg');

                    $profilePhotoPath =  MediaMarketing::DIR_IMAGE . pathinfo($file->hashName(), PATHINFO_FILENAME) . '.webp';
                    Storage::put($profilePhotoPath, $img);
                    $images[] = $profilePhotoPath;
                }
            }
            $this->merge(['images' => $images]);
        } else {
            if ($this->hasFile(MediaMarketing::IMAGE)) {
                $profilePhotoPath = $this->file(MediaMarketing::IMAGE)->store(MediaMarketing::DIR_IMAGE);
                $img = Image::make($this->file(MediaMarketing::IMAGE))
                    ->resize(2500, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->encode('jpeg');
                Storage::put(MediaMarketing::DIR_THUMBNAIL . $profilePhotoPath, $img);
                $this->merge(['image' => $profilePhotoPath]);
            }
        }
    }
}
