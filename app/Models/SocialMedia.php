<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    protected $table = 'social_media';

    protected static $flushCacheOnUpdate = true;

    public $cacheFor = 3600;

    const ICON = 'file_icon';
    const DIR_ICON = 'web/social-media';
    const DIR_THUMBNAIL = 'thumbnail/';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'social_media_name',
        'social_media_link',
        'icon',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
    ];

    protected $casts = [
        'created_at'  => 'date:d-m-Y',
    ];

    public function icon(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    $attributes['icon'],
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['icon'] ?? null
            ),
        );
    }

    public function scopeTableSearch($query)
    {
        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') .'%';
        return $query->where('social_media_name', 'like', $search)
        ->orWhere('social_media_link', 'like', $search);
    }
}
