<?php

namespace App\Http\Requests;

use App\Models\Asatidz;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Support\StorageAttributes;
use Image;


class StoreAsatidzRequest extends FormRequest
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
            'name' => 'required',
            'file_photo' => 'file|mimes:jpg,png,jpeg,webp|max:1536',
            'file_banner' => 'file|mimes:jpg,png,jpeg,webp|max:1536',
            'file_profile_picture' => 'file|mimes:jpg,png,jpeg,webp|max:1536'
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        $status = 0;
        if ($this->status == "true") {
            $status = 1;
        }

        $slug = Str::slug($this->name, '-');
        if($this->slug) {
            $slug = $this->slug;
        }

        $checkExistSlug = Asatidz::whereNot('id', $this->id)->where('slug', $slug)->count();
        if ($checkExistSlug > 0) {
            $slug = $slug . "-" . $checkExistSlug + 1;
        }
        $uid = auth()->user()->id;
        $this->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'slug' => $slug,
            'status' => $status
        ]);

        if ($this->hasFile(Asatidz::PROFILE_PICTURE)) {
            $imageMake = Image::make($this->file(Asatidz::PROFILE_PICTURE));

            $img =  (string) $imageMake->encode('webp');
            $imgSmall =  (string) $imageMake->resize(500, null, function ($constraint) { $constraint->aspectRatio(); })->encode('webp');

            $profilePhotoPath =  Asatidz::DIR_PHOTO . pathinfo($this->file(Asatidz::PROFILE_PICTURE)->hashName(), PATHINFO_FILENAME) . '.webp';
            $profilePhotoSmallPath =  Asatidz::DIR_PHOTO . StorageAttributes::THUMBNAIL_IMG_PREFIX . pathinfo($this->file(Asatidz::PROFILE_PICTURE)->hashName(), PATHINFO_FILENAME) . '.webp';

            Storage::put($profilePhotoPath, $img);
            Storage::put($profilePhotoSmallPath, $imgSmall);

            $this->merge(['profile_picture' => $profilePhotoPath, 'profile_picture_thumbnail' => $profilePhotoSmallPath]);
        }
        
        if ($this->hasFile(Asatidz::PHOTO)) {
            $imageMake = Image::make($this->file(Asatidz::PHOTO));

            $img =  (string) $imageMake->encode('webp');
            $imgSmall =  (string) $imageMake->resize(500, null, function ($constraint) { $constraint->aspectRatio(); })->encode('webp');

            $profilePhotoPath =  Asatidz::DIR_PHOTO . pathinfo($this->file(Asatidz::PHOTO)->hashName(), PATHINFO_FILENAME) . '.webp';
            $profilePhotoSmallPath =  Asatidz::DIR_PHOTO . StorageAttributes::THUMBNAIL_IMG_PREFIX . pathinfo($this->file(Asatidz::PHOTO)->hashName(), PATHINFO_FILENAME) . '.webp';

            Storage::put($profilePhotoPath, $img);
            Storage::put($profilePhotoSmallPath, $imgSmall);

            $this->merge(['photo' => $profilePhotoPath, 'photo_thumbnail' => $profilePhotoSmallPath]);
        }

        if ($this->hasFile(Asatidz::BANNER)) {
            $imageMake = Image::make($this->file(Asatidz::BANNER));

            $img =  (string) $imageMake->encode('webp');
            $imgSmall =  (string) $imageMake->resize(1080, null, function ($constraint) { $constraint->aspectRatio(); })->encode('webp');

            $profilePhotoPath =  Asatidz::DIR_PHOTO . pathinfo($this->file(Asatidz::BANNER)->hashName(), PATHINFO_FILENAME) . '.webp';
            $profilePhotoSmallPath =  Asatidz::DIR_PHOTO . StorageAttributes::THUMBNAIL_IMG_PREFIX . pathinfo($this->file(Asatidz::BANNER)->hashName(), PATHINFO_FILENAME) . '.webp';

            Storage::put($profilePhotoPath, $img);
            Storage::put($profilePhotoSmallPath, $imgSmall);

            $this->merge(['banner' => $profilePhotoPath, 'banner_thumbnail' => $profilePhotoSmallPath]);
        }
    }
}
