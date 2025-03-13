<?php

namespace App\Actions\ParticipantApp\Auth;

use App\Models\Participant;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginViaEmail
{
    const MAX_LOGIN_ATTEMPT = 5;
    const ALLOW_MAX_LOGIN_ATTEMPT_IN = 300; # 5 minutes

    public static function attempt(Request $request)
    {
        $keyAttempt = "app-login:{$request->email}";
        if (RateLimiter::tooManyAttempts($keyAttempt, self::MAX_LOGIN_ATTEMPT)) {
            throw new ThrottleRequestsException(__('auth.throttle'));
        }

        $participant = Participant::where('email', $request->email)->first();

        if (! $participant || ! Hash::check($request->password, $participant->password)) {
            RateLimiter::hit($keyAttempt, self::ALLOW_MAX_LOGIN_ATTEMPT_IN);
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if ($participant->access_status != Participant::ACCESS_STATUS_ACTIVE) {
            RateLimiter::hit($keyAttempt, self::ALLOW_MAX_LOGIN_ATTEMPT_IN);
            throw ValidationException::withMessages([
                'email' => [__('auth.disabled')],
            ]);
        }

        return $participant;
    }
}
