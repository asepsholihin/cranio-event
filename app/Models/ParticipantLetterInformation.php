<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantLetterInformation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'participant_id',
        'city',
        'province',
        'destination_imigration',
        'address_imigration',
        'destination_permission',
        'address_permission',
        'destination_kemenag',
        'address_kemenag',
        'destination_hospital',
        'address_hospital',
    ];
}
