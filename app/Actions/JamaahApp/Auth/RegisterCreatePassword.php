<?php

namespace App\Actions\ParticipantApp\Auth;

use App\Models\Participant;
use App\Models\VerificationEmailPhoneParticipant;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class RegisterCreatePassword
{
    const VIA_PHONE = 'phone';
    const VIA_EMAIL = 'email';
    const MAX_LOGIN_ATTEMPT = 5;
    const ALLOW_MAX_LOGIN_ATTEMPT_IN = 300; # 5 minutes

    public static function create(Request $request)
    {
        $keyAttempt = "app-create-password:{$request->code}";
        if (RateLimiter::tooManyAttempts($keyAttempt, self::MAX_LOGIN_ATTEMPT)) {
            throw new ThrottleRequestsException(__('auth.throttle'));
        }

        if ($request->via == self::VIA_PHONE) {
            $verifyValue = $request->no_hp;    
        }
        if ($request->via == self::VIA_EMAIL) {
            $verifyValue = $request->email;
        }

        $verify = VerificationEmailPhoneParticipant::where('via', $request->via)->where('verify', $verifyValue)->where('code', $request->code)->first();
        if(!$verify) {
            RateLimiter::hit($keyAttempt, self::ALLOW_MAX_LOGIN_ATTEMPT_IN);
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if ($request->via == self::VIA_PHONE) {
            $isAccountExist = Participant::where('no_hp',$request->no_hp)->first();
            if($isAccountExist) {
                Participant::where('no_hp', $request->no_hp)->update(['password' => Hash::make($request->password)]);
                return true;
            }

            Participant::create([
                'no_hp' => $request->no_hp, 
                'name' => '', 
                'gender' => 0, 
                'password' => Hash::make($request->password),
                'created_from' => $request->created_from
            ]);

            return true;
        }
        if ($request->via == self::VIA_EMAIL) {
            $isAccountExist = Participant::where('email',$request->email)->first();
            if($isAccountExist) {
                Participant::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
                return true;
            }

            Participant::create([
                'email' => $request->email, 
                'name' => '', 
                'gender' => 0, 
                'password' => Hash::make($request->password),
                'created_from' => $request->created_from
            ]);

            return true;
        }
    }
}
