<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class HotelUmrohTrip extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'umroh_trip_id',
        'package_umroh_trip_id',
        'city_name',
        'is_published',
        'hotel_name',
        'pic_name',
        'pic_phone',
        'star',
        'nights',
        'check_in',
        'check_out',
    ];

    protected $appends = [
        'check_in_check_out'
    ];

    public function getCheckInCheckOutAttribute(){
        if(empty($this->check_out)){
            return $this->check_in;
        }
        if(empty($this->check_in)){
            return "";
        }
        return $this->check_in . ' to ' . $this->check_out;
    }
}
