<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantAlbum extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'umroh_trip_id',
        'order_umroh_trip_id',
        'account_name',
        'participant_reference_id',
        'number_of_participant',
        'album_qty',
        'notes',
        'created_by',
        'updated_by',
    ];

    public function scopeTableSearch($query)
    {
        $search = '%' . request()->query('q') .'%';
        $query->where('account_name', 'like', $search);
        return $query;
    }
}
