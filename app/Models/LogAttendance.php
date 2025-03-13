<?php

namespace App\Models;

use App\Support\StorageAttributes;
use App\Support\VerificationAttributes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAttendance extends Model
{
    use HasFactory;

    protected $table = 'log_attendances';

    const PHOTO = 'photo';
    const DIR_PHOTO = 'attendance/evidences';
    const DIR_THUMBNAIL = 'thumbnail/';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'summary_attendance_id',
        'umroh_trip_id',
        'category_id',
        'participant_id',
        'received_evidence',
        'total_bags',
        'total_cabin',
        'attendance_status'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'evidence_thumbnail'
    ];

    public function evidenceThumbnail(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['received_evidence'] ?? null,
                LogAttendance::DIR_THUMBNAIL
            ),
        );
    }
}
