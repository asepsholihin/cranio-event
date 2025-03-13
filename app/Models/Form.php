<?php

namespace App\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use SoftDeletes, Sluggable;
    
    protected $table = 'forms';

    const CCESSA_STATUS_ACTIVE = 1;
    const ACCESS_STATUS_DISABLED = 2;

    const IMAGE = 'image';
    const DIR_IMAGE = 'web/forms/';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'department_id',
        'title',
        'description',
        'image_url',
        'slug',
        'status',
        'respons',
        'required_umroh_trip',
        'created_by',
        'updated_by',
        'packages',
        'title_in_report',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function imageUrl(): Attribute
    {
        if (App::environment('production') && request()->is('api/public/**')) {
            return Attribute::make(
                get: fn ($value, $attributes) => 
                    $attributes['image_url'],
            );
        }

        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['image_url'] ?? null
            ),
        );
    }

    public function scopeTableSearch($query)
    {
        $query->select(['forms.*', 'users.name as created_by_name'])
        ->join('users', 'users.id', 'forms.created_by');

        if (!empty(request()->query('q', ''))) {
            $search = '%' . request()->query('q') .'%';
            $query->where('title', 'like', $search);
        }

        return $query;
    }
}
