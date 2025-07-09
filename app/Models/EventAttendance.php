<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class EventAttendance extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'event',
        'event_date',
        'event_end_at',
        'location',
        'transit_hotel_id',
        'event_at',
        'slug',
        'session',
        'session_information',
        'event_akbar',
        'is_closed_link_departure_confirmation',
        'hotel_name'
    ];

    public function scopeTableSearch($query)
    {
        $query->selectRaw("event_attendances.name, event_attendances.event, event_attendances.event_date, event_attendances.id AS id," .
        "(SELECT COALESCE(SUM(CASE WHEN check_in_at IS NOT NULL THEN 1 ELSE 0 END), 0) FROM attendances as att WHERE att.event_id = event_attendances.id AND session IS NULL) as total_checkin," .
        "(SELECT COALESCE(COUNT(DISTINCT(att.id)), 0) FROM attendances as att WHERE att.event_id = event_attendances.id AND session IS NULL) AS total_attendance, event_attendances.location, event_attendances.event_at, event_attendances.event_end_at");

        if (! empty(request()->query('event'))) {
            $query->where('event_attendances.event', request()->query('event'));
        }
        if (! empty(request()->query('month'))) {
            $date = Carbon::parse(request()->query('month')."-01");
            $start = $date->startOfMonth()->format('Y-m-d');
            $end = $date->endOfMonth()->format('Y-m-d');
            $query->whereBetween('event_attendances.event_date', [$start, $end]);
        }
        if (! empty(request()->query('q'))) {
            $search = '%' . request()->query('q') .'%';
            $query->where(function($q) use($search) {
                $q->where('name', 'like', $search)
                ->orWhere('event', 'like', $search);
            });
        }

        return $query;
    }
}
