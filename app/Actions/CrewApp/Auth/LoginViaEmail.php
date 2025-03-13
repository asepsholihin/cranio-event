<?php

namespace App\Actions\CrewApp\Auth;

use App\Models\User;
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

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($keyAttempt, self::ALLOW_MAX_LOGIN_ATTEMPT_IN);
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if ($user->access_status != User::ACCESS_STATUS_ACTIVE) {
            RateLimiter::hit($keyAttempt, self::ALLOW_MAX_LOGIN_ATTEMPT_IN);
            throw ValidationException::withMessages([
                'email' => [__('auth.disabled')],
            ]);
        }

        return $user;
    }
}
