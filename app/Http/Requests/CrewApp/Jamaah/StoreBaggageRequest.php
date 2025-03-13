<?php

namespace App\Http\Requests\CrewApp\Participant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\DetailBaggage;
use Image;

class StoreBaggageRequest extends FormRequest
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
            'location_id' => 'required',
            'city_id' => 'required',
            'participant_id' => 'required',
            'umroh_trip_id' => 'required',
            'total_bags' => 'required',
            'total_cabin' => 'required',
            'tag_status' => 'required',
            //'photo' => 'required|file|mimes:jpg,png,webp|max:1536'
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        if ($this->hasFile(DetailBaggage::PHOTO)) {
            $photos = $this->file('photo');
            $arrayFilePath = [];
            foreach($photos as $key => $photo) {
                $filePath = $this->file(DetailBaggage::PHOTO)[$key]->store(DetailBaggage::DIR_PHOTO);
                $img = Image::make($this->file(DetailBaggage::PHOTO)[$key])
                        // ->resize(200, null, function ($constraint) {$constraint->aspectRatio();})
                        ->encode('webp');
                Storage::put(DetailBaggage::DIR_THUMBNAIL . $filePath, $img);   
                $arrayFilePath[] = $filePath;
            }
            $this->merge(['image' => $arrayFilePath]);
        }
    }
}
