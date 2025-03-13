<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaqCategory extends Model
{
    use SoftDeletes;

    protected $table = 'faq_categories';

    const ACCESS_STATUS_ACTIVE = 1;
    const ACCESS_STATUS_DISABLED = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'slug',
        'is_parent',
        'parent_id',
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
        'parent_category_name',
    ];

    public function getParentCategoryNameAttribute()
    {
        $parentCategory = self::where('id', $this->parent_id)->first();
        return $parentCategory->name ?? null;
    }

    public function scopeTableSearch($query)
    {
        $search = '%' . request()->query('q') .'%';
        return $query->where('name',  request()->query('q'))
            ->orWhere('name', 'like', $search);
    }
}
