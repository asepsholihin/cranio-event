<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Str;

class BookingReceipt extends Model implements Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable;

    const DIR_FILE = 'web/receipts/';
    const PREFIX_ORDER_NUMBER = 'Inv.Cranio/';
    const MONTH_ROMAWI = [1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X", "XI","XII"];

    const STATUS_PENDING = "pending";
    const STATUS_PAID = "paid";
    const STATUS_BOOKED = "booked";
    const STATUS_ACCESS_GIVEN = "access_given";
    const STATUS_CANCEL = "cancelled";
    const STATUS_PAYMENT_EXPIRED = "payment_expired";

    protected $fillable = [
        'receipt_no',
        'booking_id',
        'payment_amount',
        'bank_account',
        'sender_name',
        'evidence',
        'status',
        'updated_by',
        'deleted_by',
    ];

    public function evidence(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['evidence'] ?? null
            ),
        );
    }

    public function setOrderNumber($date = null)
    {
        $additional_code = "";
        if($date) {
            if(date('Y', strtotime($date)) != date('Y')) {
                $additional_code = "-" . date('y', strtotime($date));
            }
        }
        $eventPlace = "Bali";
        $orderNumber = self::PREFIX_ORDER_NUMBER . self::MONTH_ROMAWI[date('n')] ."/". $eventPlace ."/". date('y') . "/" . str_pad($this->getIdInThisMonth(), 5, 0, STR_PAD_LEFT);
        $this->receipt_no = $orderNumber . $additional_code;
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
        $query->select('booking_receipts.*', 'bookings.booking_no', 'bookings.account_wa');
        $query->join('bookings', 'bookings.id', 'booking_receipts.booking_id');
        $search = '%' . request()->query('q') .'%';
        $query->where(function($q) use($search) {
            $q->where('booking_receipts.bank_account', 'like', $search)
            ->orWhere('booking_receipts.sender_name', 'like', $search);
        });
        if (!empty(request()->date)) {
            $dateXplode = explode('to', request()->date);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
            $query->whereBetween('booking_receipts.created_at', [$start, $end]);
        }
        if (!empty(request()->status)) {
            $query->where('booking_receipts.status', request()->status);
        }

        return $query;
    }
}
