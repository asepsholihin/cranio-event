<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaMarketing extends Model
{
    use SoftDeletes;

    protected $table = 'media_marketings';

    const IMAGE = 'file_image';
    const DIR_IMAGE = 'web/media-marketing';
    const DIR_THUMBNAIL = 'thumbnail/';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'image',
        'category',
        'title',
        'status',
        'ratio_type',
        'layout_type',
        'created_by',
        'updated_by',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $casts = [
    ];

    public function image(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    $attributes['image'],
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['image'] ?? null
            ),
        );
    }

    public function scopeTableSearch($query)
    {
        if(auth()->user()->is_mitra == 1) {
            // Mitra
            $query->where('category', 'mitra');
        }

        if (!empty(request()->query('date'))) {
            $query->whereDate('created_at', request()->query('date'));
        }

        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') .'%';
        return $query->where(function($q) use($search) {
            $q->where('title', 'like', $search)
            ->orWhere('category', 'like', $search);
        });
    }
}
