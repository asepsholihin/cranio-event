<?php

namespace App\Http\Requests;

use App\Models\SocialMedia;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Image;


class StoreSocialMediaRequest extends FormRequest
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
            'social_media_name' => 'required',
            'social_media_link' => 'required',
            'status' => 'required',
            'file_icon' => 'file|mimes:jpg,png,webp|max:1536'
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
        if ($this->hasFile(SocialMedia::ICON)) {
            $profilePhotoPath = $this->file(SocialMedia::ICON)->store(SocialMedia::DIR_ICON);
            $img = Image::make($this->file(SocialMedia::ICON))
                ->resize(200, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->encode('webp');
            Storage::put(SocialMedia::DIR_THUMBNAIL . $profilePhotoPath, $img);
            $this->merge(['icon' => $profilePhotoPath]);
        }
    }
}
