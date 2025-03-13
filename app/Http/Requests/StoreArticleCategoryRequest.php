<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\ArticleCategory;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use App\Rules\MaxWords;

class StoreArticleCategoryRequest extends FormRequest
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
        $slugUniqueRules = 'nullable|unique:web_blog_categories';
        if (! empty($this->id)) {
            $slugUniqueRules = [ 'nullable', Rule::unique('web_blog_categories')->ignore($this->id) ];
        }
        return [
            'name' => 'required',
            'slug' => $slugUniqueRules,
            'order' => 'required|numeric'
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {

    }
}
