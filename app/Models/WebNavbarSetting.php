<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class WebNavbarSetting extends Model
{
    use HasFactory, QueryCacheable;

    protected $table = 'web_navbar_settings';

    protected static $flushCacheOnUpdate = true;

    public $cacheFor = 3600;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'title',
        'url',
        'order',
        'parent_id',
        'show',
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
        'updated_at',
    ];

    protected $casts = [
        'created_at'  => 'date:d-m-Y',
    ];

    protected $appends = [
        'parent_navbar',
    ];

    public function getParentNavbarAttribute()
    {
        $parent = self::where('id', $this->parent_id)->first();
        return $parent->title ?? null;
    }
    
    public function scopeTableSearch($query)
    {
        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') . '%';
        return $query->where('title', 'like', $search);
    }
}
