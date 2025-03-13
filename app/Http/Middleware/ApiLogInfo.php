<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiLogInfo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function terminate($request, $response)
    {
        $userId = Auth::id();
        $payload = json_encode($this->fiteredAttribute($request->all()));
        $responsePayload = json_encode($this->fiteredAttribute(json_decode($response->getContent())));
        $content = "[{$request->ip()}] [{$userId}] [{$request->method()} {$request->path()}] [{$response->status()}] [payload: {$payload}] [response: {$responsePayload}]";

        Log::info($content);
    }

    private function fiteredAttribute($payload)
    {
        if ($payload == null)
            return;
        $pii = ['password',  'token', 'current_password', 'password_confirmation',  'pin', 'no_passport', 'birth_date', 'address', 'no_hp', 'email', 'name', 'data', 'first_page_url', 'last_page_url', 'links'];
        $mask = [];
        $result = [];
        foreach($payload as $key => $value) {
            if (in_array($key, $pii)) {
                $result[$key] = '******';
                continue;
            }

            if (in_array($key, $mask)) {
                $result[$key] = Str::mask($value, '*', 5);
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }
}
