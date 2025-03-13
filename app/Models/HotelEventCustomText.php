<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelEventCustomText extends Model
{
    protected $table = 'hotel_event_custom_text';
    protected $fillable = [
        'master_hotel_event_id',
        'category',
        'custom_text',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [
        'updated_at'
    ];
}
