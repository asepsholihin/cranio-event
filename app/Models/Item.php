<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use App\Models\Category;
use App\Models\MasterUnit;

class Item extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'master_items';

    const ACCESS_STATUS_ACTIVE = 1;
    const ACCESS_STATUS_DISABLED = 2;

    const PREFIX_CODE = "JST";

    const PHOTO = 'image';
    const DIR_PHOTO = 'item';
    const DIR_THUMBNAIL = 'thumbnail/';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'category_id',
        'code',
        'name',
        'photo',
        'unit_id',
        'unit_price',
        'unit_price_riyal',
        'status',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
    ];
    
    protected $appends = [
        'category_name',
        'unit_name',
    ];

    public function photo(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['photo'] ?? null
            ),
        );
    }

    public function getCategoryNameAttribute()
    {
        $category = Category::where('id', $this->category_id)->first();
        return $category->name ?? null;
    }

    public function getUnitNameAttribute()
    {
        $unit = MasterUnit::where('id', $this->unit_id)->first();
        return $unit->name ?? null;
    }

    public function scopeTableSearch($query)
    {
        $search = '%' . request()->query('q') .'%';
        return $query->where('name',  request()->query('q'))
            ->orWhere('name', 'like', $search)
            ->orWhere('code', 'like', $search);
    }

    public static function generateCode()
    {
        $code = self::PREFIX_CODE . str_pad(self::getIdInThisYear(), 4, 0, STR_PAD_LEFT);
        return $code;
    }

    private static function getIdInThisYear()
    {
        $recordNumber = self::whereYear('created_at', date('Y'))->count();

        return $recordNumber + 1;
    }
}
