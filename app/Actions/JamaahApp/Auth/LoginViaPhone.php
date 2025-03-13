<?php

namespace App\Actions\ParticipantApp\Auth;

use App\Models\Participant;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class LoginViaPhone
{
    const MAX_LOGIN_ATTEMPT = 5;
    const ALLOW_MAX_LOGIN_ATTEMPT_IN = 300; # 5 minutes

    public static function attempt(Request $request)
    {
        $keyAttempt = "app-login:{$request->no_hp}";
        if (RateLimiter::tooManyAttempts($keyAttempt, self::MAX_LOGIN_ATTEMPT)) {
            throw new ThrottleRequestsException(__('auth.throttle'));
        }

        $participant = Participant::where('no_hp', $request->no_hp)->first();

        if (! $participant || ! Hash::check($request->pin, $participant->pin)) {
            RateLimiter::hit($keyAttempt, self::ALLOW_MAX_LOGIN_ATTEMPT_IN);
            throw ValidationException::withMessages([
                'no_hp' => [__('auth.failed')],
            ]);
        }

        if ($participant->access_status != Participant::ACCESS_STATUS_ACTIVE) {
            RateLimiter::hit($keyAttempt, self::ALLOW_MAX_LOGIN_ATTEMPT_IN);
            throw ValidationException::withMessages([
                'no_hp' => [__('auth.disabled')],
            ]);
        }

        return $participant;
    }
}
