<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Itinerary extends Model
{
    use SoftDeletes;

    protected $table = 'itineraries';

    const ACCESS_STATUS_ACTIVE = 1;
    const ACCESS_STATUS_DISABLED = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'package_type',
        'day_title',
        'time',
        'title',
        'description',
        'is_reminder',
        'status',
        'category_id',
        'order'
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

    public function scopeTableSearch($query)
    {
        $query->select('itineraries.*', 'itinerary_categories.name as category_name')
        ->join('itinerary_categories', 'itinerary_categories.id', 'itineraries.category_id');
        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') .'%';
        return $query->where('title',  request()->query('q'))
            ->orWhere('package_type', 'like', $search);
    }
}
