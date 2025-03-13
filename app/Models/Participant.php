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

    protected $table = 'participant';

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
        'no_hp',
        'nik',
        'no_passport',
        'birth_place',
        'birth_date',
        'email_verified_at',
        'gender',
        'profile_photo_path',
        'created_from',
        'title',
        'front_title',
        'back_title',
        'fathers_name',
        'married_status',
        'nationality',
        'instagram',
        'ktp_province',
        'ktp_city',
        'ktp_kecamatan',
        'ktp_kelurahan',
        'ktp_address',
        'ktp_postalcode',
        'home_province',
        'home_city',
        'home_kecamatan',
        'home_kelurahan',
        'home_address',
        'home_postalcode',
        'education',
        'is_doctor',
        'doctor_specialist',
        'doctor_evidence',
        'job',
        'company_name',
        'blood_type',
        'medical_record',
        'emergency_contact',
        'emergency_contact_name',
        'emergency_relation',
        'emergency_address',
        'have_passport',
        'name_in_passport',
        'passport_issued_at',
        'passport_published_date',
        'passport_expired_date',
        'passport_held_by',
        'suggest_booking_order',
        'suggest_package',
        'suggest_room',
        'refer_by',
        'body_size',
        'infants',
        'kitas_number',
        'full_name_vaccine',
        'participant_status',
        'wedding_book_required',
        'chest_size',
        'body_height',
        'name_in_certificate',
        'remind_milad',
        'access_status',
        'linkedin_url',
        'barcode_thumbnail',
        'created_by',
        'created_by',
        'jacket_size',
        'name_in_sandal_bag',
        'milad_card_url',
        'is_nakes',
        'is_tni_polri',
        'medical_description'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'pin',
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

        static::created(function (Participant $item) {
            $item->generateJiCode();
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
        'age'
    ];

    public function barcodeThumbnail(): Attribute
    {
        // if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    "https://www.jejakimani.com/".$attributes['barcode_thumbnail'],
            );
        // }

        // return Attribute::make(
        //     get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
        //         $attributes['barcode_thumbnail'] ?? null
        //     ),
        // );
    }

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

    public function miladCardUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => 
                "https://www.jejakimani.com/".$attributes['milad_card_url'],
        );
    }

    public function getAgeAttribute()
    {
        return $this->birth_date ? \Carbon\Carbon::createFromFormat('Y-m-d', $this->birth_date)->age : '';
    }

    public function scopeTableSearch($query)
    {
        $query->select('participant.*');
        if(in_array(3, auth()->user()->department_ids)) {
            $query->where('participant.created_by', auth()->user()->id);
        }

        if (!empty(request()->query('gender'))) {
            $query->where('participant.gender', request()->query('gender'));
        }

        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') . '%';
        return $query->where(function ($query) use ($search) {
            $query
                ->where('participant.email',  request()->query('q'))
                ->orWhere('participant.name', 'like', $search)
                ->orWhere('participant.no_hp', 'like', $search)
                ->orWhere('participant.nik', 'like', $search)
                ->orWhere('participant.no_passport', 'like', $search);
        });
    }

    public function generateJiCode()
    {
        $jiCode = self::PREFIX_JI_CODE . date('y') . str_pad($this->getIdInThisMonth(), 5, 0, STR_PAD_LEFT);
        $this->ji_code = $jiCode;
        $this->save();
    }

    private function getIdInThisMonth()
    {
        $recordNumber = self::whereYear('created_at', date('Y'))
            ->withTrashed()
            ->count();

        return $recordNumber + 1;
    }

    public static function createJiCodeForParticipant()
    {
        $participant = Participant::whereNull('ji_code')->whereNull('ji_code')->orderBy('created_at', 'asc')->withTrashed()->get();
        $year = null;
        foreach ($participant as $key => $value) {
            $dbCount = SELF::where('ji_code', 'LIKE', '%' . self::PREFIX_JI_CODE . Carbon::parse($value->created_at)->format('y') . '%')->withTrashed()->count();
            $number = (int) $dbCount + 1;
            $jiCode = self::PREFIX_JI_CODE . Carbon::parse($value->created_at)->format('y') . str_pad($number, 5, 0, STR_PAD_LEFT);
            $value->ji_code = $jiCode;
            $value->save();
        }
    }

    public function activationReminderMilad()
    {
        if($this->remind_milad == 1) {
            $this->remind_milad = 2;
        } else {
            $this->remind_milad = 1;
        }
        $this->save();
    }

    public static function getLastTrip($id)
    {
        $last_trip = '';
        $orderUmrohTrip = ParticipantUmrohTrip::where('participant_id', $id)->orderBy('created_at', 'desc')->first();
        if($orderUmrohTrip) {
            $last_trip = UmrohTrip::find($orderUmrohTrip->umroh_trip_id)->title ?? '';
        }
        return $last_trip;
    }

    public function scopeTableRawSearch($query)
    {
        $query->select('participant.*');
        if(in_array(3, auth()->user()->department_ids)) {
            $query->where('participant.created_by', auth()->user()->id);
        }
        if (!empty(request()->query('gender'))) {
            $query->where('participant.gender', request()->query('gender'));
        }
        if (!empty(request()->query('duplicate'))) {
            $query->join(
                \DB::raw('(SELECT name, nik FROM participant GROUP BY name, nik HAVING COUNT(*) > 1) AS redundant'),
                'redundant.nik', '=', 'participant.nik'
            );
            $query->orderByRaw('participant.name ASC, participant.nik ASC, participant.id ASC');
        }
        if (!empty(request()->query('q', ''))) {
            $search = '%' . request()->query('q') . '%';
            $query->where(function ($query) use ($search) {
                $query
                    ->where('participant.email',  request()->query('q'))
                    ->orWhere('participant.name', 'like', $search)
                    ->orWhere('participant.no_hp', 'like', $search)
                    ->orWhere('participant.nik', 'like', $search)
                    ->orWhere('participant.no_passport', 'like', $search);
            });
        }

        return $query;
    }
}
