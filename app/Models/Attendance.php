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
        $query->join('participants', 'participant.id', '=', 'attendances.participant_id')
            ->join('event_attendances', 'event_attendances.id', 'attendances.event_id')
            ->leftjoin('participant_umroh_trips', function ($join) {
                $join->on('participant_umroh_trips.participant_id', 'attendances.participant_id');
                $join->on('participant_umroh_trips.umroh_trip_id', 'event_attendances.umroh_trip_id');
            })
            ->leftjoin('log_qontak_broadcasts', function ($join) {
                $join->on('log_qontak_broadcasts.participant_id', 'attendances.participant_id');
                $join->on('log_qontak_broadcasts.event_id', DB::raw('CAST(attendances.event_id AS varchar)'));
                $join->where('log_qontak_broadcasts.type', 'Link Event');
            })
            ->where('attendances.event_id',request()->query('eventId', 0))
            ->select(['attendances.event_id','attendances.participant_id',
            'participant.name', 'participant.name_in_passport', 'participant.no_hp', 'participant.gender','participant.profile_photo_path','participant.barcode','participant_umroh_trips.manasik_table','participant_umroh_trips.no_urut','participant_umroh_trips.umroh_trip_id',
            'log_qontak_broadcasts.whatsapp_status', 'log_qontak_broadcasts.status as qontak_status',
            DB::raw('(SELECT check_in_at FROM attendances as att WHERE att.participant_id = attendances.participant_id AND att.event_id = attendances.event_id AND session IS NULL limit 1) as check_in_at'),
            DB::raw('(
            CASE WHEN log_qontak_broadcasts.status IS NULL THEN \'a\'
            WHEN log_qontak_broadcasts.whatsapp_status=\'failed\' THEN \'b\'
            WHEN log_qontak_broadcasts.whatsapp_status!=\'failed\' THEN \'c\'
            ELSE \'d\' END) AS qontak'),
            DB::raw('(SELECT umroh_trips.title FROM participant_umroh_trips JOIN umroh_trips ON umroh_trips.id=participant_umroh_trips.umroh_trip_id WHERE participant_id=participant.id order by participant_umroh_trips.id desc limit 1) as trip'),
            DB::raw('(SELECT package_umroh_trips.name FROM participant_umroh_trips JOIN package_umroh_trips ON package_umroh_trips.id=participant_umroh_trips.package_umroh_trip_id WHERE participant_id=participant.id order by participant_umroh_trips.id desc limit 1) as package_name'),
            DB::raw('
                (CASE WHEN participant_umroh_trips.role_type = 2 THEN \'a\' 
                WHEN participant_umroh_trips.role_type = 3 THEN \'b\' 
                WHEN participant_umroh_trips.role_type = 4 THEN \'c\' 
                ELSE \'z\' END) AS crew'
            ),
            DB::raw('
                (CASE WHEN (SELECT count(check_in_at) FROM attendances as att WHERE att.participant_id = attendances.participant_id AND att.event_id = attendances.event_id AND session IS NULL limit 1) > 0 THEN \'z\'
                ELSE \'a\' END) AS checkin'
            ),
            ])
            ->groupBy('participant.id','attendances.event_id','attendances.participant_id','participant_umroh_trips.id','log_qontak_broadcasts.id');

        if (!empty(request()->query('booking'))) {
            $query->where('participant_umroh_trips.order_umroh_trip_id', request()->query('booking'));
        }
        if (!empty(request()->query('package'))) {
            $query->where('participant_umroh_trips.package_umroh_trip_id', request()->query('package'));
        }
        if (!empty(request()->query('statusLinkConfirm'))) {
            $statusLinkConfirm = request()->query('statusLinkConfirm');
            if($statusLinkConfirm == 'empty') $query->whereNull('log_qontak_broadcasts.status');
            if($statusLinkConfirm == 'sending') $query->where('log_qontak_broadcasts.status', 'Delivered')->whereNull('log_qontak_broadcasts.whatsapp_status');
            if($statusLinkConfirm == 'failed') $query->where('log_qontak_broadcasts.whatsapp_status', 'failed');
            if($statusLinkConfirm == 'delivered') $query->where('log_qontak_broadcasts.status', '!=', 'failed');
        }
        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') .'%';
        return $query->where(function($q) use ($search) {
            $q->where('participant.name', 'like', $search);
            $q->orWhere('participant.no_hp', 'like', $search);
        });
    }
}
