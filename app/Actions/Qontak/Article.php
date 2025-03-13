<?php

namespace App\Actions\Qontak;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OrderUmrohTrip;
use App\Models\LogQontakBroadcast;
use App\Jobs\Article\SendWhatsappArticlePartTwo;
use Carbon\Carbon;

class Article
{   
    public function __construct()
    {
        App::setLocale('id');
    }

    public static function sendArticlePart2($order)
    {
        $templates = array('article-part-2');
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
                if($qontakTemplate->name == "article-part-2") {
                    SendWhatsappArticlePartTwo::dispatch($order)->delay(Carbon::now()->addSeconds(($delay*1)));
                }
            }
        }
    }
}
