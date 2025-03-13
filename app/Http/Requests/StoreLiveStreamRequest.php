<?php

namespace App\Http\Requests;

use App\Models\LiveStream;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;


class StoreLiveStreamRequest extends FormRequest
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
            'link' => 'required',
            'file_thumbnail' => 'file|mimes:jpg,png,webp|max:1536'
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

        $uid = auth()->user()->id;
        $this->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'status' => $status
        ]);
        

        if ($this->hasFile(LiveStream::THUMBNAIL)) {
            $profilePhotoPath = $this->file(LiveStream::THUMBNAIL)->store(LiveStream::DIR_THUMBNAIL);
            $img = Image::make($this->file(LiveStream::THUMBNAIL))
                ->resize(512, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->encode('webp');
            Storage::put(LiveStream::DIR_THUMBNAIL . $profilePhotoPath, $img);
            $this->merge(['thumbnail' => $profilePhotoPath]);
        }
    }
}
