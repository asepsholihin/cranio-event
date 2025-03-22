<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Booking extends Model
{
    use SoftDeletes;

    const DIR_RECEIPT = 'web/receipts';
    const PREFIX_ORDER_NUMBER = 'ORD/';
    const MONTH_ROMAWI = [1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X", "XI","XII"];

    const STATUS_PENDING = "pending";
    const STATUS_PAID = "paid";
    const STATUS_BOOKED = "booked";
    const STATUS_ACCESS_GIVEN = "access_given";
    const STATUS_CANCEL = "cancelled";
    const STATUS_PAYMENT_EXPIRED = "payment_expired";

    protected $fillable = [
        'temp_booking_id',
        'booking_no',
        'account_name',
        'account_email',
        'account_wa',
        'account_hospital',
        'total_pax',
        'pax_assign',
        'package',
        'price_per_pax',
        'total_price',
        'total_paid',
        'total_unpaid',
        'order_status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function setOrderNumber($date = null)
    {
        $additional_code = "";
        if($date) {
            if(date('Y', strtotime($date)) != date('Y')) {
                $additional_code = "-" . date('y', strtotime($date));
            }
        }
        $orderNumber = self::PREFIX_ORDER_NUMBER . date('y/') . self::MONTH_ROMAWI[date('n')] ."/". str_pad($this->getIdInThisMonth(), 5, 0, STR_PAD_LEFT);
        $this->booking_no = $orderNumber . $additional_code;
        $this->save();
    }

    private function getIdInThisMonth()
    {
        $recordNumber = self::whereYear('created_at', date('Y'))
            ->withTrashed()
            ->count();

        return $recordNumber + 1;
    }

    public function scopeTableSearch($query)
    {
        $query->select('bookings.*');
        $search = '%' . request()->query('q') .'%';
        $query->where(function($q) use($search) {
            $q->where('bookings.account_name', 'like', $search)
            ->orWhere('bookings.account_wa', 'like', $search)
            ->orWhere('bookings.account_email', 'like', $search)
            ->orWhere('bookings.account_hospital', 'like', $search);
        });
        if (!empty(request()->date)) {
            $dateXplode = explode('to', request()->date);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
            $query->whereBetween('bookings.created_at', [$start, $end]);
        }
        if (!empty(request()->status)) {
            $query->where('bookings.order_status', request()->status);
        }

        return $query;
    }
}
