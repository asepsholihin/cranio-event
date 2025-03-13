<?php

namespace App\Http\Requests\ParticipantApp\Participant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\ParticipantFile;
use App\Models\Participant;
use Image;

class StoreParticipantFileRequest extends FormRequest
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
            'participant_id' => 'required',
            'title' => 'required',
            //'file' => 'required|file|mimes:jpg,png,webp'
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        if ($this->hasFile(ParticipantFile::FILE)) {
            $files = $this->file('file');
            $arrayFilePath = [];
            foreach($files as $key => $file) {
                if($this->title == "Pas Photo") {
                    $filePath = $this->file(ParticipantFile::FILE)[$key]->store(Participant::DIR_PHOTO);
                    $img = Image::make($this->file(ParticipantFile::FILE)[$key])
                            ->resize(200, null, function ($constraint) {$constraint->aspectRatio();})
                            ->encode();
                    Storage::put(Participant::DIR_THUMBNAIL . $filePath, $img);
                    $arrayFilePath[] = $filePath;
                } else {
                    $filePath = $this->file(ParticipantFile::FILE)[$key]->store(ParticipantFile::DIR_FILE);
                    $img = Image::make($this->file(ParticipantFile::FILE)[$key])
                            ->resize(200, null, function ($constraint) {$constraint->aspectRatio();})
                            ->encode();
                    Storage::put(ParticipantFile::DIR_THUMBNAIL . $filePath, $img);   
                    $arrayFilePath[] = $filePath;
                }
            }
            $this->merge(['file_path' => $arrayFilePath]);
        }
    }
}
