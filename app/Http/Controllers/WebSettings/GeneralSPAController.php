<?php

namespace App\Http\Controllers\WebSettings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\WebSettings\StoreGeneralRequest;
use App\Models\WebGeneralSetting;
use App\Models\MasterOffice;
use App\Models\SocialMedia;
use App\Models\WebSeoPage;
use App\Models\WebProduct;
use App\Models\Asatidz;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Image;
use DB;
use Meema\CloudFront\Facades\CloudFront;
use Meema\CloudFront\Jobs\InvalidateCache;
use App\Support\NumberFormat;

class GeneralSPAController extends Controller
{
    const SPA_PATH = '/web-settings/general';

    public function __construct()
    {
        $this->middleware('permission:web-settings-add-or-edit')->only(['store']);
    }

    /**
     * Display resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->responseLatestSetting();
    }

    public function apiIndex()
    {
        $setting = WebGeneralSetting::latest()->first();

        return response()->json(
            !empty($setting)
                ? Arr::except($setting, ['deleted', 'status', 'id'])
                : $setting
        );
    }

    /**
     * Store resource.
     *
     * @param  StoreGeneralRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGeneralRequest $request)
    {
        $setting = WebGeneralSetting::latest()->first();
        $payload = $request->except(['web_logo', 'web_favicon']);
        $uid = auth()->user()->id;

        empty($setting)
            ? $setting = WebGeneralSetting::create(
                array_merge($payload, [
                    'created_by' => $uid,
                    'updated_by' => $uid,
                ])
            )
            : $setting->update($payload);

        $this->storeFile($request, $setting, ['web_logo', 'web_favicon']);

        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }

        return $this->responseLatestSetting();
    }

    /**
     * Store file.
     *
     * @param  StoreGeneralRequest  $request
     * @param  WebGeneralSetting  $setting
     * @param  array  $keys
     * @return bool
     */
    private function storeFile(StoreGeneralRequest $request, WebGeneralSetting $setting, array $keys = [])
    {
        $files = [];

        foreach ($keys as $key) {
            if ($request->hasFile($key) && $key == 'web_logo') { #resize and change format webp
                $imageMake = Image::make($request->file($key));

                $img = (string) $imageMake->encode('webp');
                $imagePath =  WebGeneralSetting::DIR_FILE . 'web-logo.webp';
                Storage::put($imagePath, $img);
                $files = array_merge($files, [$key => $imagePath]);
                continue;
            }

            if ($request->hasFile($key)) {
                $file_path = $request->file($key)->store(WebGeneralSetting::DIR_FILE);
                $files = array_merge($files, [$key => $file_path]);
            }
        }

        return !empty($files)
            ? $setting->update($files)
            : true;
    }

    private function responseLatestSetting()
    {
        $setting = WebGeneralSetting::latest()->first();

        return response()->json(
            !empty($setting)
                ? Arr::except($setting, ['deleted', 'status', 'id'])
                : $setting
        );
    }

    public function appInfo()
    {
        $y = date('Y');
        $m = date('m');
        $d = date('d');
        $jd = GregoriantoJD($m, $d, $y);
        $l = $jd - 1948440 + 10632;
        $n = (int) (($l - 1) / 10631);
        $l = $l - 10631 * $n + 354;
        $j = ((int) ((10985 - $l) / 5316)) * ((int) ((50 * $l) / 17719)) + (
            (int) ($l / 5670)) * ((int) ((43 * $l) / 15238));
        $l = $l - ((int) ((30 - $j) / 15)) * ((int) ((17719 * $j) / 50)) - (
            (int) ($j / 16)) * ((int) ((15238 * $j) / 43)) + 29;
        $m = (int) ((24 * $l) / 709);
        $d = $l - (int) ((709 * $m) / 24);
        $y = 30 * $n + $j - 30;

        $bulanHijriah = array(
            1 => "Muharram", "Shofar", "Robi'ul Awwal", "Robi'uts Tsani",
            "Jumadil Ula", "Jumadil Akhiroh", "Rojab", "Sya'ban",
            "Romadhon", "Syawwal", "Dzulqo'dah", "Dzulhijjah"
        );

        $hijriah = $d . ' ' . $bulanHijriah[$m] . ' ' . $y . 'H';

        return [
            'indonesia_date' => Carbon::now()->isoFormat('dddd, D MMMM Y'),
            'hijri_date' => $hijriah,
        ];
    }

    public function footerContent()
    {
        $setting = WebGeneralSetting::latest()->select('footer_location', 'footer_consultation', 'web_email', 'copyright_text', 'partnership_contact', 'head_office_address', 'footer_menu_links', 'siskopatuhimg')->first();
        $offices = DB::table('master_office')->select('id', 'office_name', 'office_phone', 'office_address')->where('show_footer', 1)->orderBy('order')->get();
        $socialMedia = DB::table('social_media')->select('id', 'social_media_name', 'social_media_link', 'icon')->where('status', 1)->get();

        $content = $setting;
        $content['offices'] = $offices;
        $content['social_media'] = $socialMedia;
        return response()->json($content);
    }

