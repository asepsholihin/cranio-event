<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rennokki\QueryCache\Traits\QueryCacheable;
use OwenIt\Auditing\Contracts\Auditable;

class WebSale extends Authenticatable implements Auditable
{
    use SoftDeletes;
    use HasApiTokens, HasFactory, Notifiable, QueryCacheable;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'web_sales';

    protected static $flushCacheOnUpdate = true;
    public $cacheFor = 3600;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'sales_name',
        'order_number',
        'whatsapp_number',
        'whatsapp_api',
        'total_visit',
        'status',
        'show_in_footer',
        'whatsapp_service'
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
        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') .'%';
        return $query->where('sales_name', 'like', $search)
            ->orWhere('order_number', 'like', $search)
            ->orWhere('whatsapp_number', 'like', $search);
    }
}
