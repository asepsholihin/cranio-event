<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Carbon\Carbon;

class LogArticleActivity extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'log_article_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'article_id',
        'log_type',
        'message'
    ];

    protected $appends = [
        'name'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
    ];

    protected $casts = [
        'created_at'  => 'date:d-m-Y H:i',
    ];

    public function getNameAttribute()
    {
        $user = User::find($this->user_id);
        return  $user->name ?? '';
    }

    public function scopeTableSearch($query)
    {
        $search = '%' . request()->query('q') .'%';
        return $query->where('message',  request()->query('q'))
            ->orWhere('message', 'like', $search);
    }
}
