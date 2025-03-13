<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Rennokki\QueryCache\Traits\QueryCacheable;

class WebSubcategory extends Model
{
    use HasFactory, Sluggable, QueryCacheable;

    protected $table = 'web_sub_categories';

    protected static $flushCacheOnUpdate = true;

    public $cacheFor = 3600;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'lang_id',
        'category_id',
        'name',
        'slug',
        'description',
        'active',
        'order',
        'status',
        'deleted',
        'created_by',
        'updated_by',
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

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function category()
    {
        return $this->belongsTo(WebCategory::class, 'category_id');
    }

    public function scopeTableSearch($query)
    {
        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') . '%';
        return $query->where('name', 'like', $search)
            ->orWhere('description', 'like', $search)
            ->orWhereHas('category', function ($category) use ($search) {
                $category->where('name', 'like', $search);
            });
    }
}
