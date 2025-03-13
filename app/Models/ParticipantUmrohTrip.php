<?php

namespace App\Models;

use App\Support\StorageAttributes;
use App\Support\VerificationAttributes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ParticipantUmrohTrip extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    const ROLE_TYPE_JAMAAH = 1;
    const ROLE_TYPE_LEADER = 2;
    const ROLE_TYPE_MUTAWWIF = 3;
    const ROLE_TYPE_FOC = 4;
    const ROLE_TYPE_RUNNER = 5;
    const TICKET_TYPE_ECONOMY_CLASS = 1;
    const TICKET_TYPE_BUSSINESS_CLASS = 2;
    const INFANTS_YES = 1;
    const INFANTS_NO = 2;
    const WIHTOUT_BED_YES = 1;
    const WIHTOUT_BED_NO = 2;
    const SISKOPATUH_URI = "https://siskopatuh.kemenag.go.id/web/npu/?id=";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'umroh_trip_id',
        'participant_id',
        'package_umroh_trip_id',
        'room_type',
        'group_bus',
        'group_bus_type',
        'group_hotel_room',
        'file_ktp_verified',
        'file_photo_verified',
        'file_kk_verified',
        'file_passport_verified',
        'file_akta_verified',
        'file_buku_nikah_verified',
        'file_buku_kuning_verified',
        'file_kartu_vaksin_verified',
        'file_bpjs_verified',
        'file_mcu_verified',
        'file_surat_keterangan_verified',
        'verification_complete',
        'passport_received_at',
        'buku_kuning_received_at',
        'room_group_notes',
        'code_siskopatuh',
        'booking_order_no',
        'order_item_umroh_trip_id',
        'order_umroh_trip_id',
        'infants',
        'ticket_type',
        'without_ticket',
        'waiting_list',
        'siskopatuh_id',
        'ticket_number',
        'insurance_number',
        'visa_number',
        'who_certificate',
        'manasik_table',
        'departure_seat',
        'return_seat',
        'prev_departure_seat',
        'prev_return_seat',
        'seat_updated_by',
        'seat_updated_at',
        'no_urut',
        'role_type',
        'receive_passport_evidence',
        'price_per_pax',
        'discount',
        'price_after_discount',
        'without_bed',
        'created_by',
        'updated_by',
        'hotel_makkah',
        'hotel_madinah',
        'is_mahrom',
        'relation',
        'is_fit',
        'bed_type',
        'maskapai_notes',
        'room_no_hotel_mekkah',
        'room_no_hotel_madinah',
        'room_notes',
        'hotel_assigned',
        'nomor_porsi',
        'nomor_spph',
        'passport_notes',
        'buku_kuning_notes',
        'pnr_code',
        'need_assistance',
        'is_reference'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_thumbnail',
        'passport_expires',
        'age',
        'request_participant',
        'hotel_makkah_selected',
        'hotel_madinah_selected',
        'document_signed'
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
                    $model->updated_by = auth()->user()->id;
                }
            }
        });
    }

    public function getRequestParticipantAttribute()
    {
        $requestParticipant = [];
        if($this->id) {
            $requestParticipant = OrderSpecialRequest::where('assigned_participant', 'like', '%' . $this->id . '%')->get();
        }
        return $requestParticipant;
    }

    public function getAgeAttribute()
    {
        return $this->birth_date ? \Carbon\Carbon::createFromFormat('Y-m-d', $this->birth_date)->age : '';
    }

    public function passportExpires(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => VerificationAttributes::getPassportExpires($attributes),
        );
    }

    public function profileThumbnail(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['profile_photo_path'] ?? null,
                Participant::DIR_THUMBNAIL
            ),
        );
    }

    public function receivePassportEvidence(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['receive_passport_evidence'] ?? null,
                ParticipantFile::DIR_THUMBNAIL
            ),
        );
    }

    public function getHotelMakkahSelectedAttribute()
    {
        $roomUmrohTrip = DB::table('room_umroh_trips')->where('participant_umroh_trip_id', $this->id)
        ->where('city', 'like', '%makkah%')
        ->first();
        if($roomUmrohTrip) {
            return $roomUmrohTrip->hotel_name;
        }

        $participant = DB::table('participant_umroh_trips')->select('hotel_makkah','package_umroh_trip_id')->where('id', $this->id)->first();
        $hotel_makkah = "";
        if($participant) {
            if($participant->hotel_makkah) {
                $hotel_makkah = $participant->hotel_makkah;
            } else {
                $package = DB::table('package_umroh_trips')->select('hotel_makkah')->where('id', $participant->package_umroh_trip_id)->first();
                if($package)
                    $hotel_makkah = $package->hotel_makkah;
            }
        }
        
        return $hotel_makkah;
    }

    public function getHotelMadinahSelectedAttribute()
    {
        $roomUmrohTrip = DB::table('room_umroh_trips')->where('participant_umroh_trip_id', $this->id)
        ->where('city', 'like', '%madinah%')
        ->first();
        if($roomUmrohTrip) {
            return $roomUmrohTrip->hotel_name;
        }

        $participant = DB::table('participant_umroh_trips')->select('hotel_madinah','package_umroh_trip_id')->where('id', $this->id)->first();
        $hotel_madinah = "";
        if($participant) {
            if($participant->hotel_madinah) {
                $hotel_madinah = $participant->hotel_madinah;
            } else {
                $package = DB::table('package_umroh_trips')->select('hotel_madinah')->where('id', $participant->package_umroh_trip_id)->first();
                if($package)
                    $hotel_madinah = $package->hotel_madinah;
            }
        }
        
        return $hotel_madinah;
    }

    public function getDocumentSignedAttribute()
    {
        $documents = array();
        $logLetters = LogLetter::select('id','letter_type','letter_number','sign_evidence','slug')->where('participant_umroh_trip_id', $this->id)->whereNotNull('sign_evidence')->get();
        foreach ($logLetters as $letter) {
            $letter['letter_name'] = Str::upper(Str::replace('_', ' ', $letter->letter_type));
            $documents[] = $letter;
        }
        return $documents;
    }

    public function scopeTableSearch($query)
    {
        $query
            ->join('participant', 'participant.id', '=', 'participant_umroh_trips.participant_id')
            ->join('package_umroh_trips', 'package_umroh_trips.id', '=', 'participant_umroh_trips.package_umroh_trip_id')
            ->join('umroh_trips', 'umroh_trips.id', '=', 'participant_umroh_trips.umroh_trip_id')
            ->where('participant_umroh_trips.umroh_trip_id', request()->query('umrohTripId', 0))
            ->select(
                'participant_umroh_trips.*',
                'package_umroh_trips.name as packageName',
                'participant.name',
                'umroh_trips.departure_at',
                'participant.no_hp',
                'participant.birth_date',
                'participant.passport_expired_date',
                'participant.gender',
                'participant.profile_photo_path',
                'participant.title',
                'participant.front_title',
                'participant.back_title',
                'participant.name_in_certificate',
                'participant.no_passport',
                'participant.barcode',
                'participant.full_name_vaccine',
                'participant.name_in_passport',
                'participant.medical_record',
                DB::raw(
                    '
            (CASE 
                WHEN participant_umroh_trips.role_type = 2 THEN \'Tour Leader\'
                WHEN participant_umroh_trips.role_type = 3 THEN \'Mutawwif\'
                WHEN participant_umroh_trips.role_type = 4 THEN \'Free Of Charge \'
                WHEN participant_umroh_trips.role_type = 5 THEN \'Runner \'
                ELSE \'\' END) AS crew'
                ),
                DB::raw(
                    '
            (CASE 
                WHEN participant_umroh_trips.role_type = 2 THEN \'a\' 
                WHEN participant_umroh_trips.role_type = 3 THEN \'b\' 
                ELSE \'z\' END) AS tour_crew'
                ),
                DB::raw(
                    '
            (CASE 
                WHEN ticket_type = 2 THEN \'a\' ELSE \'z\' END) AS bussiness_class'
                ),
                DB::raw('(CASE 
                WHEN package_umroh_trips.name iLIKE \'%Rub%\' THEN \'c\' 
                WHEN package_umroh_trips.name iLIKE \'%Emerald%\' THEN \'b\' 
                WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'a\' 
                ELSE \'c\' END
            ) AS package_type'),
                DB::raw('(CASE WHEN participant_umroh_trips.waiting_list != 1 THEN \'a\' ELSE \'z\' END) AS participant_waiting_list')
            );

        $query->orderByRaw('package_type ASC, tour_crew, no_urut ASC NULLS LAST, group_bus ASC NULLS FIRST');

        if (!empty(request()->query('roomType'))) {
            $query->where('room_type', request()->query('roomType'));
        }

        if (!empty(request()->query('package'))) {
            $query->where('package_umroh_trip_id', request()->query('package'));
        }

        if (!empty(request()->query('verification'))) {
            $query->where('verification_complete', request()->query('verification'));
        }

        if (!empty(request()->query('gender'))) {
            $query->where('participant.gender', request()->query('gender'));
        }

        if (request()->query('roomGroup') == 'None') {
            $query->whereNull('group_hotel_room');
        } else if (!empty(request()->query('roomGroup'))) {
            $query->where('group_hotel_room', request()->query('roomGroup'));
        }

        if (request()->query('busGroup') == 'None') {
            $query->whereNull('group_bus');
        } else if (!empty(request()->query('busGroup'))) {
            $query->where('group_bus', request()->query('busGroup'));
        }

        if (!empty(request()->query('booking'))) {
            $query->where('participant_umroh_trips.order_umroh_trip_id', request()->query('booking'));
        }

        if (!empty(request()->query('medicalRecord'))) {
            $query->where('participant.medical_record', request()->query('medicalRecord'));
        }

        if(in_array(8, auth()->user()->department_ids)) {
            // Kepala Cabang
            $query->join('users', 'users.id', 'participant.created_by')->join('master_office_user', 'master_office_user.user_id', 'users.id');
            $query->whereIn('master_office_user.office_id', auth()->user()->office_ids);
        }
        if(in_array(3, auth()->user()->department_ids)) {
            $query->where('participant_umroh_trips.created_by', auth()->user()->id);
        }

        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') . '%';
        return $query->where(function($q) use($search){
            $q->where('participant.name', 'like', $search)
            ->orWhere('participant.name_in_passport', 'like', $search)
            ->orWhere('participant.no_hp', 'like', $search)
            ->orWhere('participant_umroh_trips.booking_order_no', 'like', $search)
            ->orWhere('participant.no_passport', 'like', $search);
        });
    }

    public static function canGroupingRoom()
    {
        if (request()->get('roomHotelGroup') == 'None') {
            return;
        }

        $package = null;
        $roomType = null;
        $umrohTripId = null;
        $rooms = self::whereIn('id', request()->get('ids'))->get(['room_type', 'package_umroh_trip_id']);
        foreach ($rooms as $room) {
            if ($package == null) {
                $roomType = $room->room_type;
                $packageId = $room->package_umroh_trip_id;
                $package = PackageUmrohTrip::where('id', $room->package_umroh_trip_id)->select('name', 'umroh_trip_id')->first();
                $umrohTripId = $package->umroh_trip_id;
                $package = explode(' ', $package->name)[0];
            } else {
                $packageName = PackageUmrohTrip::where('id', $room->package_umroh_trip_id)->value('name');
                if (str_contains($packageName, $package) == FALSE || $roomType != $room->room_type) {
                    throw ValidationException::withMessages([
                        'roomHotelGroup' => ['Invalid Package or Room Type are not same'],
                    ]);
                }
            }
        }

        $roomCount = self::leftjoin('package_umroh_trips','package_umroh_trips.id','participant_umroh_trips.package_umroh_trip_id')
            ->where('package_umroh_trips.name', $package)
            ->where('room_type', $roomType)
            ->where('participant_umroh_trips.umroh_trip_id', $umrohTripId)
            ->where('group_hotel_room', request()->get('roomHotelGroup'))
            ->count();
    
        $roomCount += count(request()->get('ids'));

        if (request()->get('additionalBedRoom')) {
            return;
        }

        if ($roomType == 'double' && $roomCount > 2) {
            throw ValidationException::withMessages([
                'roomHotelGroup' => ['Room Type Double Cannot more than 2 people'],
            ]);
        }

        if ($roomType == 'triple' && $roomCount > 3) {
            throw ValidationException::withMessages([
                'roomHotelGroup' => ['Room Type Triple Cannot more than 3 people'],
            ]);
        }

        if ($roomType == 'quad' && $roomCount > 4) {
            throw ValidationException::withMessages([
                'roomHotelGroup' => ['Room Type Quad Cannot more than 4 people'],
            ]);
        }
    }

    
    public static function getParticipantUpgradeHotel($umrohTripId)
    {
        // Check upgrade hotel
        $participantUpgradeHotel = array();
        $participantUmrohTrips = DB::table('participant_umroh_trips')
        ->select('participant_umroh_trips.id','participant_umroh_trips.umroh_trip_id','participant_umroh_trips.package_umroh_trip_id','package_umroh_trips.name as package_name')
        ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
        ->join('participant', 'participant.id', 'participant_umroh_trips.participant_id')
        ->where('participant_umroh_trips.umroh_trip_id', $umrohTripId)->get();
        foreach ($participantUmrohTrips as $value) {
            $list_hotels = UmrohTrip::getHotels($value->umroh_trip_id, $value->package_umroh_trip_id);
            $hotel_shouldbe = [];
            foreach ($list_hotels as $hotel) {
                $hotel_shouldbe[] = $hotel['hotel_name'];
            }
            $hotel_actual = DB::table('room_umroh_trips')->select('hotel_name')
            ->where('participant_umroh_trip_id', $value->id)->get()->pluck('hotel_name')->toArray();

            if(array_diff($hotel_actual,$hotel_shouldbe)) {
                $list_all_hotels = UmrohTrip::getHotels($value->umroh_trip_id);
                $value->hotel_shouldbe = $hotel_shouldbe;
                $value->hotel_actual = $hotel_actual;
                $hotel_upgrade = array_diff($hotel_actual,$hotel_shouldbe);
                $hotel_same = array_intersect($hotel_actual,$hotel_shouldbe);
                foreach ($list_all_hotels as $item_hotel) {
                    if(in_array($item_hotel['hotel_name'], $hotel_upgrade)) {
                        $value->hotel_upgrade = $hotel_upgrade;
                        $value->hotel_info[] = $item_hotel;
                        $value->note_upgrade = ucwords(strtolower($value->package_name . " Upgrade " . $item_hotel['city_name']));
                    }
                    if(in_array($item_hotel['hotel_name'], $hotel_same)) {
                        $value->hotel_info[] = $item_hotel;
                    }
                }
                
                $participantUpgradeHotel[] = $value;
            }
        }

        return $participantUpgradeHotel;
    }
}
