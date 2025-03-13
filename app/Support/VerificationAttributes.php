<?php

namespace App\Support;

use Carbon\Carbon;

class VerificationAttributes
{
    const MONTHS_PASSPORT_EXPIRED = 9;

    public static function getPassportExpires($attributes)
    {
        if (! isset($attributes['passport_expired_date']) || ! isset($attributes['departure_at'])) {
            return null;
        }

        $departure = new Carbon($attributes['departure_at']);
        $expiredIn = $departure->diffInMonths($attributes['passport_expired_date']);

        return ($expiredIn >= self::MONTHS_PASSPORT_EXPIRED);
    }

}
