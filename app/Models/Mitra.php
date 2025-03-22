<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mitra extends Authenticatable
{
    use SoftDeletes;
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'mitra';

    const ACCESS_STATUS_ACTIVE = 1;
    const ACCESS_STATUS_DISABLED = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'participant_id',
        'user_id',
        'mitra_code',
        'status',
        'created_by',
        'updated_by',
        'join_date',
        'total_sales_pax',
        'total_sales_transaction',
        'total_fee',
        'total_fee_paid'
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

    protected $casts = [
        'join_date'  => 'date:d-m-Y',
    ];

    public function scopeTableSearch($query)
    {
        $query->join('participants', 'mitra.participant_id', 'participant.id');
        $query->select(['participant.name', 'mitra.id', 'participant.no_hp', 'participant.gender', 'participant.profile_photo_path', 'join_date', 'total_sales_pax', 'total_sales_transaction', 'total_fee', 'total_fee_paid']);
        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') .'%';
        return $query->where(function($q) use($search) {
            $q->where('email',  request()->query('q'))
            ->orWhere('name', 'like', $search)
            ->orWhere('no_hp', 'like', $search)
            ->orWhere('location', 'like', $search);
        });
    }
}
