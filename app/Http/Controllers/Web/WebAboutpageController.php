<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\WebGeneralSetting;
use App\Models\WebIndexSetting;
use App\Models\WebAboutSetting;
use App\Models\WebPartner;
use App\Models\WebProgram;
use App\Models\WebSlider;
use App\Models\WebTourPackage;
use App\Models\WebWhyus;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class WebAboutpageController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $result['webSetting'] = $this->getWebAboutSetting();
        $result['webGeneral'] = $this->getWebGeneral();
        $result['slider']['data'] = WebSlider::orderBy('id')->get();
        $result['whyus']['data'] = WebWhyus::orderBy('order')->orderBy('updated_at', 'desc')->get();
        $result['program']['data'] = WebProgram::orderBy('id')->get();
        $result['tourPackage']['data'] = WebTourPackage::orderBy('order')->orderBy('updated_at', 'desc')->get();
        $result['partner']['data'] = WebPartner::orderBy('order')->orderBy('updated_at', 'desc')->get();
        $result['article']['data'] = Article::limit(9)->orderBy('id', 'desc')->get();

        return response()->json($result);
    }

    private function getWebAboutSetting()
    {
        $webSetting = WebAboutSetting::latest()->first();
        return !empty($webSetting)
                ? Arr::except($webSetting, ['id'])
                : $webSetting;
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
