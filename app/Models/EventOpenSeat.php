<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Support\Facades\App;

class EventOpenSeat extends Model
{
    const S3_PATH_BARCODE = 'web/OpenRegistration/Barcode';

    protected $fillable = [
        'event_id',
        'parent_account_id',
        'seat_name',
        'seat_number',
        'gender',
        'barcode',
        'barcode_thumbnail',
        'check_in_at'
    ];

    public function barcodeThumbnail(): Attribute
    {
        // if (App::environment('production') && request()->is('api/public/**')) {
        //     return Attribute::make(
        //         get: fn ($value, $attributes) => 
        //             $attributes['barcode_thumbnail'],
        //     );
        // }

        // return Attribute::make(
        //     get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
        //         $attributes['barcode_thumbnail'] ?? null
        //     ),
        // );

        return Attribute::make(
            get: fn ($value, $attributes) => 
                "https://www.jejakimani.com/".$attributes['barcode_thumbnail'],
        );
    }
}
