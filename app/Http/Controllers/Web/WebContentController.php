<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\WebGeneralSetting;
use App\Models\WebIndexSetting;
use App\Models\WebContentSetting;
use App\Models\WebPartner;
use App\Models\WebProgram;
use App\Models\WebSlider;
use App\Models\WebTourPackage;
use App\Models\WebWhyus;
use App\Models\PriceSimulation;
use App\Models\WebSeoPage;
use App\Support\StorageAttributes;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Image;

use Carbon\Carbon;
use DB;

class WebContentController extends Controller
{
    public function __construct()
    {
        App::setLocale('id');
        $this->middleware('permission:web-settings-add-or-edit')->only(['store']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable',
            'text' => 'nullable',
            'overlay' => 'nullable',
            'subtitle' => 'nullable',
            'link_url' => 'nullable',
            'image' => 'nullable|file|mimes:jpg,png,webp|max:1536',
            'image_mobile' => 'nullable|file|mimes:jpg,png,webp|max:1536',
            'icon' => 'nullable|file|mimes:jpg,png,webp|max:1536'
        ], [
            'image.uploaded' => 'Gambar terlalu besar, maximal ukuran 1.5MB',
            'image_mobile.uploaded' => 'Gambar terlalu besar, maximal ukuran 1.5MB',
            'icon.uploaded' => 'Gambar terlalu besar, maximal ukuran 1.5MB'
        ]);

