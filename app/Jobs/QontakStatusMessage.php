<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Inisiatif\WhatsappQontakPhp\Client;
use Inisiatif\WhatsappQontakPhp\Credential;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use DB;


class QontakStatusMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    const URL = "https://service-chat.qontak.com/api/open/v1/broadcasts/";

    public $logQontakBroadcast;

    public function __construct($logQontakBroadcast)
    {
        $this->logQontakBroadcast = $logQontakBroadcast;
        App::setLocale('id');
    }

    public function handle()
    {
        $logQontakBroadcast = json_decode($this->logQontakBroadcast->reason);

        $accessToken = \config('qontak.access_token');
        $endpoint = self::URL . $logQontakBroadcast->id . "/whatsapp/log";

        $headers = [
            'content-type' => 'application/json',
            'Authorization' => \sprintf('Bearer %s', $accessToken ?? ''),
        ];
        $result = Http::withHeaders($headers)->get($endpoint);
        $result = $result->json();
        
        $whatsapp_status = null;
        $whatsapp_error_message = null;
        if(isset($result['data'])) {
            $whatsapp_status = $result['data'][0]['status'];
            $whatsapp_error_message = $result['data'][0]['whatsapp_error_message'];
            if($whatsapp_error_message == "n/a") {
                $whatsapp_error_message = null;
            }
        }
        if(isset($result->data)) {
            $whatsapp_status = $result->data[0]['status'];
            $whatsapp_error_message = $result->data[0]['whatsapp_error_message'];
            if($whatsapp_error_message == "n/a") {
                $whatsapp_error_message = null;
            }
        }
        
        $this->logQontakBroadcast->whatsapp_status = $whatsapp_status;
        $this->logQontakBroadcast->whatsapp_error_message = $whatsapp_error_message;
        $this->logQontakBroadcast->qontak_log = $result;
        $this->logQontakBroadcast->save();
    }
}
