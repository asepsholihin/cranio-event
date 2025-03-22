<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\WebGeneralSetting;
use App\Models\WebIndexSetting;
use App\Models\WebPartner;
use App\Models\WebProgram;
use App\Models\WebSlider;
use App\Models\WebTourPackage;
use App\Models\WebWhyus;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class WebHomepageController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $result['webSetting'] = $this->getWebSetting();
        $result['webGeneral'] = $this->getWebGeneral();
        $result['slider']['data'] = WebSlider::where('status', 1)->orderBy('id')->get() ?? null;
        $result['whyus']['data'] = WebWhyus::where('status', 1)->orderBy('order')->orderBy('updated_at', 'desc')->get();
        $result['program']['data'] = WebProgram::where('status', 1)->orderBy('id')->get();
        $result['partner']['data'] = WebPartner::where('status', 1)->orderBy('order')->orderBy('updated_at', 'desc')->get();

        return response()->json($result);
    }

    private function getWebSetting()
    {
        $webSetting = WebIndexSetting::latest()->first();
        return !empty($webSetting)
            ? Arr::except($webSetting, ['deleted', 'status', 'id'])
            : $webSetting;
    }

    private function getWebGeneral()
    {
        $setting = WebGeneralSetting::latest()->first();
        return response()->json(
            !empty($setting)
                ? Arr::except($setting, ['deleted', 'status', 'id'])
                : $setting
        );
    }
}