        $overlay_value = 0;
        if ($request->overlay == "true" || $request->overlay == 1) {
            $overlay_value = 1;
        }

        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'text' => $request->text,
            'link_url' => $request->link_url,
            'overlay' => $overlay_value
        ]);

        if ($request->has_delete_image) {
            $request->merge(['image_url' => null]);
        }
        if ($request->has_delete_image_mobile) {
            $request->merge(['image_mobile_url' => null]);
        }
        if ($request->has_delete_icon) {
            $request->merge(['icon_url' => null]);
        }

        if ($request->hasFile('image')) {
            $imageMake = Image::make($request->file('image'));

            $img =  (string) $imageMake->encode('webp');
            $imgSmall = (string) $imageMake
                ->resize(1080, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->encode('webp');

            $profilePhotoPath =  WebContentSetting::DIR_FILE . pathinfo($request->file('image')->hashName(), PATHINFO_FILENAME) . '.webp';
            $profilePhotoSmallPath =  WebContentSetting::DIR_FILE . StorageAttributes::SMALL_IMG_PREFIX . pathinfo($request->file('image')->hashName(), PATHINFO_FILENAME) . '.webp';

            Storage::put($profilePhotoPath, $img);
            Storage::put($profilePhotoSmallPath, $imgSmall);
            $request->merge(['image_url' => $profilePhotoPath]);
        }

        if ($request->hasFile('image_mobile')) {
            $imageMake = Image::make($request->file('image_mobile'));

            $img =  (string) $imageMake->encode('webp');
            $imgSmall = (string) $imageMake
                ->resize(1080, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->encode('webp');

            $profilePhotoPath =  WebContentSetting::DIR_FILE . pathinfo($request->file('image_mobile')->hashName(), PATHINFO_FILENAME) . '.webp';
            $profilePhotoSmallPath =  WebContentSetting::DIR_FILE . StorageAttributes::SMALL_IMG_PREFIX . pathinfo($request->file('image_mobile')->hashName(), PATHINFO_FILENAME) . '.webp';

            Storage::put($profilePhotoPath, $img);
            Storage::put($profilePhotoSmallPath, $imgSmall);
            $request->merge(['image_mobile_url' => $profilePhotoPath]);
        }

        if ($request->hasFile('icon')) {
            $imageMake = Image::make($request->file('icon'));

            $img =  (string) $imageMake->encode('webp');

            $profilePhotoPath =  WebContentSetting::DIR_FILE . pathinfo($request->file('icon')->hashName(), PATHINFO_FILENAME) . '.webp';

            Storage::put($profilePhotoPath, $img);
            $request->merge(['icon_url' => $profilePhotoPath]);
        }

        WebContentSetting::updateOrCreate(['id' => $request->get('id')], $request->except(['image']));

        

        return response()->json(['success' => true]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        WebContentSetting::find($request->id)->delete();

        return response()->json(['success' => true]);
    }

    public function contentAboutPage()
    {
        $result['contens'] = $this->getContentAbout();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentAbout()
    {
        $contents = WebContentSetting::where('page', 'about')->orderBy('id', 'asc')->get();
        return $contents;
    }

    public function contentHajiPage()
    {
        $result['contens'] = $this->getContentHaji();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentHaji()
    {
        if(request()->get('isUpdate') == "true") {
            $contents = WebContentSetting::where('page', 'haji-parent-update')->orderBy('id', 'asc')->get();
        } else {
            $contents = WebContentSetting::where('page', 'haji-parent')->orderBy('id', 'asc')->get();
        }
        return $contents;
    }

    public function getPrivacyPolicy()
    {
        $result['webGeneral'] = $this->getWebGeneral();
        return response()->json($result);
    }

    public function contentHajiFurodaPage()
    {
        $result['contens'] = $this->getContentHajiFuroda();
        $result['webGeneral'] = $this->getWebGeneral();
        return response()->json($result);
    }

    public function getContentHajiFuroda()
    {
        $contents = WebContentSetting::where('page', 'haji-furoda')->orderBy('id', 'asc')->get();
        return $contents;
    }

    public function contentHajiKhususPage()
    {
        $result['contens'] = $this->getContentHajiKhusus();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentHajiKhusus()
    {
        $contents = WebContentSetting::where('page', 'haji-khusus')->orderBy('id', 'asc')->get();
        return $contents;
    }

    private function getWebGeneral()
    {
        $setting = WebGeneralSetting::latest()->first();
        return !empty($setting)
            ? Arr::except($setting, ['deleted', 'status', 'id'])
            : $setting;
    }

    public function contentFAQPage()
    {
        $result['contens'] = $this->getContentFAQ();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentFAQ()
    {
        $contents = WebContentSetting::where('page', 'faq')->orderBy('id', 'asc')->get();
        return $contents;
    }

    public function contentContactPage()
    {
        $result['contens'] = $this->getContentContact();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentContact()
    {
        $contents = WebContentSetting::where('page', 'contact')->orderBy('id', 'asc')->get();
        return $contents;
    }

    public function contentTabunganUmrohPage()
    {
        $result['contens'] = $this->getContentTabunganUmroh();
        $result['price_simulation'] = $this->getPriceSimulationUmroh();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentTabunganUmroh()
    {
        $contents = WebContentSetting::where('page', 'tabungan-umroh')->orderBy('id', 'asc')->get();
        return $contents;
    }

    public function getPriceSimulationUmroh()
    {
        $contents = PriceSimulation::where('program', 'tabungan-umroh')->orderBy('id', 'asc')->first();
        return $contents;
    }

    public function contentUmrohPage(Request $request)
    {
        $result['contens'] = $this->getContentUmroh($request->category);
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentUmroh($category)
    {
        $contents = WebContentSetting::where('page', $category)->orderBy('id', 'asc')->get();
        return $contents;
    }

    public function contentProfileUstadzPage()
    {
        $result['contens'] = $this->getContentProfileUstadz();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentProfileUstadz()
    {
        $contents = WebContentSetting::where('page', 'profile-ustadz')->orderBy('id', 'asc')->get();
        return $contents;
    }

    public function contentBadalParentPage()
    {
        $result['contens'] = $this->getContentBadalParent();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentBadalParent()
    {
        $contents = WebContentSetting::where('page', 'badal-parent')->orderBy('id', 'asc')->get();
        return $contents;
    }

    public function contentBadalHajiPage()
    {
        $result['contens'] = $this->getContentBadalHaji();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentBadalHaji()
    {
        $contents = WebContentSetting::where('page', 'badal-haji')->orderBy('id', 'asc')->get();
        return $contents;
    }

    public function contentBadalUmrohPage()
    {
        $result['contens'] = $this->getContentBadalUmroh();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentBadalUmroh()
    {
        $contents = WebContentSetting::where('page', 'badal-umroh')->orderBy('id', 'asc')->get();
        return $contents;
    }

    public function contentHalalTourPage()
    {
        $result['contens'] = $this->getContentHalalTour();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentHalalTour()
    {
        if(request()->get('isUpdate') == "true") {
            $contents = WebContentSetting::where('page', 'halal-tour-update')->orderBy('id', 'asc')->get();
        } else {
            $contents = WebContentSetting::where('page', 'halal-tour')->orderBy('id', 'asc')->get();
        }

        return $contents;
    }

    public function contentHalalTourProductPage()
    {
        $result['contens'] = $this->getContentHalalTourProduct();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentHalalTourProduct()
    {
        $contents = WebContentSetting::where('page', 'halal-tour-product')->orderBy('id', 'asc')->get();
        return $contents;
    }

    public function contentHalalTourDetailPage()
    {
        $result['contens'] = $this->getContentHalalTourDetail();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentHalalTourDetail()
    {
        $contents = WebContentSetting::where('page', 'halal-tour-detail')->orderBy('id', 'asc')->get();
        return $contents;
    }

    public function contentParticipantRoomPage()
    {
        $result['contens'] = $this->getContentParticipantRoom();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentParticipantRoom()
    {
        $contents = WebContentSetting::where('page', 'participant-room')->orderBy('id', 'asc')->get();
        return $contents;
    }

    public function contentParticipantRoomGalleryPage()
    {
        $result['contens'] = $this->getContentParticipantRoomGallery();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentParticipantRoomGallery()
    {
        $contents = WebContentSetting::where('page', 'participant-room-gallery')->orderBy('id', 'asc')->get();
        return $contents;
    }

    public function contentKajianPage()
    {
        $result['contens'] = $this->getContentKajian();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentKajian()
    {
        $contents = WebContentSetting::where('page', 'kajian')->orderBy('id', 'asc')->get();
        return $contents;
    }

    public function contentHalalTourCategoryPage()
    {
        $result['contens'] = $this->getContentHalalTourCategory();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentHalalTourCategory()
    {
        $seoTag = WebSeoPage::where('page', 'wisata-halal-'. request()->get('category'))->first();
        if(!$seoTag) {
            WebSeoPage::create([
                'page' => 'wisata-halal-'. request()->get('category'),
                'meta_keyword' => 'wisata halal',
                'meta_title' => 'Wisata Halal Jejak Imani',
                'meta_description' => 'Wisata Halal Jejak Imani',
                'canonical' => 'https://www.jejakimani.com/wisata-halal/' . request()->get('category'),
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'og_type' => 'website',
                'og_url' => 'https://www.jejakimani.com/wisata-halal/' . request()->get('category'),
                'og_title' => 'Wisata Halal Jejak Imani',
                'og_description' => 'Wisata Halal Jejak Imani',
                'og_image' => 'web/og_image/trLlDsYEDpEVDSS1aBpzrsGU4bm6kUokRyql27oN.jpg',
                'twitter_site' => '@Jejakimani',
                'twitter_card' => 'summary',
                'twitter_title' => 'Wisata Halal Jejak Imani',
                'twitter_description' => 'Wisata Halal Jejak Imani',
                'twitter_image' => 'web/og_image/trLlDsYEDpEVDSS1aBpzrsGU4bm6kUokRyql27oN.jpg'
            ]);
        }

        $contents = WebContentSetting::where('page', 'wisata-halal-'. request()->get('category'))->first();
        if(!$contents) {

            DB::transaction(function() {

                $sliders = array();
                for ($i=0; $i < 5; $i++) {
                    $slider = [
                        'page' => 'wisata-halal-'. request()->get('category'),
                        'section' => 'slider',
                        'content_type' => 'slider',
                        'image_url' => 'https://api.jejakimani.com/images/background_umrah.webp',
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'alt_image' => 'Wisata Halal',
                        'title_image' => 'Wisata Halal',
                    ];
                    $sliders[] = $slider;
                    WebContentSetting::create($slider);
                }

                $section1 = [
                    [
                        'page' => 'wisata-halal-'. request()->get('category'),
                        'section' => 'section1',
                        'content_type' => 'text',
                        'title' => ucwords(str_replace('-', ' ', request()->get('category'))),
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'page' => 'wisata-halal-'. request()->get('category'),
                        'section' => 'section1-a',
                        'content_type' => 'text',
                        'title' => ucwords(str_replace('-', ' ', request()->get('category'))),
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'page' => 'wisata-halal-'. request()->get('category'),
                        'section' => 'section1-b',
                        'content_type' => 'text',
                        'title' => '9 - 10 Hari',
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'page' => 'wisata-halal-'. request()->get('category'),
                        'section' => 'section1-c',
                        'content_type' => 'text',
                        'title' => '5 / setaraf',
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'page' => 'wisata-halal-'. request()->get('category'),
                        'section' => 'section1-d',
                        'content_type' => 'text',
                        'title' => 'Cari Tanggal',
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ];
                WebContentSetting::insert($section1);
                $section2 = [
                    [
                        'page' => 'wisata-halal-'. request()->get('category'),
                        'section' => 'section2',
                        'content_type' => 'text',
                        'text' => 'Description Tentang '. ucwords(str_replace('-', ' ', request()->get('category'))),
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ];
                WebContentSetting::insert($section2);
                $section3 = [
                    [
                        'page' => 'wisata-halal-'. request()->get('category'),
                        'section' => 'section3',
                        'content_type' => 'text',
                        'title' => 'Pilihan Tanggal Keberangkatan',
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ];
                WebContentSetting::insert($section3);
                $section4 = [
                    [
                        'page' => 'wisata-halal-'. request()->get('category'),
                        'section' => 'section4',
                        'content_type' => 'text',
                        'title' => 'Rencana Perjalanan Wisata Halal',
                        'text' => 'Hari ke- 1',
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ];
                WebContentSetting::insert($section4);
                $section5 = [
                    [
                        'page' => 'wisata-halal-'. request()->get('category'),
                        'section' => 'section5',
                        'content_type' => 'text',
                        'title' => 'Tanya Dulu, Konsultasi Gratis',
                        'text' => 'Punya banyak pertanyaan mengenai pilihan paket dan biaya badal haji di Jejak Imani? tanyakan pada kami kapan saja dan dimanapun Anda berada. Kami siap memberikan solusi dan konsultasi gratis dengan sepenuh hati.',
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ];
                WebContentSetting::insert($section5);
                $section6 = [
                    [
                        'page' => 'wisata-halal-'. request()->get('category'),
                        'section' => 'section6',
                        'content_type' => 'text',
                        'title' => 'Informasi Lainnya',
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ];
                WebContentSetting::insert($section6);
                $section7 = [
                    [
                        'page' => 'wisata-halal-'. request()->get('category'),
                        'section' => 'section7',
                        'content_type' => 'accordion',
                        'title' => 'Bagaimana cara mendaftar di Jejak Imani?',
                        'text' => 'Anda dapat menghubungi nomor berikut untuk konsultasi gratis dan melakukan pendaftaran.',
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ];
                WebContentSetting::insert($section7);
                $section8 = [
                    [
                        'page' => 'wisata-halal-'. request()->get('category'),
                        'section' => 'section8',
                        'content_type' => 'text',
                        'title' => 'Pilihan Wisata Halal ke Negara Lain',
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ];
                WebContentSetting::insert($section8);
                $section9Left = [
                    [
                        'page' => 'wisata-halal-'. request()->get('category'),
                        'section' => 'section9-left',
                        'content_type' => 'youtube',
                        'link_url' => '17Sxo6uleGE',
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ];
                $section9Right = [
                    [
                        'page' => 'wisata-halal-'. request()->get('category'),
                        'section' => 'section9-right',
                        'content_type' => 'text',
                        'text' => 'Jika anda ingin mengetahui lebih lanjut mengenai Informasi Paket Umroh Lebih Nyaman dan Hemat Jejak Imani. Silahkan isi form di bawah ini dan klik tombol submit. Kami akan segera menghubungi Anda',
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ];
                WebContentSetting::insert($section9Left);
                WebContentSetting::insert($section9Right);
                $section10 = [
                    [
                        'page' => 'wisata-halal-'. request()->get('category'),
                        'section' => 'section10',
                        'content_type' => 'text',
                        'title' => 'Paket Lainnya',
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ];
                WebContentSetting::insert($section10);
            });
        }

        $contentNegara = WebContentSetting::where('page', 'wisata-halal-negara')->first();
        if(!$contentNegara) {
            $sectionHalalTourCountries = [
                [
                    'page' => 'wisata-halal-negara',
                    'section' => 'country',
                    'content_type' => 'slider',
                    'title' => 'Uzbekistan',
                    'image_url' => 'https://api.jejakimani.com/images/background_umrah.webp',
                    'link_url' => 'uzbekistan',
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'page' => 'wisata-halal-negara',
                    'section' => 'country',
                    'content_type' => 'slider',
                    'title' => 'Korea',
                    'image_url' => 'https://api.jejakimani.com/images/background_umrah.webp',
                    'link_url' => 'korea',
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ];
            WebContentSetting::insert($sectionHalalTourCountries);
        }

        $contents = WebContentSetting::where('page', 'wisata-halal-'. request()->get('category'))->orderBy('id', 'asc')->get()->toArray();
        $contentNegara = WebContentSetting::where('page', 'wisata-halal-negara')->get()->toArray();

        $contents = array_merge($contents, $contentNegara);

        return $contents;
    }

    public function contentUmrohHajiPage()
    {
        $result['contens'] = $this->getContentUmrohHaji();
        $result['webGeneral'] = $this->getWebGeneral();

        return response()->json($result);
    }

    public function getContentUmrohHaji()
    {
        if(request()->get('pageName') == "haji") {
            $contents = WebContentSetting::where('page', 'haji-parent-update')->orderBy('id', 'asc')->get();
        } else {
            $contents = WebContentSetting::where('page', 'umroh-parent-update')->orderBy('id', 'asc')->get();
        }
        return $contents;
    }

    public function contentCalendarRequest(){
        $contents = WebContentSetting::where('page', 'calendar-request')->orderBy('id', 'asc')->get();
        return response()->json($contents);
    }

    public function contentMerchandiseConfirmation(){
        $contents = WebContentSetting::where('page', 'merchandise-confirmation')->orderBy('id', 'asc')->get();
        return response()->json($contents);
    }

    public function saveArrayTour(Request $request){
        $items = json_decode($request->items);
        foreach($items as $item){
            $data = [
                'page' => $request->page,
                'content_type' => $request->content_type,
                'text' => $item->text,
                'section' => $item->section
            ];
            if(!empty($item->id)){
                $pushed =  WebContentSetting::find($item->id);
                $pushed->update($data);
            }else{
                $pushed =  new WebContentSetting();
                $pushed->create($data);
            }
        }
        return response()->json($items);
    }
}
