<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingTransaction extends Model
{
    protected $table = 'log_booking_umroh_trip_transactions';

    protected $fillable = [
        'order_umroh_trip_id',
        'invoice_umroh_trip_id',
        'category',
        'transaction_id',
        'transaction_status',
        'payment_information'
    ];

    public function scopeTableSearch($query)
    {
        $query->join('order_umroh_trips', 'order_umroh_trips.id', 'log_booking_umroh_trip_transactions.order_umroh_trip_id')
        ->join('invoice_umroh_trips', 'invoice_umroh_trips.id', 'log_booking_umroh_trip_transactions.invoice_umroh_trip_id')
        ->select(['log_booking_umroh_trip_transactions.*', 'invoice_umroh_trips.payment_amount', 'invoice_umroh_trips.payment_created_at', 'order_umroh_trips.name', 'order_umroh_trips.no_hp', 'order_umroh_trips.total_pax_trip', 'invoice_umroh_trips.payment_date', 'invoice_umroh_trips.invoice_no']);

        $search = '%' . request()->query('q') .'%';
        $query->where(function($q) use($search) {
            $q->where('order_umroh_trips.name', 'like', $search)
            ->orWhere('invoice_umroh_trips.invoice_no', 'like', $search)
            ->orWhere('category', 'like', $search)
            ->orWhere('order_umroh_trips.no_hp', 'like', $search);
        });
        
        return $query;
    }
}
