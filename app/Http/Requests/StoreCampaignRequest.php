<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Support\StorageAttributes;
use Illuminate\Validation\Rule;
use Image;


class StoreCampaignRequest extends FormRequest
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
        $slugUniqueRules = 'nullable|unique:campaigns';
        if (! empty($this->id)) {
            $slugUniqueRules = [ 'nullable', Rule::unique('campaigns')->ignore($this->id) ];
        }
        return [
            'title' => 'required',
            'slug' => $slugUniqueRules,
            'file_thumbnail' => 'file|mimes:jpg,png,jpeg,webp|max:1536',
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

        $slug = Str::slug($this->name, '-');
        if($this->slug) {
            $slug = $this->slug;
        }

        $checkExistSlug = Campaign::whereNot('id', $this->id)->where('slug', $slug)->count();
        if ($checkExistSlug > 0) {
            $slug = $slug . "-" . $checkExistSlug + 1;
        }
        $uid = auth()->user()->id;
        $this->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'slug' => $slug,
            'status' => $status
        ]);

        if ($this->hasFile(Campaign::THUMBNAIL)) {
            $imageMake = Image::make($this->file(Campaign::THUMBNAIL));

            $img =  (string) $imageMake->encode('webp');

            $profilePhotoPath =  Campaign::DIR_THUMBNAIL . pathinfo($this->file(Campaign::THUMBNAIL)->hashName(), PATHINFO_FILENAME) . '.webp';

            Storage::put($profilePhotoPath, $img);

            $this->merge(['thumbnail_url' => $profilePhotoPath]);
        }
    }
}
