<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAddress extends Model
{
    use HasFactory;

    protected $table = 'master_address';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'province',
        'city',
        'district',
        'subdistrict',
        'postalcode',
    ];
}
