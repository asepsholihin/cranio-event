<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Support\Facades\App;

class WebItinerary extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    use QueryCacheable;

    public $cacheFor = 3600;

    protected static $flushCacheOnUpdate = true;

    const IMAGE = 'image';
    const DIR_IMAGE = 'web/image-itinerary';
    const DIR_THUMBNAIL = 'thumbnail/';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'image_url',
        'package_duration',
        'notes',
        'alt_image',
        'title_image',
        'category_id',
        'subcategory_id',
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

    protected $appends = [
        'category_name',
        'subcategory_name'
    ];
    public function getCategoryNameAttribute(){
        $categories = WebCategory::find($this->category_id);
        return $categories->name ?? ' - ';
    }

    public function getSubcategoryNameAttribute(){
        $subCategories = WebSubcategory::find($this->subcategory_id);
        return $subCategories->name ?? ' - ';
    }

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

        $search = '%' . request()->query('q') . '%';
        return $query->where('package_duration', 'like', $search)
            ->orWhere('package_duration', 'like', $search);
    }
}
