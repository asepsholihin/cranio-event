<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FormQuestion extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'form_id',
        'question',
        'type',
        'required',
        'option_value',
        'created_by',
        'updated_by',
        'order',
        'has_other',
        'scale_top',
        'scale_bottom',
        'label_scale_top',
        'label_scale_bottom',
        'section_id',
        'view_in_report',
        'title_in_report',
        'model_in_report'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
    ];

    public function scopeTableSearch($query)
    {
        if (empty(request()->query('q', ''))) {
            return $query;
        }

        $search = '%' . request()->query('q') .'%';
        return $query->where('question', 'like', $search);
    }
}
