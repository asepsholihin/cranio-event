<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebLinkText extends Authenticatable
{
    use SoftDeletes;
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'web_link_texts';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'lang_id',
        'page_id',
        'page_name',
        'whatsapp_text',
        'google_tag',
        'status',
        'google_tag_event',
        'google_tag_event_category',
        'google_tag_event_label'
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
        return $query->where('page_name',  request()->query('q'))
            ->orWhere('page_name', 'like', $search)
            ->orWhere('whatsapp_text', 'like', $search)
            ->orWhere('google_tag_event_category', 'like', $search)
            ->orWhere('google_tag_event_label', 'like', $search)
            ->orWhere('google_tag_event', 'like', $search);
    }
}
