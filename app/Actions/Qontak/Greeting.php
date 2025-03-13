<?php

namespace App\Actions\Qontak;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OrderUmrohTrip;
use App\Models\LogQontakBroadcast;
use App\Jobs\Greetings\SendWhatsappGreeting;
use App\Jobs\Greetings\SendWhatsappArticlePartOne;
use App\Jobs\Greetings\SendWhatsappManasikInformation;
use Carbon\Carbon;

class Greeting
{   
    public function __construct()
    {
        App::setLocale('id');
    }

    public static function sendGreetings($order)
    {
        $templates = array('greetings','manasik-information','article-part-1');
        $qontakTemplates = DB::table('qontak_templates')->whereIn('name', $templates)->get();

        $delay = 60;
        foreach ($qontakTemplates as $qontakTemplate) {
            $checkLog = LogQontakBroadcast::where('umroh_trip_id', $order->umroh_trip_id)
            ->where('no_hp', $order->no_hp)->where('template_id', $qontakTemplate->template_id)
            ->where('status', 'Delivered')->first();
            if($checkLog) {
                //sudah dikirim    
            } else {
                //belum dikirim
                if($qontakTemplate->name == "greetings") {
                    SendWhatsappGreeting::dispatch($order)->delay(Carbon::now()->addSeconds(($delay*1)));
                }
                if($qontakTemplate->name == "manasik-information") {
                    SendWhatsappManasikInformation::dispatch($order)->delay(Carbon::now()->addSeconds(($delay*2)));
                }
                if($qontakTemplate->name == "article-part-1") {
                    SendWhatsappArticlePartOne::dispatch($order)->delay(Carbon::now()->addSeconds(($delay*3)));
                }
            }
        }
    }
}
