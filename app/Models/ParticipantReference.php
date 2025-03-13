<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;

class ParticipantReference extends Model
{
    protected $fillable = [
        'participant_id',
        'name',
        'nik',
        'total_transaction',
        'umroh_trip_id',
        'booking_order_no',
        'total_pax',
        'created_by',
        'updated_by',
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
        $orderBy = request()->query('sortBy', 'id');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        
        $query->select('participant_references.*', 'umroh_trips.title as umroh_trip_name');
        $query->join('umroh_trips', 'umroh_trips.id', 'participant_references.umroh_trip_id');
        if(!empty(request()->query('q'))) {
            $search = '%' . request()->query('q') . '%';
            $query->where(function($query) use ($search) {
                $query->where('name', 'like', $search)
                ->orWhere('booking_order_no', 'like', $search)
                ->orWhere('nik', 'like', $search);
            });
        }
        $query->orderBy($orderBy, $sortBy);
        if (empty(request()->query('q', ''))) {
            return $query;
        }

        return $query;
    }
}
