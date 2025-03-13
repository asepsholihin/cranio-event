<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParticipantMerchandise extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'participant_id',
        'merchandise_type',
        'name',
        'no_hp',
        'province',
        'city',
        'district',
        'subdistrict',
        'postalcode',
        'address',
        'process_date',
        'pickup_date',
        'delivery_date',
        'process_by',
        'logistic_company',
        'recipient_name',
        'recipient_phone',
        'delivery_notes',
        'received_date',
        'notes',
        'status',
        'awb',
        'informations',
        'created_by',
        'updated_by',
        'deleted_by',
        'haji_khusus'
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
        if (!empty(request()->packing_date)) {
            $dateXplode = explode('to', request()->packing_date);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
            $query->whereBetween('participant_merchandises.process_date', [$start, $end]);
        }
        if (!empty(request()->request_date)) {
            $dateXplode = explode('to', request()->request_date);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
            $query->whereBetween('participant_merchandises.created_at', [$start, $end]);
        }
        if (!empty(request()->status)) {
            $query->where('participant_merchandises.status', request()->status);
        }
        if(!empty(request()->query('q'))) {
            $search = '%' . request()->query('q') .'%';
            $query->where(function($q) use($search) {
                $q->where('name', 'like', $search);
            });
        }

        return $query;
    }
}
