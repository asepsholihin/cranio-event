<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterOfficeUser extends Model
{
    protected $table = 'master_office_user';

    protected $fillable = [
        'office_id',
        'user_id',
    ];
}
