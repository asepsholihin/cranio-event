<?php

namespace App\Models;

use App\Http\Controllers\CrewApp\JiosController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\App;

class Participant extends Authenticatable implements Auditable
{
    use SoftDeletes;
    use HasApiTokens, HasFactory, Notifiable;
    use \OwenIt\Auditing\Auditable;

    const ACCESS_STATUS_ACTIVE = 1;
    const ACCESS_STATUS_DISABLED = 2;

    const CREATED_FROM_SPA = 1;
    const CREATED_FROM_MOBILE = 2;

    const PHOTO = 'photo';
    const DIR_PHOTO = 'participant/profiles';
    const DIR_THUMBNAIL = 'thumbnail/';
    const PREFIX_PHONE_NUMBER = '62';
    const PREFIX_JI_CODE = 'JI';

    const DIR_BARCODE = 'web/barcodes';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'whatsapp',
        'nik',
        'no_passport',
        'birth_place',
        'birth_date',
        'email_verified_at',
        'gender',
        'profile_photo_path',
        'polo_size',
        'name_in_certificate',
        'request',
        'created_by',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_from',
        'email_verified_at',
        'verification_email_code',
        'verification_email_expired_at',
        'verification_sms_code',
        'verification_sms_expired_at',
        'sms_verified_at',
        'created_at',
        'updated_at',
        'created_by',
        'created_by'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if(auth()->user()) {
                if (!$model->isDirty('created_by')) {
                    $model->created_by = auth()->user()->id;
                }
                if (!$model->isDirty('updated_by')) {
                    $model->updated_by = auth()->user()->id;
                }
            }
        });

        static::updating(function ($model) {
            if(auth()->user()) {
                if (!$model->isDirty('updated_by')) {
                    if(auth()->user())
                        $model->updated_by = auth()->user()->id;
                }
            }
        });
    }

    public function files()
    {
        return $this->hasMany(ParticipantFile::class);
    }

    public static function checkFile($participantId, $fileTitle)
    {
        $checkFile = ParticipantFile::where('participant_id', $participantId)->where('title', $fileTitle)->exists();
        return ($checkFile) ? true : false;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_thumbnail',
        'profile_photo',
    ];

    public function profilePhoto(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['profile_photo_path'] ?? null
            ),
        );
    }

    public function profileThumbnail(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['profile_photo_path'] ?? null,
                self::DIR_THUMBNAIL
            ),
        );
    }

    public function scopeTableSearch($query)
    {
        $query->select(['participants.*',
        DB::raw("(SELECT booking_no FROM bookings JOIN participant_bookings ON bookings.id = participant_bookings.booking_id WHERE participant_bookings.participant_id = participants.id ORDER BY bookings.id DESC) as booking_no"),
        DB::raw("(SELECT account_name FROM bookings JOIN participant_bookings ON bookings.id = participant_bookings.booking_id WHERE participant_bookings.participant_id = participants.id ORDER BY bookings.id DESC) as booking_account_name"),
        DB::raw("(SELECT account_hospital FROM bookings JOIN participant_bookings ON bookings.id = participant_bookings.booking_id WHERE participant_bookings.participant_id = participants.id ORDER BY bookings.id DESC) as booking_account_hospital"),
        DB::raw("(SELECT order_status FROM bookings JOIN participant_bookings ON bookings.id = participant_bookings.booking_id WHERE participant_bookings.participant_id = participants.id ORDER BY bookings.id DESC) as booking_order_status")
        ]);
        if(in_array(3, auth()->user()->department_ids)) {
            // $query->where('participants.created_by', auth()->user()->id);
        }
        if (!empty(request()->query('gender'))) {
            $query->where('participants.gender', request()->query('gender'));
        }
        if (!empty(request()->query('poloSize'))) {
            $query->where('participants.polo_size', request()->query('poloSize'));
        }
        if (!empty(request()->query('date'))) {
            $dateXplode = explode('to', request()->query('date'));
            $start = date('Y-m-d', strtotime($dateXplode[0]));
            $end = date('Y-m-d', strtotime($dateXplode[1]??$dateXplode[0]));
            $query->whereBetween('participants.created_at', [$start, $end]);
        }
        if (!empty(request()->query('q'))) {
            $search = '%' . request()->query('q') . '%';
            $query->where(function ($q) use ($search) {
                $q
                    ->where('participants.email',  request()->query('q'))
                    ->orWhere('participants.name', 'like', $search)
                    ->orWhere('participants.whatsapp', 'like', $search)
                    ->orWhere('participants.nik', 'like', $search);
            });
        }
        return $query;
    }

    private function getIdInThisMonth()
    {
        $recordNumber = self::whereYear('created_at', date('Y'))
            ->withTrashed()
            ->count();

        return $recordNumber + 1;
    }

    public function scopeTableRawSearch($query)
    {
        $query->select('participants.*');
        if(in_array(3, auth()->user()->department_ids)) {
            $query->where('participants.created_by', auth()->user()->id);
        }
        if (!empty(request()->query('gender'))) {
            $query->where('participants.gender', request()->query('gender'));
        }
        if (!empty(request()->query('duplicate'))) {
            $query->join(
                \DB::raw('(SELECT name, nik FROM participant GROUP BY name, nik HAVING COUNT(*) > 1) AS redundant'),
                'redundant.nik', '=', 'participants.nik'
            );
            $query->orderByRaw('participants.name ASC, participants.nik ASC, participants.id ASC');
        }
        if (!empty(request()->query('q', ''))) {
            $search = '%' . request()->query('q') . '%';
            $query->where(function ($query) use ($search) {
                $query
                    ->where('participants.email',  request()->query('q'))
                    ->orWhere('participants.name', 'like', $search)
                    ->orWhere('participants.no_hp', 'like', $search)
                    ->orWhere('participants.nik', 'like', $search)
                    ->orWhere('participants.no_passport', 'like', $search);
            });
        }

        return $query;
    }
}
