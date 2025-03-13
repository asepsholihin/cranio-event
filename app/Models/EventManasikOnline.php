<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Support\StorageAttributes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class EventManasikOnline extends Model
{
    use SoftDeletes;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'event_timeline_id',
        'event_id',
        'description',
        'slug',
        'link_url',
        'date',
        'time_start',
        'time_end',
        'status',
        'total_pax',
        'attend_pax',
        'not_attend_pax',
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
        return $query;
    }

    public static function generateConfirmationLink($id)
    {
        $event = self::find($id);
        if($event->slug == null) {
            $slug = Str::slug(strtolower($event->description));
            $existSlug = self::where('slug', $slug)->where('id', '<>', $id)->count();
            if ($existSlug) {
                $slug = $slug . $existSlug;
            }
            $event->slug = $slug;
            $event->save();
        }
    }
}
