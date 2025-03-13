<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRateHistory extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'hotel_id',
        'transaction_date',
        'room_single_rate',
        'room_double_rate',
        'room_triple_rate',
        'room_quad_rate',
        'room_queen_rate',
        'total_price',
        'currency',
        'price_convertion',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    protected $appends = [
        'created_by_name',
        'updated_by_name',
    ];

    public function getCreatedByNameAttribute()
    {
        $user = User::find($this->created_by);
        return $user->name ?? '';
    }

    public function getUpdatedByNameAttribute()
    {
        $user = User::find($this->updated_by);
        return $user->name ?? '';
    }

    public function scopeTableSearch($query)
    {
        $search = '%' . request()->query('q') .'%';
        $query->select(['hotel_rate_histories.*','master_hotels.name']);
        $query->join('master_hotels', 'master_hotels.id', 'hotel_rate_histories.hotel_id');
        $query->where(function($q) use($search) {
            $q->where('master_hotels.name',  request()->query('q'))
            ->orWhere('master_hotels.name', 'like', $search);
        });

        return $query;
    }
}
