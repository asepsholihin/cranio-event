<?php

namespace App\Http\Requests;

use App\Models\WebSeoPage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Image;


class StoreWebSeoPageRequest extends FormRequest
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
            'og_type' => 'required',
            'twitter_card' => 'required',
            'tog_file' => 'file|mimes:jpg,png,webp|max:1536',
            'twitter_file' => 'file|mimes:jpg,png,webp|max:1536'
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        if ($this->hasFile(WebSeoPage::OG_IMAGE)) {
            $profilePhotoPath = $this->file(WebSeoPage::OG_IMAGE)->store(WebSeoPage::DIR_OG_IMAGE);
            $img = Image::make($this->file(WebSeoPage::OG_IMAGE))
                ->resize(200, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->encode('webp');
            Storage::put(WebSeoPage::DIR_THUMBNAIL . $profilePhotoPath, $img);
            $this->merge(['og_image' => $profilePhotoPath]);
        }

        if ($this->hasFile(WebSeoPage::TWITTER_IMAGE)) {
            $profilePhotoPath = $this->file(WebSeoPage::TWITTER_IMAGE)->store(WebSeoPage::DIR_TWITTER_IMAGE);
            $img = Image::make($this->file(WebSeoPage::TWITTER_IMAGE))
                ->resize(200, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->encode('webp');
            Storage::put(WebSeoPage::DIR_THUMBNAIL . $profilePhotoPath, $img);
            $this->merge(['twitter_image' => $profilePhotoPath]);
        }
    }
}
