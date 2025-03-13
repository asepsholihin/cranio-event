<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class WebSeoPage extends Model
{
    use QueryCacheable;

    protected $table = 'web_seo_pages';

    const OG_IMAGE = 'og_file';
    const TWITTER_IMAGE = 'twitter_file';
    const DIR_OG_IMAGE = 'web/og_image';
    const DIR_TWITTER_IMAGE = 'web/twitter_image';
    const DIR_IMAGE = 'web/seo-settings';
    const DIR_THUMBNAIL = 'thumbnail/';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'page',
        'meta_keyword',
        'meta_title',
        'meta_description',
        'canonical',
        'created_by',
        'updated_by',
        'meta_index',
        'og_type',
        'og_url',
        'og_title',
        'og_description',
        'og_image',
        'twitter_site',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
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

    public function ogImage(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    StorageAttributes::getSmallScreenPath($attributes['og_image']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['og_image'] ?? null
            ),
        );
    }

    public function twitterImage(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    StorageAttributes::getSmallScreenPath($attributes['twitter_image']),
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['twitter_image'] ?? null
            ),
        );
    }

    public function scopeTableSearch($query)
    {
        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') . '%';
        return $query->where('page', 'like', $search);
    }
}
