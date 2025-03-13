<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebRedirectUrl extends Model
{
    use HasFactory;

    protected $table = 'web_redirect_urls';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'redirect_type',
        'old_url',
        'new_url',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_by',
        'updated_by',
        'updated_at',
    ];

    protected $casts = [
        'created_at'  => 'date:d-m-Y',
    ];
    
    public function scopeTableSearch($query)
    {
        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') . '%';
        return $query->where('old_url', 'like', $search);
    }
}
