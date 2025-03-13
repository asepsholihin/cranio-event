<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Support\Facades\App;
use Rennokki\QueryCache\Traits\QueryCacheable;

class WebIndexSetting extends Model
{
    use QueryCacheable;

    const DIR_FILE = 'web/settings/files/';

    protected $table = 'web_index_settings';

    protected static $flushCacheOnUpdate = true;
    public $cacheFor = 3600;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'why_us_title',
        'about_image',
        'about_image_alt',
        'about_image_title',
        'about_title',
        'about_link',
        'profile_ustadz_image',
        'profile_ustadz_image_alt',
        'profile_ustadz_image_title',
        'profile_ustadz_title',
        'profile_ustadz_link',
        'tour_package_title',
        'article_title',
        'partner_title',
        'lang_id',
        'video_url',
        'status',
        'deleted',
        'created_by',
        'updated_by',
        'background_ustadz_salim',
        'background_ustadz_salim_image_alt',
        'background_ustadz_salim_image_title',
        'background_package',
        'background_package_image_alt',
        'background_package_image_title',
        'flagship_program_title',
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

    public function aboutImage(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    StorageAttributes::getSmallScreenPath($attributes['about_image']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['about_image'] ?? null, null, true
            ),
        );
    }

    public function profileUstadzImage(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    StorageAttributes::getSmallScreenPath($attributes['profile_ustadz_image']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['profile_ustadz_image'] ??  null, null, true
            ),
        );
    }

    public function backgroundUstadzSalim(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    StorageAttributes::getSmallScreenPath($attributes['background_ustadz_salim']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['background_ustadz_salim'] ??  null, null, true
            ),
        );
    }

    public function backgroundPackage(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    StorageAttributes::getSmallScreenPath($attributes['background_package']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['background_package'] ??  null, null, true
            ),
        );
    }
}
