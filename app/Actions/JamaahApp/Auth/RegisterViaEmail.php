<?php

namespace App\Actions\ParticipantApp\Auth;

use App\Mail\Auth\VerifyEMailByCode;
use App\Models\Participant;
use App\Models\VerificationEmailPhoneParticipant;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class RegisterViaEmail
{
    const MAX_LOGIN_ATTEMPT = 3;
    const ALLOW_MAX_REGISTER_ATTEMPT_IN = 600; # 10 minutes

    public static function verify(Request $request)
    {
        $keyAttempt = "app-register:{$request->email}";
        if (RateLimiter::tooManyAttempts($keyAttempt, self::MAX_LOGIN_ATTEMPT)) {
            throw new ThrottleRequestsException(__('auth.throttle'));
        }

        $isAccountExist = Participant::where('email', $request->email)
                                ->whereNotNull('password')
                                ->whereNotNull('email_verified_at')
                                ->first();

        if ($isAccountExist) {
            RateLimiter::hit($keyAttempt, self::ALLOW_MAX_REGISTER_ATTEMPT_IN);
            throw ValidationException::withMessages([
                'email' => [__('auth.register_exists')],
            ]);
        }

        $code = random_int(100000, 999999);

        $verify = VerificationEmailPhoneParticipant::updateOrCreate(
            ['via' => 'email', 'verify' => $request->email],
            ['code' => $code]
        );

        $email = new VerifyEMailByCode($verify);
        Mail::to($request->email)->send($email);

    }
}
