<?php

namespace App\Actions\ParticipantApp\Auth;

use App\Models\Participant;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class RegisterViaPhone
{
    const MAX_LOGIN_ATTEMPT = 5;
    const ALLOW_MAX_LOGIN_ATTEMPT_IN = 300; # 5 minutes

    public static function verify(Request $request)
    {
        $verify = Participant::where('no_hp', $request->no_hp)
                                ->whereNull('pin')
                                ->first();
    }
}
