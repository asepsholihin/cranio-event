<?php

namespace App\Http\Requests;

use App\Models\Form;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Image;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class StoreSurveyRequest extends FormRequest
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
        $slugUniqueRules = 'nullable|unique:forms';
        if (!empty($this->id)) {
            $slugUniqueRules = ['nullable', Rule::unique('forms')->whereNull('deleted_at')->ignore($this->id)];
        }

        return [
            'title' => 'required',
            'slug' => $slugUniqueRules,
            'image' => 'file|mimes:jpg,png,webp|max:1536'
        ];
    }

    public function  passedValidation()
    {
        $status = $this->status;
        $this->merge(['status' => 0]);
        if ($status == "true") {
            $this->merge(['status' => 1]);
        }
        if ($this->hasFile(Form::IMAGE)) {
            $img =  (string) Image::make($this->file(Form::IMAGE))->encode('webp');
            $profilePhotoPath =  Form::DIR_IMAGE . pathinfo($this->file(Form::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            $storeImage = Storage::put($profilePhotoPath, $img);
            $this->merge(['image_url' => $profilePhotoPath]);
        }
    }
}
