<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Illuminate\Support\Facades\App;

class WebContentProduct extends Authenticatable
{
    use SoftDeletes;
    use Notifiable, QueryCacheable;

    protected $table = 'web_content_products';

    protected static $flushCacheOnUpdate = true;

    public $cacheFor = 3600;

    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 2;

    const IMAGE = 'image';
    const DIR_IMAGE = 'web/product/images/';
    const DIR_THUMBNAIL = 'thumbnail/';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'package_id',
        'image_url',
        'title',
        'description',
        'duration',
        'flight',
        'hotel',
        'price_start_from',
        'currency',
        'order',
        'status',
        'waiting_period',
        'maktab_type',
        'url',
        'slug'
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
                get: fn ($value, $attributes) => $attributes['image_url'] ?? null
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

        $search = '%' . request()->query('q') .'%';
        return $query->where('title',  request()->query('q'));
    }
}
