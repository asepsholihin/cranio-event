<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Support\StorageAttributes;
use Illuminate\Validation\Rule;
use App\Models\Article;
use Image;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use App\Rules\MaxWords;

class StoreArticleRequest extends FormRequest
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
        $slugUniqueRules = 'nullable|unique:web_blogs';
        if (!empty($this->id)) {
            $slugUniqueRules = ['nullable', Rule::unique('web_blogs')->whereNull('deleted_at')->ignore($this->id)];
        }
        return [
            'title' => 'required',
            'slug' => $slugUniqueRules,
            'keywords' => 'required',
            // 'content' => 'required',
            'image' => 'file|mimes:jpg,png,webp|max:1536',
            'meta_description' => ['required', new MaxWords(300)]
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        $status = $this->save_as;

        $status_value = 0;
        if ($status == "publish") {
            $status_value = 1;
        }
        if (!$this->has('category_id')) {
            $this->merge(['category_id' => null]);
        }
        $written_by_id = null;
        if($this->written_by_id) {
            $written_by_id = $this->written_by_id;
        }
        $this->merge([
            'status' => $status_value,
            'lang_id' => 1,
            'written_by_id' => $written_by_id
        ]);

        // $slug = !empty($this->slug) ? SlugService::createSlug(Article::class, 'slug', $this->slug) : null;
        // $this->merge(['slug' => $slug]);

        if ($this->hasFile(Article::IMAGE)) {
            $imageMake = Image::make($this->file(Article::IMAGE));
            $img =  (string) $imageMake->encode('webp');

            $imgSmall =  (string) $imageMake
                ->resize(200, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->encode('webp');

            $profilePhotoPath =  Article::DIR_IMAGE . pathinfo($this->file(Article::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            $profilePhotoSmallPath =  Article::DIR_IMAGE . Article::DIR_THUMBNAIL . pathinfo($this->file(Article::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';

            Storage::put($profilePhotoPath, $img);
            Storage::put($profilePhotoSmallPath, $imgSmall);

            $this->merge(['image_url' => $profilePhotoPath, 'thumbnail_url' => $profilePhotoSmallPath]);
        }
    }
}
