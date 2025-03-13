<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Support\Facades\App;
use Rennokki\QueryCache\Traits\QueryCacheable;

class WebAboutSetting extends Model
{
    use QueryCacheable;

    const DIR_FILE = 'web/settings/files/';

    protected $table = 'web_about_settings';

    protected static $flushCacheOnUpdate = true;
    public $cacheFor = 3600;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'header_image',
        'header_image_alt',
        'header_image_title',
        'header_title',
        'vision_title',
        'vision_text_1',
        'vision_image_1',
        'vision_1_image_alt',
        'vision_1_image_title',
        'vision_text_2',
        'vision_image_2',
        'vision_2_image_alt',
        'vision_2_image_title',
        'vision_text_3',
        'vision_image_3',
        'vision_3_image_alt',
        'vision_3_image_title',
        'legality_title',
        'legality_image',
        'legality_image_alt',
        'legality_image_title',
        'executive_title',
        'executive_image',
        'executive_image_alt',
        'executive_image_title',
        'director_title',
        'director_image',
        'director_image_alt',
        'director_image_title',
        'director_text',
        'org_title',
        'org_image',
        'org_image_alt',
        'org_image_title',
        'created_by',
        'updated_by',
        'youtube_link',
        'text_inquiry',
        'flagship_program_title',
        'header_overlay'
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

    public function headerImage(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    StorageAttributes::getSmallScreenPath($attributes['header_image']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['header_image'] ?? null, null, true
            ),
        );
    }

    public function visionImage1(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    StorageAttributes::getSmallScreenPath($attributes['vision_image_1']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['vision_image_1'] ?? null, null, true
            ),
        );
    }

    public function visionImage2(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    StorageAttributes::getSmallScreenPath($attributes['vision_image_2']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['vision_image_2'] ?? null, null, true
            ),
        );
    }

    public function visionImage3(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    StorageAttributes::getSmallScreenPath($attributes['vision_image_3']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['vision_image_3'] ?? null, null, true
            ),
        );
    }

    public function legalityImage(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    StorageAttributes::getSmallScreenPath($attributes['legality_image']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['legality_image'] ?? null, null, true
            ),
        );
    }

    public function executiveImage(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    StorageAttributes::getSmallScreenPath($attributes['executive_image']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['executive_image'] ?? null, null, true
            ),
        );
    }

    public function directorImage(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    StorageAttributes::getSmallScreenPath($attributes['director_image']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['director_image'] ?? null, null, true
            ),
        );
    }

    public function orgImage(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    StorageAttributes::getSmallScreenPath($attributes['org_image']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['org_image'] ?? null, null, true
            ),
        );
    }
}
