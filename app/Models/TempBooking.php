<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempBooking extends Model
{
    protected $fillable = [
        'uuid',
        'account_name',
        'account_email',
        'account_wa',
        'account_hospital',
        'total_pax',
        'price_per_pax',
        'total_price',
        'package',
        'status'
    ];

    public function scopeTableSearch($query)
    {
        $query->select('temp_bookings.*');
        $search = '%' . request()->query('q') .'%';
        $query->where(function($q) use($search) {
            $q->where('temp_bookings.account_name', 'like', $search)
            ->orWhere('temp_bookings.account_wa', 'like', $search)
            ->orWhere('temp_bookings.account_email', 'like', $search)
            ->orWhere('temp_bookings.account_hospital', 'like', $search);
        });
        if (!empty(request()->date)) {
            $dateXplode = explode('to', request()->date);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
            $query->whereBetween('temp_bookings.created_at', [$start, $end]);
        }
        if (!empty(request()->status)) {
            $query->where('temp_bookings.status', request()->status);
        }

        return $query;
    }
}
