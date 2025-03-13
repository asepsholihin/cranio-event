<?php

namespace App\Http\Requests\Catalog;

use App\Models\WebProduct;
use Illuminate\Foundation\Http\FormRequest;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use App\Rules\MaxWords;
use Illuminate\Validation\Rule;

class WebProductRequest extends FormRequest
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
        $slugUniqueRules = 'nullable|unique:web_products';
        if (! empty($this->id)) {
            $slugUniqueRules = [ 'nullable', Rule::unique('web_products')->ignore($this->id) ];
        }
        return [
            'slug' => $slugUniqueRules,
            'sub_category_id' => 'required:exists:web_sub_categories,id',
            'package_type' => 'required:exists:package_umroh_trips,id',
            'trip_id' => 'required:exists:umroh_trips,id',
            'name' => 'required',
            'description' => 'nullable',
            'flights' => 'required',
            'image_thumbnail' => 'nullable|image|mimes:jpeg,png|max:8048',
            'image_background' => 'nullable|image|mimes:jpeg,png|max:8048',
            'image_itinerary' => 'nullable|image|mimes:jpeg,png|max:8048',
            'package_price' => 'required',
            'price_start_from' => 'required',
            'departure_date' => 'required|date',
            'status' => 'required|in:true,false',
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
        // $slug = !empty($this->slug) ? SlugService::createSlug(WebProduct::class, 'slug', $this->slug) : null;

        $meta_index = $this->meta_index == '[]' ? '' : $this->meta_index;
        $this->merge(['meta_index' => $meta_index]);

        $status_value = 0;
        if ($status) {
            $status_value = 1;
        }

        $this->merge(['status' => $status_value]);
    }
}
