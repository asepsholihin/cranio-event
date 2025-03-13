<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inquiry extends Authenticatable
{
    use SoftDeletes;
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'web_inquiries';

    const ACCESS_STATUS_ACTIVE = 1;
    const ACCESS_STATUS_DISABLED = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'lang_id',
        'full_name',
        'wa_number',
        'email',
        'message',
        'from_page'
    ];

    protected $casts = [
        'created_at'  => 'date:d-m-Y',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
    ];

    public function scopeTableSearch($query)
    {   
        if (! empty(request()->query('startDate'))) {
            $startDate = date('Y-m-d 00:00:00', strtotime(request()->query('startDate')));
            $query->where('created_at', '>=', $startDate);
        }
        
        if (!empty(request()->query('fromPage'))) {
            $query->where('from_page', request()->query('fromPage'));
        }
        
        if (! empty(request()->query('endDate'))) {
            $endDate = date('Y-m-d 23:59:59', strtotime(request()->query('endDate')));
            $query->where('created_at', '<=', $endDate);
        }
        
        if (empty(request()->query('q', ''))) {
            return $query;
        }
        
        $search = '%' . request()->query('q') .'%';
        return $query->where('full_name',  request()->query('q'))
            ->orWhere('wa_number', 'like', $search)
            ->orWhere('from_page', 'like', $search)
            ->orWhere('email', 'like', $search);
    }
}
