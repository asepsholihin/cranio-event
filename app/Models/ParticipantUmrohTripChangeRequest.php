<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ParticipantUmrohTripChangeRequest extends Model
{
    protected $table = "participant_umroh_trip_change_requests";

    protected $fillable = [
        'order_item_umroh_trip_change_request_id',
        'participant_umroh_trip_id',
        'prev_umroh_trip_id',
        'prev_package_umroh_trip_id',
        'prev_room_type',
        'new_umroh_trip_id',
        'new_package_umroh_trip_id',
        'new_room_type',
        'infants'
    ];
}
