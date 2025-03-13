<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Rennokki\QueryCache\Traits\QueryCacheable;

class WebTourPackage extends Authenticatable
{
    use SoftDeletes;
    use HasApiTokens, HasFactory, Notifiable, QueryCacheable;

    protected $table = 'web_tour_packages';

    const IMAGE = 'image';
    const DIR_IMAGE = 'web/tour_package/';
    const DIR_THUMBNAIL = 'thumbnail/';

    protected static $flushCacheOnUpdate = true;

    public $cacheFor = 3600;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'lang_id',
        'image_url',
        'url',
        'title',
        'order',
        'button_title',
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
        'deleted_at',
        'created_by',
        'updated_by',
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

    public function scopeTableSearch($query)
    {
        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') .'%';
        return $query->where('title',  request()->query('q'))
            ->orWhere('url', 'like', $search);
    }
}
