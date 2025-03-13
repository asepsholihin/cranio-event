<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Support\Facades\App;

class GalleryContent extends Model
{
    protected $table = 'gallery_contents';

    const IMAGE = 'file_image';
    const DIR_IMAGE = 'web/gallery/';
    const DIR_THUMBNAIL = 'thumbnail/';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'title',
        'gallery_category_id',
        'image_url',
        'content',
        'status',
        'created_by',
        'updated_by'
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

    protected $appends = [
        'category_name',
        'category_slug'
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

    public function getCategoryNameAttribute()
    {
        $category = GalleryCategory::withTrashed()->find($this->gallery_category_id);
        return $category->name ?? null;
    }

    public function getCategorySlugAttribute()
    {
        $category = GalleryCategory::withTrashed()->find($this->gallery_category_id);
        return $category->slug ?? null;
    }

    public function scopeTableSearch($query)
    {
        $query
            ->leftjoin('gallery_categories', 'gallery_categories.id', '=', 'gallery_contents.gallery_category_id')
            ->select('gallery_contents.*', 'gallery_categories.name as category');

        if (request()->is('api/*')) {
            $query->where('gallery_contents.status', 1);
        }

        if (request()->categoryId) {
            $query->where('gallery_contents.gallery_category_id', request()->categoryId);
        }

        if (request()->category) {
            $query->where('gallery_categories.slug', request()->category);
        }
        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') . '%';
        return $query->where(function($q) use($search) {
            $q->where('title',  request()->query('q'))
            ->orWhere('content', 'like', $search);
        });
    }
}
