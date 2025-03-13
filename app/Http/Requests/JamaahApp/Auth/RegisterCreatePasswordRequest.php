<?php

namespace App\Http\Requests\ParticipantApp\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCreatePasswordRequest extends FormRequest
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
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'no_hp' => 'required_if:via,phone',
            'via' => 'required|in:email,phone',
            'code' => 'required',
            'device_name' => 'required',
            'device_id' => 'required',
            'created_from' => 'required'
        ];
    }
}
