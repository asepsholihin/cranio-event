<?php

namespace App\Http\Requests\WebSettings;

use Illuminate\Foundation\Http\FormRequest;

class StoreGeneralRequest extends FormRequest
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
            'web_logo' => 'nullable|image|mimes:jpeg,png|max:2048',
            'web_favicon' => 'nullable|image|mimes:jpeg,png|max:2048',
            'web_title' => 'required|min:3|max:255',
            'meta_keywords' => 'required|min:3',
            'meta_description' => 'required|min:7',
            'cta_button_text' => 'required|min:7|max:255',
            'web_email' => 'required|email',
            'phone_number' => 'required|min:10',
            'wa_number_1' => 'required|min:10',
            'wa_number_2' => 'required|min:10',
            'copyright_text' => 'required',
            'footer_consultation' => 'required',
            'footer_location' => 'required|min:10',
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        $status = $this->status;

        $status_value = 0;
        if ($status) {
            $status_value = 1;
        }

        $this->merge(['status' => $status_value]);
    }
}