    public function postFooterContent(Request $request)
    {
        if ($request->hasFile('logo_siskopatuh')) {
            $imageMake = Image::make($request->file('logo_siskopatuh'));

            $img =  (string) $imageMake->encode('webp');

            $profilePhotoPath =  WebGeneralSetting::DIR_FILE . pathinfo($request->file('logo_siskopatuh')->hashName(), PATHINFO_FILENAME) . '.webp';

            Storage::put($profilePhotoPath, $img);
            $request->merge(['logo_siskopatuh' => $profilePhotoPath]);
        }
        $setting = WebGeneralSetting::latest();
        $setting->update($request->except('social_media_name', 'social_media_link', 'social_id', 'icon_social_media'));

        if(count($request->get('social_id')) > 0)
        {
            $array = array_diff($request->get('social_id'), ['undefined']);
            DB::table('social_media')->whereNotIn('id', $array)->update(['status'=>2]);
            foreach($request->get('social_media_name') as $key=>$value){
                $data = [
                    'social_media_name' => $request->get('social_media_name')[$key],
                    'social_media_link' => $request->get('social_media_link')[$key],
                    'status' => 1,
                ];
                $cimss = $request->file('icon_social_media')[$key] ?? "";
                // IMAGE SOCIAL MEDIA
                if ($request->hasFile('icon_social_media')) {
                    if(!empty($cimss)){
                        $imageMake = Image::make($request->file('icon_social_media')[$key]);

                        $img =  (string) $imageMake->encode('webp');

                        $profilePhotoPath =  SocialMedia::DIR_ICON . pathinfo($request->file('icon_social_media')[$key]->hashName(), PATHINFO_FILENAME) . '.webp';

                        Storage::put($profilePhotoPath, $img);
                        $data['icon'] = $profilePhotoPath;
                    }
                }
                // INSERT
                if($request->get('social_id')[$key] != 'undefined')
                {
                    $socialMedia = DB::table('social_media')->where('id', $request->get('social_id')[$key])->update($data);
                }
                // UPDATE
                else{
                    $data['created_by'] = 1;
                    $data['created_at'] = date('Y-m-d');
                    $data['updated_by'] = 1;
                    $socialMedia = DB::table('social_media')->insert($data);
                }
            }
        }

        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }
    }

    public function seoPage(Request $request)
    {
        $page = $request->page;

        $seoTag = WebSeoPage::where('page', $page)->first();

        return response()->json($seoTag);
    }

    public function addSeoPage(Request $request)
    {

        $request->merge([
            'og_type' => $request->og_type ?? 'website',
            'og_url' => $request->canonical,
            'og_title' => $request->meta_title,
            'og_description' => $request->meta_description,
            'twitter_card' => $request->twitter_card ?? 'summary',
            'twitter_site' => $request->twitter_site ?? '@Jejakimani',
            'twitter_title' => $request->meta_title,
            'twitter_description' => $request->meta_description,
        ]);
        $seoTag = WebSeoPage::updateOrCreate(['page' => $request->page], $request->except('page'));
        if($seoTag->og_image == null) {
            $seoTag->og_image = 'web/og_image/trLlDsYEDpEVDSS1aBpzrsGU4bm6kUokRyql27oN.jpg';
            $seoTag->save();
        }
        if($seoTag->twitter_image == null) {
            $seoTag->twitter_image = 'web/og_image/trLlDsYEDpEVDSS1aBpzrsGU4bm6kUokRyql27oN.jpg';
            $seoTag->save();
        }
        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }

        return response()->json($seoTag);
    }

    public function addSeoProduct(Request $request)
    {
        $product = WebProduct::find($request->id);
        if ($product) {
            $product->update($request->except('id'));
        }

        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }

        return response()->json($product);
    }

    public function addSeoProfileUstadz(Request $request)
    {
        $asatidz = Asatidz::find($request->id);
        if ($asatidz) {
            $asatidz->update($request->except('id'));
        }

        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }

        return response()->json($asatidz);
    }

    public function getPrivacyPolicy()
    {
        $privacyPolicy = WebGeneralSetting::select('id', 'privacy_policy')->latest()->first();
        return response()->json($privacyPolicy);
    }

    public function getCountryCode()
    {
        $codes = WebGeneralSetting::getCountryCode();
        return response()->json($codes);
    }

    public function getOffices()
    {
        $offices = MasterOffice::orderBy('order', 'asc')->get();
        foreach ($offices as $value) {
            $whatsapp_text = $value->whatsapp_link;
            $whatsapp_number = NumberFormat::formatWhatsappIndonesia($value->office_phone);
            $template = "https://api.whatsapp.com/send/?phone=" . $whatsapp_number . "&text=" . $whatsapp_text . "";

            $value['whatsapp_number'] = $whatsapp_number;
            $value['whatsapp_api'] = $template;
            $value['google_tag_event'] = '';
            $value['google_tag_event_category'] = '';
            $value['google_tag_event_label'] = '';
        }

        return response()->json($offices);
    }

    public function postOffice(Request $request){
        $offices = MasterOffice::findOrFail($request->get('id'));
        $offices->office_name = $request->get('office_name');
        $offices->office_phone = $request->get('office_phone');
        $offices->office_address = $request->get('office_address');
        $offices->order = $request->get('order');
        $offices->latitude = $request->get('latitude');
        $offices->longitude = $request->get('longitude');
        $offices->save();

        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }
    }

    public function postMaps(Request $request){
        $setting = WebGeneralSetting::latest()->first();
        $setting->update(['latitude'=>$request->get('latitude'), 'longitude'=>$request->get('longitude'), 'title_maps'=>$request->get('title_maps')]);
        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }
    }

    public function deleteOffice($id){
        MasterOffice::where('id', $id)->delete();

        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }
    }
}
