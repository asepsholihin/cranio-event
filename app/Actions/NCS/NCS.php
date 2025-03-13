<?php

namespace App\Actions\NCS;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use App\Models\LogNcsApiRequest;

class NCS
{
    const URL = "https://sandbox-api.ptncs.com/";
    
    public function __construct()
    {
        App::setLocale('id');
    }

    public static function pickupRequest($body, $createdById)
    {
        $userName = \config('ncs.username');
        $apiKey = \config('ncs.api_key');
        $endpoint = self::URL . "bot/picktranscargo/" . $userName . "/" . $apiKey;

        $response = Http::post($endpoint, $body);
        LogNcsApiRequest::create([
            'name' => 'PICKUP REQUEST CARGO',
            'request' => json_encode($body),
            'response' => $response->body(),
            'created_by' => $createdById
        ]);
        return $response->json();
    }

    public static function publishRate($body)
    {
        $userName = \config('ncs.username');
        $apiKey = \config('ncs.api_key');
        $endpoint = self::URL . "bot/publishrate/" . $userName . "/" . $apiKey;

        $result = Http::get($endpoint, $body);
        $result = $result->json();
        // Find NRS Service Only
        // $found_key = array_search('NRS', array_column($result['data'], 'ServiceCode'));
        // $response = $result['data'][$found_key] ?? null;
        // LogNcsApiRequest::create([
        //     'name' => 'PUBLISH RATE',
        //     'request' => $body,
        //     'response' => $response->json(),
        // ]);
        return $result;
    }
}
