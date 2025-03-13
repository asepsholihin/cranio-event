<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSection extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'options',
        'created_by',
        'updated_by',
    ];

    public function scopeTableSearch($query)
    {
        $query->select(['form_sections.*', 'users.name as created_by_name'])
        ->join('users', 'users.id', 'form_sections.created_by');

        if (!empty(request()->query('q', ''))) {
            $search = '%' . request()->query('q') .'%';
            $query->where('name', 'like', $search);
        }

        return $query;
    }
}
