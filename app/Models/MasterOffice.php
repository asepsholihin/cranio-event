<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterOffice extends Model
{
    use SoftDeletes;
    protected $table = "master_office";

    protected $fillable = [
        'office_name',
        'office_phone',
        'office_address',
        'whatsapp_link',
        'show_footer',
        'created_by',
        'updated_by',
        'order',
        'latitude',
        'longitude',
        'deleted_by',
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
        return $query->where('office_name',  request()->query('q'))
            ->orWhere('office_phone', 'like', $search)
            ->orWhere('office_address', 'like', $search);
    }
}
