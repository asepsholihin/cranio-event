<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ParticipantBooking extends Model
{
    protected $fillable = [
        'booking_id',
        'participant_id',
        'created_by',
        'updated_by',
    ];

    public function scopeTableSearch($query)
    {
        $query->select('participant_bookings.*', 'participant.name', 'participant.whatsapp');
        $query->join('participants', 'participant.id', 'participant_bookings.participant_id');
        $search = '%' . request()->query('q') .'%';
        $query->where(function($q) use($search) {
            $q->where('participant_bookings.bank_account', 'like', $search)
            ->orWhere('participant_bookings.sender_name', 'like', $search);
        });

        return $query;
    }
}
