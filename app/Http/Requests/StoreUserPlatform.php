<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StoreUserPlatform extends FormRequest
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
        $emailUniqueRules = 'email|unique:users';
        $passwordRules = 'required|min:7';
        if (! empty($this->id)) {
            $emailUniqueRules = ['email', Rule::unique('users')->ignore($this->id)];
            $passwordRules = 'nullable|min:7';
        }

        return [
            'name' => 'required|max:255',
            'email' => $emailUniqueRules,
            'password' => $passwordRules,
            'access_status' => 'required',
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        if (! empty($this->password)) {
            $this->merge(['password' => Hash::make($this->password)]);
            return;
        }

        $this->request->remove('password');
    }
}
