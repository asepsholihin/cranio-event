<?php

namespace App\Http\Requests\ParticipantApp\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterVerificationCodeRequest extends FormRequest
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
        return [
            'email' => 'required_if:via,email|email',
            'no_hp' => 'required_if:via,phone',
            'via' => 'required|in:email,phone',
            'code' => 'required'
        ];
    }
}
