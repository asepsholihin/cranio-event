<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Crew;
use Image;

class StoreCrewRequest extends FormRequest
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
        $emailUniqueRules = 'nullable|email|unique:crews,name,NULL,id,deleted_at,NULL';
        $passwordRules = 'required|min:7';
        if (! empty($this->id)) {
            $emailUniqueRules = [ 'nullable', 'email', Rule::unique('crews')->ignore($this->id) ];
            $passwordRules = 'nullable|min:7';
        }

        return [
            'participant_id' => 'required',
            'email' => $emailUniqueRules,
            'name' => 'required|max:255',
            'password' => $passwordRules,
            'gender' => 'required',
            'location' => 'required',
            'photo' => 'nullable|file|mimes:jpg,png,webp|max:1536'
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
            $phoneNumber = Str::replaceFirst('0', Crew::PREFIX_PHONE_NUMBER, $phoneNumber);
        }

        if ($this->hasFile(Crew::PHOTO)) {
            $profilePhotoPath = $this->file(Crew::PHOTO)->store(Crew::DIR_PHOTO);
            $img = Image::make($this->file(Crew::PHOTO))
                    ->resize(200, null, function ($constraint) {$constraint->aspectRatio();})
                    ->encode();
            Storage::put(Crew::DIR_THUMBNAIL . $profilePhotoPath, $img);
            $this->merge(['profile_photo_path' => $profilePhotoPath]);
        }
        
        $this->merge(['no_hp' => $phoneNumber]);
    }
}
