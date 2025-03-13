<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HotelEventRate extends Model
{
    protected $table = 'hotel_event_rate';
    protected $fillable = [
        'master_hotel_event_id',
        'rate_category',
        'item_name',
        'item_price',
        'created_by',
        'updated_by'
    ];

    protected $appends = [
        'rate_name'
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function getRateNameAttribute(){
        $data = DB::table('hotel_event_rate_category')->where('id', $this->rate_category)->first();
        return $data ?? '';
    }
}
