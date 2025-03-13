<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormAnswer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'umroh_trip_id',
        'session_id',
        'name',
        'no_hp',
        'question_id',
        'answer',
        'section_id',
        'section_option'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
    ];
}
