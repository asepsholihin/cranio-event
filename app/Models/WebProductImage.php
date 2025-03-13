<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Support\Facades\App;
use Rennokki\QueryCache\Traits\QueryCacheable;

class WebProductImage extends Model
{
    use QueryCacheable;

    protected $table = 'web_product_images';
    public $cacheFor = 3600;

    protected static $flushCacheOnUpdate = true;

    const IMAGE = 'image';
    const DIR_IMAGE = 'web/product/images/';
    const DIR_THUMBNAIL = 'thumbnail/';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'web_product_id',
        'image_url',
        'type',
        'status',
        'alt_image',
        'title_image',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function imageUrl(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    $attributes['image_url'],
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['image_url'] ?? null
            ),
        );
    }

    public function scopeTableSearch($query)
    {
        if (empty(request()->query('q', ''))) {
            return $query;
        }
    }
}
