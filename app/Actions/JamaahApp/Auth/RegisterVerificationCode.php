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
use Carbon\Carbon;

class RegisterVerificationCode
{
    const VIA_PHONE = 'phone';
    const VIA_EMAIL = 'email';
    
    public static function verify(Request $request)
    {
        if ($request->via == self::VIA_PHONE) {
            $verifyValue = $request->no_hp;    
        }
        if ($request->via == self::VIA_EMAIL) {
            $verifyValue = $request->email;
        }
        $verify = VerificationEmailPhoneParticipant::where('via', $request->via)->where('verify', $verifyValue)->where('code', $request->code)->first();
        if($verify) {
            Participant::where('email', $request->email)->update(['email_verified_at'=>Carbon::now()]);
            return true;
        } else {
            return false;
        }
    }
}
