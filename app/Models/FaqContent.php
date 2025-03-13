<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class FaqContent extends Model
{
    protected $table = 'faq_contents';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'title',
        'faq_category_id',
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

    public function getCategoryNameAttribute()
    {
        $category = FaqCategory::withTrashed()->find($this->faq_category_id);
        return $category->name ?? null;
    }

    public function getCategorySlugAttribute()
    {
        $category = FaqCategory::withTrashed()->find($this->faq_category_id);
        return $category->slug ?? null;
    }

    public function scopeTableSearch($query)
    {
        $search = '%' . request()->query('q') .'%';
        return $query->where('title',  request()->query('q'))
            ->orWhere('title', 'like', $search);
    }
}
