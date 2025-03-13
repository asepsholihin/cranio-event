<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Item;
use Image;

class StoreItemRequest extends FormRequest
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
            'category_id' => 'required',
            'name' => 'required',
            'unit_id' => 'required',
            'unit_price' => 'required',
            'image' => 'file|mimes:jpg,png,webp|max:1536'
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        $status = $this->status;

        $status_value = 0;
        if ($status == "true") {
            $status_value = 1;
        }

        $code = Item::generateCode();
        
        $uid = auth()->user()->id;

        $this->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'code' => $code,
            'status' => $status_value
        ]);

        if ($this->hasFile(Item::PHOTO)) {
            $profilePhotoPath = $this->file(Item::PHOTO)->store(Item::DIR_PHOTO);
            $img = Image::make($this->file(Item::PHOTO))
                    ->resize(800, null, function ($constraint) {$constraint->aspectRatio();})
                    ->encode();
            Storage::put(Item::DIR_THUMBNAIL . $profilePhotoPath, $img);
            $this->merge(['photo' => $profilePhotoPath]);
        }
    }
}
