<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Support\Facades\App;
use Rennokki\QueryCache\Traits\QueryCacheable;

class WebContentSetting extends Model
{
    use QueryCacheable;

    const DIR_FILE = 'web/settings/files/';

    protected $table = 'web_content_settings';

    protected static $flushCacheOnUpdate = true;
    public $cacheFor = 3600;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'page',
        'section',
        'content_type',
        'image_url',
        'title',
        'subtitle',
        'text',
        'link_url',
        'alt_image',
        'title_image',
        'created_by',
        'updated_by',
        'overlay',
        'title_color',
        'subtitle_color',
        'text_color',
        'image_mobile_url',
        'icon_url',
        'longitude',
        'latitude',
        'forms'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function imageUrl(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) =>
                    StorageAttributes::getSmallScreenPath($attributes['image_url']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['image_url'] ?? null, null, true
            ),
        );
    }

    public function imageMobileUrl(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) =>
                    StorageAttributes::getSmallScreenPath($attributes['image_mobile_url']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['image_mobile_url'] ?? null, null, true
            ),
        );
    }

    public function iconUrl(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) =>
                    StorageAttributes::getSmallScreenPath($attributes['icon_url']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['icon_url'] ?? null, null, true
            ),
        );
    }
}
