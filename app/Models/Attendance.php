<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\StorageAttributes;
use DB;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'event_id',
        'participant_id',
        'check_in_at',
        'umroh_trip_id',
        'confirm',
        'confirm_at',
        'package_umroh_trip_id',
        'group_bus',
        'departure_from',
        'departure_from_update',
        'departure_confirmation_at',
        'departure_confirmation_by',
        'confirm_by',
        'check_in_at_online',
        'session'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_thumbnail',
    ];

    public function profileThumbnail(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['profile_photo_path'] ?? null,
                Participant::DIR_THUMBNAIL
            ),
        );
    }

    public function scopeTableSearch($query)
    {
        $query->join('participants', 'participants.id', '=', 'attendances.participant_id')
        ->join('event_attendances', 'event_attendances.id', 'attendances.event_id')
        ->join('participant_bookings', 'participant_bookings.participant_id', 'attendances.participant_id')
        ->join('bookings', 'participant_bookings.booking_id', 'bookings.id')
        ->where('attendances.event_id',request()->query('eventId', 0))
        ->select(['attendances.event_id','attendances.participant_id',
        'participants.name', 'participants.whatsapp', 'participants.gender','participants.profile_photo_path','participants.barcode', 'bookings.account_hospital',
        DB::raw('(SELECT check_in_at FROM attendances as att WHERE att.participant_id = attendances.participant_id AND att.event_id = attendances.event_id AND session IS NULL limit 1) as check_in_at'),
        DB::raw('
            (CASE WHEN (SELECT count(check_in_at) FROM attendances as att WHERE att.participant_id = attendances.participant_id AND att.event_id = attendances.event_id AND session IS NULL limit 1) > 0 THEN \'z\'
            ELSE \'a\' END) AS checkin'
        ),
        ])
        ->whereNull('participants.deleted_at');

        if (!empty(request()->query('booking'))) {
            $query->where('participant_bookings.order_umroh_trip_id', request()->query('booking'));
        }
        if (!empty(request()->query('package'))) {
            $query->where('participant_bookings.package_umroh_trip_id', request()->query('package'));
        }
        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') .'%';
        return $query->where(function($q) use ($search) {
            $q->where('participants.name', 'like', $search);
            $q->orWhere('participants.whatsapp', 'like', $search);
        });
    }
}
