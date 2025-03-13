<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'no_hp',
        'departure_year',
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
        'notes',
        'delivery_notes',
        'received_date',
        'status',
        'awb',
        'informations',
        'haji_khusus',
        'mitra'
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
            $query->whereBetween('calendar_requests.process_date', [$start, $end]);
        }
        if (!empty(request()->request_date)) {
            $dateXplode = explode('to', request()->request_date);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
            $query->whereBetween('calendar_requests.created_at', [$start, $end]);
        }
        if (!empty(request()->status)) {
            $query->where('calendar_requests.status', request()->status);
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
