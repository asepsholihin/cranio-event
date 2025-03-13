<?php

namespace App\Models;

use App\Support\StorageAttributes;
use App\Support\VerificationAttributes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SummaryAttendance extends Model
{
    use HasFactory;

    protected $table = 'summary_attendances';

    const PHOTO = 'photo';
    const DIR_PHOTO = 'attendance/evidences';
    const DIR_THUMBNAIL = 'thumbnail/';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'umroh_trip_id',
        'category_id',
        'attendance_name',
        'evidence',
        'notes',
        'total_attendance',
        'status'
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
                $attributes['evidence'] ?? null,
                SummaryAttendance::DIR_THUMBNAIL
            ),
        );
    }
}
