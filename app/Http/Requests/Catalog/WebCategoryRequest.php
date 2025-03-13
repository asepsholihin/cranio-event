<?php

namespace App\Http\Requests\Catalog;

use App\Models\WebCategory;
use Illuminate\Foundation\Http\FormRequest;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class WebCategoryRequest extends FormRequest
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
            'name' => 'required|min:3|max:255',
            'description' => 'nullable',
            'order' => 'required|integer',
            'active' => 'required|in:true,false',
            'status' => 'required|in:true,false',
            'slug' => 'nullable',
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        if (!empty($this->id)) {
            $this->merge(['id' => (int)$this->id]);
        }

        $status = $this->status == 'true' ? true : false;
        $active = $this->active == 'true' ? true : false;
        // $slug = !empty($this->slug) ? SlugService::createSlug(WebCategory::class, 'slug', $this->slug) : null;

        $status_value = 0;
        if ($status) {
            $status_value = 1;
        }

        $active_value = 0;
        if ($active) {
            $active_value = 1;
        }

        $this->merge(['status' => $status_value, 'active' => $active_value]);
    }
}
