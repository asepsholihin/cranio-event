<?php

namespace App\Http\Requests\CrewApp\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class LoginRequest extends FormRequest
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
            'password' => 'required_if:via,email',
            'no_hp' => 'required_if:via,phone',
            'pin' => 'required_if:via,phone',
            'device_name' => 'required',
            'device_id' => 'required',
            'via' => 'required|in:email,phone',
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        // $phoneNumber = $this->no_hp;

        // if (Str::startsWith($phoneNumber, '0')) {
        //     $phoneNumber = Str::replaceFirst('0', User::PREFIX_PHONE_NUMBER, $phoneNumber);
        // }

        // $this->merge(['no_hp' => $phoneNumber]);
    }
}
