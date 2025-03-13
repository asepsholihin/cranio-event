<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentUser extends Model
{
    protected $table = 'department_user';
    
    protected $fillable = [
        'department_id',
        'user_id',
    ];
}
