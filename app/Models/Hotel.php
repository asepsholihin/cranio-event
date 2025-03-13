<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use SoftDeletes;

    protected $table = 'master_hotels';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'city_id',
        'name',
        'star',
        'image',
        'description',
        'address',
        'email',
        'pic_name',
        'pic_phone',
        'created_by',
        'updated_by',
        'informations',
        'images',
        'map_url',
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
        $query->select([
        'master_hotels.id',
        'master_hotels.city_id',
        'master_hotels.name',
        'master_hotels.star',
        'master_hotels.image',
        'master_hotels.description',
        'master_hotels.address',
        'master_hotels.email',
        'master_hotels.pic_name',
        'master_hotels.pic_phone',
        'master_hotels.created_by',
        'master_hotels.updated_by',
        'master_hotels.map_url',
        'master_cities.name as city']);
        $query->join('master_cities', 'master_cities.id', 'master_hotels.city_id');
        $search = '%' . request()->query('q') .'%';
        $query->where(function($q) use($search) {
            $q->where('master_hotels.name', 'like', $search)
            ->orWhere('master_cities.name', 'like', $search)
            ->orWhere('pic_name', 'like', $search);
        });
        return $query;
    }
}
