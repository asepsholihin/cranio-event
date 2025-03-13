<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HotelRoomType extends Model
{
    use SoftDeletes;

    protected $table = 'master_hotel_room_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'hotel_id',
        'name',
        'pax_per_room',
        'price_per_pax',
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
        'created_at',
        'updated_at',
    ];

    public function scopeTableSearch($query)
    {
        $query->select(['master_hotel_room_types.*','master_hotels.name as hotel','master_hotels.star','master_hotels.pic_name','master_hotels.pic_phone','master_cities.name as city']);
        $query->join('master_hotels', 'master_hotels.id', 'master_hotel_room_types.hotel_id');
        $query->join('master_cities', 'master_cities.id', 'master_hotels.city_id');
        $search = '%' . request()->query('q') .'%';
        return $query->where('master_hotels.name',  request()->query('q'))
            ->orWhere('master_hotel_room_types.name', 'like', $search)
            ->orWhere('master_hotels.pic_name', 'like', $search);
    }
}
