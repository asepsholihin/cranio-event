<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class StorageAttributes
{
    const CACHE_PRESIGNED_URL_IN_MINUTES = 30;
    const CACHE_STORE = 'file';
    const SMALL_IMG_PREFIX = 'small_';
    const THUMBNAIL_IMG_PREFIX = 'thumbnail_';

    public static function getTempUrl($path = null, $prefix = null, $smallScreen = false)
    {
        if (empty($path)) {
            return null;
        }

        if ($smallScreen) {
            $path = self::getSmallScreenPath($path);
        }

        $key = $prefix . $path;
        if (Cache::store(self::CACHE_STORE)->has($key)) {
            return Cache::store(self::CACHE_STORE)->get($key);
        }

        $expiredAt = now('UTC')->addMinutes(self::CACHE_PRESIGNED_URL_IN_MINUTES);
        $presignedUrl = Storage::url($key);
        Cache::store(self::CACHE_STORE)->put($key, $presignedUrl, $expiredAt->subMinute());

        return $presignedUrl;
    }

    public static function getSmallScreenPath($path)
    {
        if (request()->get('smallScreen', 'false') == 'true') {
            $path_dir = pathinfo($path, PATHINFO_DIRNAME) . '/';
            $filename = self::SMALL_IMG_PREFIX . pathinfo($path, PATHINFO_FILENAME) . '.' . pathinfo($path, PATHINFO_EXTENSION);
            return $path_dir . $filename;
        }

        return $path;
    }

    public static function getThumbnailUrl($path = null, $prefix = null)
    {
        if (empty($path)) {
            return null;
        }

        $path_dir = pathinfo($path, PATHINFO_DIRNAME) . '/';
        $filename = self::SMALL_IMG_PREFIX . pathinfo($path, PATHINFO_FILENAME) . '.' . pathinfo($path, PATHINFO_EXTENSION);
        $path = $path_dir . $filename;

        $key = $prefix . $path;
        if (Cache::store(self::CACHE_STORE)->has($key)) {
            return Cache::store(self::CACHE_STORE)->get($key);
        }

        $expiredAt = now('UTC')->addMinutes(self::CACHE_PRESIGNED_URL_IN_MINUTES);
        $presignedUrl = Storage::url($key);
        Cache::store(self::CACHE_STORE)->put($key, $presignedUrl, $expiredAt->subMinute());

        return $presignedUrl;
    }
}
