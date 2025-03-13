<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Support\Facades\App;

class EventOpenRegistration extends Model
{
    use SoftDeletes;
    use HasFactory;

    const IMAGE = 'image';
    const DIR_IMAGE = 'web/events/';
    const DIR_THUMBNAIL = 'thumbnail/';

    protected $primaryKey = 'uuid';
    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'location',
        'event_date',
        'close_registration',
        'event_at',
        'slug',
        'event_end_at',
        'speaker',
        'is_paid_event',
        'number_of_seats',
        'price',
        'image_url',
        'image_thumbnail_url'
    ];

    protected $casts = [
        'event_at'  => 'date:H:i',
        'event_end_at'  => 'date:H:i',
        'is_paid_event' => 'boolean',
        'available_seat'
    ];

    protected $appends = [
        'available_seats'
    ];

    public function getAvailableSeatsAttribute()
    {
        $takenSeats = EventTicketTransaction::where('event_id', $this->uuid)->whereIn('transaction_status', ['WAITING PAYMENT', 'PAID'])->sum('pax');
        $avaliableSeats = $this->number_of_seats - $takenSeats;
        return $avaliableSeats ?? 0;
    }

    public function imageThumbnailUrl(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    $attributes['image_thumbnail_url'],
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['image_thumbnail_url'] ?? null
            ),
        );
    }

    public function imageUrl(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    $attributes['image_url'],
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['image_url'] ?? null
            ),
        );
    }

    public function scopeTableSearch($query)
    {
        $query
        ->leftJoin('attendance_open_registrations', 'event_open_registrations.uuid', '=', 'attendance_open_registrations.event_open_registration_id')
        ->leftJoin('event_open_seats', 'attendance_open_registrations.id', 'event_open_seats.parent_account_id')
        ->selectRaw("event_open_registrations.name, event_open_registrations.location, event_open_registrations.close_registration, event_open_registrations.event_date, event_open_registrations.uuid AS id, event_open_registrations.is_paid_event," .
                    "SUM(CASE WHEN event_open_seats.check_in_at IS NOT NULL THEN 1 ELSE 0 END) AS total_checkin," .
                    "count(event_open_seats.id) AS total_attendance")
            ->groupBy('event_open_registrations.name', 'event_open_registrations.location', 'event_open_registrations.event_date', 'event_open_registrations.close_registration', 'event_open_registrations.uuid', 'event_open_registrations.is_paid_event');

        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') .'%';
        return $query->where('event_open_registrations.name', 'like', $search)
            ->orWhere('description', 'like', $search)
            ->orWhere('location', 'like', $search);
    }
}
