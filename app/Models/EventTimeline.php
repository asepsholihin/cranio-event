<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use App\Support\StorageAttributes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Casts\Attribute;
use DB;
use Carbon\Carbon;

class EventTimeline extends Model implements Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'umroh_trip_id',
        'ticketing_id',
        'event_id',
        'manasik_offline_date',
        'manasik_offline_pax',
        'manasik_offline_location',
        'departure_standby_time',
        'departure_standby_notes',
        'arrival_standby_time',
        'arrival_standby_notes',
        'certificate_due_date',
        'certificate_status',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    public function scopeTableSearch($query)
    {
        $query->select(['event_timelines.*', 'umroh_trips.title as umroh_trip_title', 'umroh_trips.departure_at', 'umroh_trips.return_at', 'crew.name as tour_leader', 'crew.front_title as tour_leader_front_title', 'crew.back_title as tour_leader_back_title'])
        ->join('umroh_trips', 'umroh_trips.id', 'event_timelines.umroh_trip_id')
        ->join('ticketings', 'ticketings.id', 'event_timelines.ticketing_id')
        ->leftjoin('participant as crew', 'crew.id', 'umroh_trips.tour_leader');
        $search = '%' . request()->get('q') .'%';
        if(!empty(request()->get('q'))){
            $query->where('umroh_trips.title', 'like', $search);
        }
        if(!empty(request()->get('status'))){
            $query->where('event_timelines.certificate_status', request()->get('status'));
        }
        if(!empty(request()->query('month'))) {
            $date = Carbon::parse(request()->query('month')."-01");
            $start = $date->startOfMonth()->format('Y-m-d H:i:s');
            $end = $date->endOfMonth()->format('Y-m-d H:i:s');
            $query->whereBetween('umroh_trips.departure_at', [$start, $end]);
        } else {
            $query->where('umroh_trips.departure_at', '>=', Carbon::now());
        }
        $query->orderByRaw('umroh_trips.departure_at ASC');
        return $query;
    }

    public static function createOrUpdateEventTimeline($umrohTripId) {
        $ticketingId = Ticketing::where('umroh_trip_id', $umrohTripId)->first()->id ?? null;
        if($ticketingId) {
            $eventId = EventAttendance::where('umroh_trip_id', $umrohTripId)->first()->id ?? null;
            $eventTimeline = self::updateOrCreate(
                [
                    'umroh_trip_id' => $umrohTripId,
                    'ticketing_id' => $ticketingId,
                    'event_id' => $eventId
                ],
                [
                    'umroh_trip_id' => $umrohTripId,
                    'ticketing_id' => $ticketingId,
                    'event_id' => $eventId,
                    'created_by' => auth()->user()->id,
                ]
            );
        }
    }

    public function packages()
    {
        return $this->hasMany(PackageUmrohTrip::class, 'umroh_trip_id', 'umroh_trip_id');
    } 

    public function ticketing()
    {
        return $this->belongsTo(Ticketing::class, 'ticketing_id');
    } 

    public function event()
    {
        return $this->belongsTo(EventAttendance::class, 'event_id');
    } 

    public function manasikOnlines()
    {
        return $this->hasMany(EventManasikOnline::class, 'event_timeline_id');
    } 
}
