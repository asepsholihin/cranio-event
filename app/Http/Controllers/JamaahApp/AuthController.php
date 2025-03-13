<?php

namespace App\Http\Controllers\ParticipantApp;

use App\Actions\ParticipantApp\Auth\LoginViaEmail;
use App\Actions\ParticipantApp\Auth\LoginViaPhone;
use App\Actions\ParticipantApp\Auth\RegisterViaEmail;
use App\Actions\ParticipantApp\Auth\RegisterViaPhone;
use App\Actions\ParticipantApp\Auth\RegisterVerificationCode;
use App\Actions\ParticipantApp\Auth\RegisterCreatePassword;
use App\Http\Controllers\Controller;
use App\Http\Requests\ParticipantApp\Auth\LoginRequest;
use App\Http\Requests\ParticipantApp\Auth\RegisterRequest;
use App\Http\Requests\ParticipantApp\Auth\RegisterVerificationCodeRequest;
use App\Http\Requests\ParticipantApp\Auth\RegisterCreatePasswordRequest;
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

        $user->tokens()->delete(); #revoke other devices
        $token = explode('|', $user->createToken($request->device_name)->plainTextToken)[1];
        return ['token' => $token];
    }

    public function register(RegisterRequest $request)
    {
        if ($request->via == self::VIA_PHONE) {
            RegisterViaPhone::verify($request);
        }

        if ($request->via == self::VIA_EMAIL) {
            RegisterViaEmail::verify($request);
        }

        return ['verification_sent' => true];
    }

    public function verificationCode(RegisterVerificationCodeRequest $request)
    {
        $verify = RegisterVerificationCode::verify($request);
        if($verify) {
            $message = "Verfication code is correct";
        } else {
            $message = "Verfication code is incorrect";
        }

        return ['verification_success' => $verify, 'message' => $message];
    }

    public function createPassword(RegisterCreatePasswordRequest $request)
    {
        $result = RegisterCreatePassword::create($request);
        $token = "";
        if($result) {
            $message = "Password Created";
            $user = ($request->via == self::VIA_PHONE)
            ? LoginViaPhone::attempt($request)
            : LoginViaEmail::attempt($request);

            $user->tokens()->delete(); #revoke other devices
            $token = explode('|', $user->createToken($request->device_name)->plainTextToken)[1];
        } else {
            $message = "An error detected";
        }

        $response = ['success' => $result, 'message' => $message];
        if($token) {
            $response['token'] = $token;
        }

        return $response;
    }
}
