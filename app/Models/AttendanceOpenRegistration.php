<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use DB;

class AttendanceOpenRegistration extends Model
{
    const S3_PATH_BARCODE = 'web/OpenRegistration/Barcode';

    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'event_open_registration_id',
        'barcode',
        'name',
        'no_hp',
        'email',
        'is_alumni',
        'check_in_at',
        'barcode_thumbnail',
        'pax',
        'last_umroh_trip',
        'notes',
        'pax_ikhwan',
        'pax_akhwat'
    ];

    protected $appends = [
        'seats',
        'seat_codes'
    ];

    public function getSeatsAttribute()
    {
        $seats = EventOpenSeat::where('event_id', $this->event_open_registration_id)->where('parent_account_id', $this->id)->pluck('seat_name');
        return $seats ?? null;
    }

    public function getSeatCodesAttribute()
    {
        $seats = EventOpenSeat::where('event_id', $this->event_open_registration_id)->where('parent_account_id', $this->id)->pluck('seat_number');
        return $seats ?? null;
    }

    public function barcodeThumbnail(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => 
                "https://www.jejakimani.com/".$attributes['barcode_thumbnail'],
        );
    }

    public function scopeTableSearch($query)
    {
        if(request()->query('eventId')) {
            $query->where('event_open_registration_id',request()->query('eventId', 0));
        }
        $query->select(['attendance_open_registrations.*',
            DB::raw('SUM(CASE WHEN event_open_seats.check_in_at IS NOT NULL THEN 1 ELSE 0 END) as actual_pax'),
        ])
        ->leftJoin('event_open_seats', 'attendance_open_registrations.id', 'event_open_seats.parent_account_id')
        ->groupBy('attendance_open_registrations.id');
        $search = '%' . request()->query('q') .'%';
        return $query->where(function($q) use($search) {
            $q->where('name', 'like', $search)
            ->orWhere('no_hp', 'like', $search)
            ->orWhere('email', 'like', $search);
        });
    }
}
