<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Participant;
use Image;
use Carbon\Carbon;

class StoreParticipantRequest extends FormRequest
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
        $emailUniqueRules = 'nullable|email|unique:participant';
        $nikUniqueRules = 'required_if:nationality,WNI|numeric|unique:participant,nik,NULL,id,deleted_at,NULL|digits_between:14,16';
        $kitasUniqueRules = 'required_if:nationality,WNA|unique:participant,kitas_number,NULL,id,deleted_at,NULL';
        if (! empty($this->id)) {
            $emailUniqueRules = [ 'nullable', 'email' ];//Rule::unique('participants')->ignore($this->id) ];
            $nikUniqueRules = [ 'numeric', Rule::unique('participants')->ignore($this->id) ];
            $kitasUniqueRules = [ 'numeric', Rule::unique('participants')->ignore($this->id) ];
        }
        
        return [
            'email' => $emailUniqueRules,
            'nik' => $nikUniqueRules,
            'kitas_number' => $kitasUniqueRules,
            'name' => 'required|max:255',
            'gender' => 'required',
            'birth_date' => 'required|date',
            'body_size' => 'required',
            'home_address' => 'required',
            'photo' => 'nullable|file|mimes:jpg,png,webp',
            'no_hp' => 'numeric|digits_between:5,14'
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

        $infants = 2;
        if ($this->birth_date) {
            $birthDate = date('Y-m-d', strtotime($this->birth_date));
            $birthDate = Carbon::parse($birthDate);
            $diffYears = Carbon::now()->diffInYears($birthDate);
            $infants = ($diffYears <= 2) ? 1 : 2;
        }

        if ($this->hasFile(Participant::PHOTO)) {
            $profilePhotoPath = $this->file(Participant::PHOTO)->store(Participant::DIR_PHOTO);
            $img = Image::make($this->file(Participant::PHOTO))
                    ->resize(200, null, function ($constraint) {$constraint->aspectRatio();})
                    ->encode();
            Storage::put(Participant::DIR_THUMBNAIL . $profilePhotoPath, $img);
            $this->merge(['profile_photo_path' => $profilePhotoPath]);
        }

        $name = strtoupper($this->name);
        
        $this->merge(['name' => $name, 'no_hp' => $phoneNumber, 'infants' => $infants]);
    }
}
