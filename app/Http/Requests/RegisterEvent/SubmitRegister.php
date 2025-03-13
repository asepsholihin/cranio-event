<?php

namespace App\Http\Requests\RegisterEvent;

use App\Exceptions\ErrorMessageException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitRegister extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $token = request()->get(config('recaptcha.default_token_parameter_name', 'token'), '');
        if (! recaptcha()->validate($token)['success']) {
            throw new ErrorMessageException('Trafic Anda Gagal Diverifikasi Oleh Google reCAPTCHA V3');
        }
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
            'event_open_registration_id' => 'required|uuid',
            'name' => 'required',
            'no_hp' => ['required', Rule::unique('attendance_open_registrations')->where('event_open_registration_id', $this->event_open_registration_id)],
            'email' => 'required|email',
        ];
    }

    public function messages()
    {
        return [
            'no_hp.unique' => 'No Whatsapp Sudah Terdaftar Sebelumnya',
        ];
    }
}
