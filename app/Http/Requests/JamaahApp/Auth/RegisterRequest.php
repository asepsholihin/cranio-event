<?php

namespace App\Http\Requests\ParticipantApp\Auth;

use App\Models\Participant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class RegisterRequest extends FormRequest
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
        ];
    }

        /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        $phoneNumber = $this->no_hp;

        if (Str::startsWith($phoneNumber, '0')) {
            $phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $phoneNumber);
        }

        $this->merge(['no_hp' => $phoneNumber]);
    }
}
