<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class Notification extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'notifications';

    const DEPARTMENT_DIRECTORS = 1;
    const DEPARTMENT_MANAGEMENT = 2;
    const DEPARTMENT_SALES = 3;
    const DEPARTMENT_DOCUMENT = 4;
    const DEPARTMENT_EQUIPMENT = 5;
    const DEPARTMENT_HANDLING = 6;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'notification_type',
        'department_id',
        'order_umroh_trip_id',
        'message',
        'page_url',
        'created_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function scopeTableSearch($query)
    {
        $query->select([
            'notifications.*',
            DB::raw('(CASE WHEN notification_read_log.notification_id = notifications.id THEN true ELSE false END) AS is_read'),
            DB::raw('(CASE WHEN notifications.notification_type = 1 THEN \'New Booking Order\' ELSE \'\' END) AS notification_type_name'),
            'users.name'
        ]);
        $query->join('users', 'notifications.created_by', '=', 'users.id');
        $query->leftJoin('notification_read_log', 'notification_read_log.notification_id', '=', 'notifications.id');
        $query->whereNull('notification_read_log.id');

        return $query;
    }
}
