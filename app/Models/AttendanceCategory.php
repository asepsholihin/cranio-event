<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceCategory extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = "master_attendance_categories";

    protected $fillable = [
        'name',
        'description',
        'status',
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
        return $query->where('name',  request()->query('q'))
            ->orWhere('name', 'like', $search);
    }
}
