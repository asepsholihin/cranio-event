<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class City extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'master_cities';

    const ACCESS_STATUS_ACTIVE = 1;
    const ACCESS_STATUS_DISABLED = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
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
        'created_at',
        'updated_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by = auth()->user()->id;
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->id;
            }
        });

        static::updating(function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->id;
            }
        });
    }

    public function scopeTableSearch($query)
    {
        $search = '%' . request()->query('q') .'%';
        return $query->where('name',  request()->query('q'))
            ->orWhere('name', 'like', $search);
    }
}
