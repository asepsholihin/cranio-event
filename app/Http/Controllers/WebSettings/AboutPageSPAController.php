<?php

namespace App\Http\Controllers\WebSettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebSettings\StoreAboutPageRequest;
use App\Models\WebAboutSetting;
use App\Support\StorageAttributes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Image;

class AboutPageSPAController extends Controller
{
    const SPA_PATH = '/web-settings/about-page';

    public function __construct()
    {
        $this->middleware('permission:web-settings-add-or-edit')->only(['store']);
    }

    /**
     * Display resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->responseLatestSetting();
    }

    public function apiIndex()
    {
        $setting = WebAboutSetting::latest()->first();

        return response()->json(
            !empty($setting)
                ? Arr::except($setting, ['id'])
                : $setting
        );
    }

    /**
     * Store resource.
     *
     * @param  StoreAboutPageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAboutPageRequest $request)
    {
        $setting = WebAboutSetting::latest()->first();
        $payload = $request->except(['header_image', 'legality_image', 'executive_image', 'director_image', 'org_image']);
        $uid = auth()->user()->id;

        empty($setting)
            ? $setting = WebAboutSetting::create(
                array_merge($payload, [
                    'created_by' => $uid,
                    'updated_by' => $uid,
                ])
            )
            : $setting->update($payload);

        $this->storeFile($request, $setting, ['header_image', 'legality_image', 'executive_image', 'director_image', 'org_image']);

        return $this->responseLatestSetting();
    }

    /**
     * Store file.
     *
     * @param  StoreAboutPageRequest  $request
     * @param  WebAboutSetting  $setting
     * @param  array  $keys
     * @return bool
     */
    private function storeFile(StoreAboutPageRequest $request, WebAboutSetting $setting, array $keys = [])
    {
        $files = [];

        foreach ($keys as $key) {
            if ($request->hasFile($key)) {
                $imageMake = Image::make($request->file($key));

                $img =  (string) $imageMake->encode('webp');
                $imgSmall =  $imageMake
                    ->resize(500, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->encode('webp', 90);

                $photoPath =  WebAboutSetting::DIR_FILE . pathinfo($request->file($key)->hashName(), PATHINFO_FILENAME) . '.webp';
                $profilePhotoSmallPath =  WebAboutSetting::DIR_FILE . StorageAttributes::SMALL_IMG_PREFIX . pathinfo($request->file($key)->hashName(), PATHINFO_FILENAME) . '.webp';
                Storage::put($photoPath, $img);
                Storage::put($profilePhotoSmallPath, $imgSmall);

                $files = array_merge($files, [$key => $photoPath]);
            }
        }

        return !empty($files)
            ? $setting->update($files)
            : true;
    }

    private function responseLatestSetting()
    {
        $setting = WebAboutSetting::latest()->first();

        return response()->json(
            !empty($setting)
                ? Arr::except($setting, ['id'])
                : $setting
        );
    }
}
