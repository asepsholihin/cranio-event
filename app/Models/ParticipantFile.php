<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\StorageAttributes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use OwenIt\Auditing\Contracts\Auditable;

class ParticipantFile extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    const FILE = 'file';
    const DIR_FILE = 'participant/files';
    const DIR_THUMBNAIL = 'thumbnail/';

    protected $fillable = [
        'participant_id',
        'title',
        'file_type',
        'file_path',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'file_path_url',
    ];

    public function filePathUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['file_path'] ?? null
            ),
        );
    }
}
