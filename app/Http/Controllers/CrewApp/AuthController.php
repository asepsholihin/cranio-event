<?php

namespace App\Http\Controllers\CrewApp;

use App\Actions\CrewApp\Auth\LoginViaEmail;
use App\Actions\CrewApp\Auth\LoginViaPhone;
use App\Http\Controllers\Controller;
use App\Http\Requests\CrewApp\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class AuthController extends Controller
{
    const VIA_PHONE = 'phone';
    const VIA_EMAIL = 'email';

    public function __construct()
    {
        App::setLocale('id');
    }

    public function login(LoginRequest $request)
    {
        $user = ($request->via == self::VIA_PHONE)
                    ? LoginViaPhone::attempt($request)
                    : LoginViaEmail::attempt($request);

        // $user->tokens()->delete(); #revoke other devices
        $token = explode('|', $user->createToken($request->device_name)->plainTextToken)[1];
        return ['token' => $token];
    }
}
