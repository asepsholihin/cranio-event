<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Inquiry;
use Image;

class StoreInquiryRequest extends FormRequest
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
            'full_name' => 'required|min:2|max:50',
            'wa_number' => 'required|numeric|digits_between:9,14',
            'email' => 'required|email',
            'message' => 'required',
            'from_page' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'full_name.required' => 'Nama harus diisi',
            'wa_number.required' => 'No. Handphone / Whatsapp harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
            'message.required' => 'Message harus diisi',
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        $this->merge(['lang_id' => 1]);
    }
}
