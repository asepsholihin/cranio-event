<?php

namespace App\Http\Requests\WebSettings;

use Illuminate\Foundation\Http\FormRequest;

class StoreAboutPageRequest extends FormRequest
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
        $headerTitleRules = 'required|min:3|max:255';
        $visionTitleRules = 'required|min:3|max:255';
        if (! empty($this->update_only)) {
            $headerTitleRules = 'min:3|max:255';
            $visionTitleRules = 'min:3|max:255';
        }

        return [
            'header_title' => $headerTitleRules,
            'header_image' => 'nullable|image|mimes:jpeg,png|max:2048',
            'vision_title' => $visionTitleRules,
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        $overlay = $this->header_overlay;
        $overlay_value = 0;
        if ($overlay == "true" || $overlay == 1) {
            $overlay_value = 1;
        }
        $this->merge(['header_overlay' => $overlay_value]);
    }
}
