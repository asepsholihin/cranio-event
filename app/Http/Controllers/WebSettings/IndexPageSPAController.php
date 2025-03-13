<?php

namespace App\Http\Controllers\WebSettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebSettings\StoreIndexPageRequest;
use App\Models\WebIndexSetting;
use App\Support\StorageAttributes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Image;

class IndexPageSPAController extends Controller
{
    const SPA_PATH = '/web-settings/index-page';

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
        $setting = WebIndexSetting::latest()->first();

        return response()->json(
            !empty($setting)
                ? Arr::except($setting, ['deleted', 'status', 'id'])
                : $setting
        );
    }

    /**
     * Store resource.
     *
     * @param  StoreIndexPageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreIndexPageRequest $request)
    {
        $setting = WebIndexSetting::latest()->first();
        $payload = $request->except(['about_image', 'profile_ustadz_image', 'background_ustadz_salim', 'background_package']);
        $uid = auth()->user()->id;

        empty($setting)
            ? $setting = WebIndexSetting::create(
                array_merge($payload, [
                    'created_by' => $uid,
                    'updated_by' => $uid,
                ])
            )
            : $setting->update($payload);

        $this->storeFile($request, $setting, ['about_image', 'profile_ustadz_image', 'background_ustadz_salim', 'background_package']);

        return $this->responseLatestSetting();
    }

    /**
     * Store file.
     *
     * @param  StoreIndexPageRequest  $request
     * @param  WebIndexSetting  $setting
     * @param  array  $keys
     * @return bool
     */
    private function storeFile(StoreIndexPageRequest $request, WebIndexSetting $setting, array $keys = [])
    {
        $files = [];

        foreach ($keys as $key) {
            if ($request->hasFile($key)) {
                $imageMake = Image::make($request->file($key));

                $img =  (string) $imageMake->encode('webp');
                $imgSmall =  (string) $imageMake
                ->resize(500, null, function ($constraint) {$constraint->aspectRatio();})
                ->encode('webp',90);

                $photoPath =  WebIndexSetting::DIR_FILE . pathinfo($request->file($key)->hashName(), PATHINFO_FILENAME) . '.webp';
                $profilePhotoSmallPath =  WebIndexSetting::DIR_FILE . StorageAttributes::SMALL_IMG_PREFIX . pathinfo($request->file($key)->hashName(), PATHINFO_FILENAME) . '.webp';

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
        $setting = WebIndexSetting::latest()->first();

        return response()->json(
            !empty($setting)
                ? Arr::except($setting, ['deleted', 'status', 'id'])
                : $setting
        );
    }
}
