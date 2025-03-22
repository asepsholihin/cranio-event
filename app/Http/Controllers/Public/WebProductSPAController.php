<?php

namespace App\Http\Controllers\Public;

use App\Exceptions\ErrorMessageException;
use App\Http\Controllers\Controller;
use App\Models\WebProduct;
use App\Models\OtherPackage;
use App\Models\Package;
use App\Models\Itinerary;
use App\Models\Facility;
use App\Models\WebSubcategory;
use App\Models\WebProductImage;
use App\Models\WebContentProduct;
use App\Models\UmrohTrip;
use App\Http\Requests\Catalog\WebProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Support\StorageAttributes;
use Meema\CloudFront\Facades\CloudFront;
use Image;
use DB;
use Carbon\Carbon;

class WebProductSPAController extends Controller
{
    const SPA_PATH = '/catalog/products';

    public function __construct()
    {

    }

    /**
     * Display resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orderBy = request()->query('sortBy', 'price_start_from');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage', 10);
        $query = DB::table('web_products')->select([
            'web_products.id','web_products.name','web_products.title','web_products.slug','web_products.description','web_products.flights','web_products.image_thumbnail',
            'web_products.alt_image_thumbnail', 'web_products.title_image_thumbnail', 'web_products.price_start_from', 'web_products.trip_id', 'web_products.departure_date',
            'packages.image_icon', 'umroh_trips.total_days', 'umroh_trips.location_destination_id', 'web_sub_categories.slug as sub_category_slug', 'web_sub_categories.name as sub_category_name', 'web_products.package_type',
            'web_products.sub_category_id', 'airlines.logo as airline_logo', 'airlines.name as airlines', 'umroh_trips.ustadz', 'umroh_trips.itinerary_category_id', 'packages.name as package_name', 'umroh_trips.available_seats as available_seat'
        ])
        ->join('airlines', 'airlines.id', 'web_products.flights')
        ->join('web_sub_categories', 'web_sub_categories.id', 'web_products.sub_category_id')
        ->join('umroh_trips', 'umroh_trips.id', 'web_products.trip_id')
        ->leftJoin('package_umroh_trips', 'package_umroh_trips.id', 'web_products.package_type')
        ->leftJoin('packages', 'packages.name', 'package_umroh_trips.name')
        ->where('web_products.status', 1)
        ->where('web_products.deleted', 0)
        ->where('umroh_trips.available_seats', '>', 0)
        ->whereDate('web_products.departure_date', '>=', now());

        if (!empty(request()->query('categoryId'))) {
            $query->where('umroh_trips.category_id', request()->categoryId);
        }

        if (!empty(request()->query('subcategoryId'))) {
            if (request()->query('subcategoryId') == "2_5") {
                $query->whereIn('web_products.sub_category_id', array(2, 5));
            } else if (request()->query('subcategoryId') == "7_8") {
                $query->whereIn('web_products.sub_category_id', array(7, 8, 10, 11));
            } else {
                $query->where('web_products.sub_category_id', request()->query('subcategoryId'));
            }
        }

        if (!empty(request()->query('departureDate'))) {
            $departureDate = Carbon::parse(request()->query('departureDate'))->format('Y-m-d');
            $query->where('departure_date', $departureDate);
        }

        if (!empty(request()->query('packageType'))) {
            $packageTypes = explode(",", request()->query('packageType'));
            $query->where(function ($queries) use ($packageTypes) {
                foreach ($packageTypes as $packageType) {
                    $queries->orWhere('package_umroh_trips.name', 'like', $packageType);
                }
            });
        }

        if (!empty(request()->query('productId'))) {
            $query->where('web_products.id', '<>', request()->query('productId'));
        }

        $products = $query->orderBy($orderBy, $sortBy)
        ->paginate($perPage)
        ->withQueryString()
        ->withPath(self::SPA_PATH);

        $productData = $products->items();
        foreach ($productData as $product) {
            $package = DB::table('package_umroh_trips')->select([
                DB::raw('MAX(GREATEST(star_hotel_makkah::integer, star_hotel_madinah::integer)) as star'),
                DB::raw('MIN(LEAST(star_hotel_makkah::integer, star_hotel_madinah::integer)) as low_star')
            ])
            ->where('id', $product->package_type)
            ->first();
            $product->star = $package->star ?? 3;
            $product->low_star = $package->low_star ?? 3;

            $hotel = DB::table('hotel_umroh_trips')->select([
                DB::raw('MAX(GREATEST(star::integer)) as star'),
                DB::raw('MIN(LEAST(star::integer)) as low_star')
            ])->where('umroh_trip_id', $product->trip_id)->first();

            if ($hotel->star) {
                $product->star = $hotel->star ?? 3;
                $product->low_star = $hotel->low_star ?? 3;
            }

            $destinations = "";
            if ($product->location_destination_id) {
                $location_ids = Str::replace('[', '', $product->location_destination_id);
                $location_ids = Str::replace(']', '', $location_ids);
                $location_ids = explode(',', $location_ids);
                if(!$location_ids) {
                    $destinations = City::select(['name'])
                    ->whereIn('id', $location_ids)
                    ->pluck('name');
                }
            }

            if ($product->image_thumbnail == null) {
                $thumbnail = asset('images/thumbnail_umrah_ruby.webp');
                $package = DB::table('package_umroh_trips')->select('name')->where('id', $product->package_type)->first();
                if ($package->name == "Ruby") {
                    $thumbnail = asset('images/thumbnail_umrah_ruby.webp');
                }
                if ($package->name == "Emerald") {
                    $thumbnail = asset('images/thumbnail_umrah_emerald.webp');
                }
                if ($package->name == "Sapphire") {
                    $thumbnail = asset('images/thumbnail_umrah_sapphire.webp');
                }
                if ($package->name == "Lebih Hemat") {
                    $thumbnail = asset('images/thumbnail_umrah_ruby.webp');
                }
                $product->image_thumbnail = $thumbnail;
            }

            $product->destinations = $destinations;
            $product->itineraries = DB::table('itineraries')->select('day_title', 'time', 'title', 'description')->whereNull('deleted_at')->where('category_id', $product->itinerary_category_id)->orderBy('order', 'ASC')->get();
            $product->hotels = UmrohTrip::getHotels($product->trip_id, $product->package_type);
            $product->available_seats = UmrohTrip::getAvailableSeat($product->trip_id, $product->package_type);
        }

        return response()->json($products);
    }

    public function getProductByCategory($categoriId)
    {
        $orderBy = request()->query('sortBy', 'departure_date');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage', 10);
        $query = DB::table('web_products')->select([
            'web_products.id','web_products.name','web_products.title','web_products.slug','web_products.description','web_products.flights','web_products.image_thumbnail',
            'web_products.alt_image_thumbnail', 'web_products.title_image_thumbnail', 'web_products.price_start_from', 'web_products.trip_id', 'web_products.departure_date',
            'packages.image_icon','umroh_trips.currency', 'umroh_trips.total_days', 'umroh_trips.location_destination_id', 'web_sub_categories.slug as sub_category_slug', 'web_products.package_type',
            'web_products.sub_category_id', 'airlines.logo as airline_logo', 'airlines.name as airlines', 'umroh_trips.ustadz', 'package_umroh_trips.name as package_name', 'packages.document_style'
        ])
        ->join('airlines', 'airlines.id', 'web_products.flights')
        ->join('web_sub_categories', 'web_sub_categories.id', 'web_products.sub_category_id')
        ->join('umroh_trips', 'umroh_trips.id', 'web_products.trip_id')
        ->leftJoin('package_umroh_trips', 'package_umroh_trips.id', 'web_products.package_type')
        ->leftJoin('packages', 'packages.name', 'package_umroh_trips.name')
        ->where('web_products.status', 1)
        ->where('web_products.deleted', 0)
        ->whereDate('web_products.departure_date', '>=', now());

        if (!empty($categoriId)) {
            if ($categoriId == "2_5") {
                $query->whereIn('web_products.sub_category_id', array(2, 5));
            } else if ($categoriId == "7_8") {
                $query->whereIn('web_products.sub_category_id', array(7, 8, 10, 11));
            } else {
                $query->where('web_products.sub_category_id', $categoriId);
            }
        }

        $products = $query->orderBy($orderBy, $sortBy)->limit($perPage)->get();

        foreach ($products as $product) {
            $package = DB::table('package_umroh_trips')->select([
                DB::raw('MAX(GREATEST(star_hotel_makkah::integer, star_hotel_madinah::integer)) as star'),
                DB::raw('MIN(LEAST(star_hotel_makkah::integer, star_hotel_madinah::integer)) as low_star')
            ])
            ->where('id', $product->package_type)
            ->first();
            $product->star = $package->star ?? 3;
            $product->low_star = $package->low_star ?? 3;

            $hotel = DB::table('hotel_umroh_trips')->select([
                DB::raw('MAX(GREATEST(star::integer)) as star'),
                DB::raw('MIN(LEAST(star::integer)) as low_star')
            ])->where('umroh_trip_id', $product->trip_id)->first();

            if ($hotel->star) {
                $product->star = $hotel->star ?? 3;
                $product->low_star = $hotel->low_star ?? 3;
            }

            $destinations = "";
            if ($product->location_destination_id) {
                $location_ids = Str::replace('[', '', $product->location_destination_id);
                $location_ids = Str::replace(']', '', $location_ids);
                $location_ids = explode(',', $location_ids);
                if(!$location_ids) {
                    $destinations = City::select(['name'])
                    ->whereIn('id', $location_ids)
                    ->pluck('name');
                }
            }

            if ($product->image_thumbnail == null) {
                $thumbnail = asset('images/thumbnail_umrah_ruby.webp');
                $package = DB::table('package_umroh_trips')->select('name')->where('id', $product->package_type)->first();
                if ($package->name == "Ruby") {
                    $thumbnail = asset('images/thumbnail_umrah_ruby.webp');
                }
                if ($package->name == "Emerald") {
                    $thumbnail = asset('images/thumbnail_umrah_emerald.webp');
                }
                if ($package->name == "Sapphire") {
                    $thumbnail = asset('images/thumbnail_umrah_sapphire.webp');
                }
                if ($package->name == "Lebih Hemat") {
                    $thumbnail = asset('images/thumbnail_umrah_ruby.webp');
                }
                $product->image_thumbnail = $thumbnail;
            }

            $product->destinations = $destinations;

            $product->currency = ($product->currency == 'IDR') ? 'Rp' : '$';
            if (!\App::environment('production')) {
                $product->image_icon = StorageAttributes::getTempUrl($product->image_icon ?? null);
            }
        }

        return $products;
    }

    public function batchProducts()
    {
        $response = array(
            'productHemat' => $this->getProductByCategory("1"),
            'productNyaman' => $this->getProductByCategory("2_5"),
            'productUstadzSalim' => $this->getProductByCategory("3")
        );
        return response()->json($response);
    }

    public function updateAllPackages(Request $request){
        $other = OtherPackage::whereNull('category')->get();
        if($request->size != 'size'){
            if(count($other) > 0){
                foreach($other as $otp){
                    $otherPackages = OtherPackage::findOrFail($otp->id);
                    $otherPackages->category = 'all';
                    $otherPackages->save();
                }
            }
        }
        $response = array(
            'size' => count($other)
        );
        return response()->json($response);
    }

    public function allProducts()
    {
        $products = DB::table('web_products')->where('status', 1)->where('web_products.deleted', 0)->get();

        $response = array();
        foreach ($products as $value) {
            $subcategory = 'umroh-lebih-hemat/';
            $category = 'umroh/';
            if ($value->sub_category_id == 1) {
                $category = 'umroh/';
                $subcategory = 'umroh-lebih-hemat/';
            }
            if ($value->sub_category_id == 2) {
                $category = 'umroh/';
                $subcategory = 'umroh-lebih-nyaman/';
            }
            if ($value->sub_category_id == 3) {
                $category = 'umroh/';
                $subcategory = 'umroh-bersama-ustadz-h-salim-a-fillah/';
            }
            if ($value->sub_category_id == 4) {
                $category = 'umroh/';
                $subcategory = 'umroh-plus/';
            }
            if ($value->sub_category_id == 5) {
                $category = 'haji/';
                $subcategory = '';
            }
            if ($value->sub_category_id == 6) {
                $category = 'haji/';
                $subcategory = '';
            }
            if ($value->sub_category_id == 7 || $value->sub_category_id == 8) {
                $category = 'wisata-halal/';
                $subcategory = '';
            }

            $response[] = $category . $subcategory . $value->slug;
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  WebProduct  $product
     * @return \Illuminate\Http\Response
     */
    public function show(WebProduct $product)
    {
        return response()->json(
            $product
                ->load(['sub_category', 'sub_category.category', 'trip', 'trip.packages', 'images'])
                ->toArray()
        );
    }

    public function productDetail($slug, Request $request)
    {
        // $category = WebSubcategory::where('slug', $request->category)->first()->id ?? null;
        $query = WebProduct::leftJoin('web_sub_categories', 'web_sub_categories.id', 'web_products.sub_category_id')
            ->leftJoin('package_umroh_trips', 'package_umroh_trips.id', 'web_products.package_type')
            ->join('airlines', 'airlines.id', 'web_products.flights')
            ->join('umroh_trips', 'umroh_trips.id', 'web_products.trip_id')
            ->select('web_products.*', 'package_umroh_trips.name as package_name', 'web_sub_categories.name as sub_category_name', 'airlines.logo as airline_logo', 'airlines.name as airline_name', 'umroh_trips.itinerary_category_id')
            ->where('web_products.slug', $slug);
            // ->where('web_products.sub_category_id', $category);
        $product = $query->first();

        if ($product != null) {
            $departure_dates = DB::table('web_products')->select('departure_date', 'slug','price_start_from')->where('sub_category_id', $product->sub_category_id)->whereDate('departure_date', '>', now())->orderBy('departure_date', 'asc')->get();
            $product->departure_dates = $departure_dates;
            $package = DB::table('packages')->where('name', $product->package_name)->whereNull('deleted_at')->first();
            $product->itineraries = DB::table('itineraries')->select('title','day_title','description')->where('status', 1)->whereNull('deleted_at')->whereNotNull('category_id')->where('category_id', $product->itinerary_category_id)->get();
            $product->facilities = DB::table('facilities')->select('title', 'image_icon')->where('package_type', $package->id)->whereNull('deleted_at')->get();
            $product->images = DB::table('web_product_images')->where('web_product_id', $product->id)->get();
        }

        return response()->json($product);
    }

    public function relatedProducts()
    {
        return $this->getRelatedProducts();
    }

    public function relatedProductDates()
    {
        return $this->getRelatedProducts('date_only');
    }

    public function getRelatedProducts($isDateOnly = '')
    {
        $orderBy = request()->query('sortBy', 'departure_date');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $query = DB::table('web_products');

        $query->select([
            'web_products.id','web_products.name','web_products.title','web_products.slug','web_products.description','web_products.flights','web_products.image_thumbnail',
            'web_products.alt_image_thumbnail', 'web_products.title_image_thumbnail', 'web_products.price_start_from', 'web_products.package_price', 'web_products.trip_id', 'web_products.departure_date',
            'packages.image_icon', 'umroh_trips.total_days', 'umroh_trips.location_destination_id', 'web_sub_categories.slug as sub_category_slug', 'web_products.package_type',
            'web_products.sub_category_id', 'airlines.logo as airline_logo', 'airlines.name as airline_name', 'umroh_trips.ustadz'
        ]);

        $query->join('airlines', 'airlines.id', 'web_products.flights')
        ->join('web_sub_categories', 'web_sub_categories.id', 'web_products.sub_category_id')
        ->join('umroh_trips', 'umroh_trips.id', 'web_products.trip_id')
        ->leftJoin('package_umroh_trips', 'package_umroh_trips.id', 'web_products.package_type')
        ->leftJoin('packages', 'packages.name', 'package_umroh_trips.name')
        ->where('web_products.status', 1)
        ->where('web_products.deleted', 0)
        ->whereDate('web_products.departure_date', '>=', now());

        if (!empty(request()->query('categoryId'))) {
            $query->whereHas('sub_category', function ($query) {
                $query->where('category_id', request()->categoryId);
            });
        }

        if (!empty(request()->query('subcategoryId'))) {
            if (request()->query('subcategoryId') == "2_5") {
                $query->whereIn('web_products.sub_category_id', array(2, 5));
            } else if (request()->query('subcategoryId') == "7_8") {
                $query->whereIn('web_products.sub_category_id', array(7, 8, 10, 11));
            } else {
                $query->where('web_products.sub_category_id', request()->query('subcategoryId'));
            }
        }

        if (!empty(request()->query('departureDate'))) {
            $departureDate = Carbon::parse(request()->query('departureDate'))->format('Y-m-d');
            $query->where('departure_date', $departureDate);
        }

        if (!empty(request()->query('packageType'))) {
            $packageTypes = explode(",", request()->query('packageType'));
            $query->where(function ($queries) use ($packageTypes) {
                foreach ($packageTypes as $packageType) {
                    $queries->orWhere('package_umroh_trips.name', 'like', $packageType);
                }
            });
        }

        if (!empty(request()->query('productId'))) {
            $query->where('web_products.id', '<>', request()->query('productId'));
        }

        $products = $query->orderBy($orderBy, $sortBy)
        ->limit(6)->get();

        if($isDateOnly != 'date_only') {
            foreach ($products as $product) {
                $package = DB::table('package_umroh_trips')->select([
                    DB::raw('MAX(GREATEST(star_hotel_makkah::integer, star_hotel_madinah::integer)) as star'),
                    DB::raw('MIN(LEAST(star_hotel_makkah::integer, star_hotel_madinah::integer)) as low_star')
                ])
                ->where('id', $product->package_type)
                ->first();
                $product->star = $package->star ?? 3;
                $product->low_star = $package->low_star ?? 3;

                $hotel = DB::table('hotel_umroh_trips')->select([
                    DB::raw('MAX(GREATEST(star::integer)) as star'),
                    DB::raw('MIN(LEAST(star::integer)) as low_star')
                ])->where('umroh_trip_id', $product->trip_id)->first();

                if ($hotel->star) {
                    $product->star = $hotel->star ?? 3;
                    $product->low_star = $hotel->low_star ?? 3;
                }

                $destinations = "";
                if ($product->location_destination_id) {
                    $location_ids = Str::replace('[', '', $product->location_destination_id);
                    $location_ids = Str::replace(']', '', $location_ids);
                    $location_ids = explode(',', $location_ids);
                    if(!$location_ids) {
                        $destinations = City::select(['name'])
                        ->whereIn('id', $location_ids)
                        ->pluck('name');
                    }
                }

                if ($product->image_thumbnail == null) {
                    $thumbnail = asset('images/thumbnail_umrah_ruby.webp');
                    $package = DB::table('package_umroh_trips')->select('name')->where('id', $product->package_type)->first();
                    if ($package->name == "Ruby") {
                        $thumbnail = asset('images/thumbnail_umrah_ruby.webp');
                    }
                    if ($package->name == "Emerald") {
                        $thumbnail = asset('images/thumbnail_umrah_emerald.webp');
                    }
                    if ($package->name == "Sapphire") {
                        $thumbnail = asset('images/thumbnail_umrah_sapphire.webp');
                    }
                    if ($package->name == "Lebih Hemat") {
                        $thumbnail = asset('images/thumbnail_umrah_ruby.webp');
                    }
                    $product->image_thumbnail = $thumbnail;
                }

                $product->destinations = $destinations;
            }
        }
        return response()->json($products);
    }

    public function otherPackages()
    {
        $products = OtherPackage::tableSearch()
            ->orderBy('order', 'asc')
            ->where('publish_status', 1)
            ->where('category', 'all')
            ->limit(12)->get();
        return response()->json($products);
    }

    public function otherPackagesCategory(Request $request)
    {
        $products = OtherPackage::where('category', $request->get('category'))->orderBy('order', 'ASC')->limit(12)->get();
        return response()->json($products);
    }

    public function postData(Request $request)
    {
        if($request->product_wisata_halal) {
            $update = array();
            if($request->alt_image_thumbnail)
                $update['alt_image_thumbnail'] = $request->alt_image_thumbnail;
            if($request->title_image_thumbnail)
                $update['title_image_thumbnail'] = $request->title_image_thumbnail;
            if($request->name){
                $update['product_name'] = $request->name;
            }
            if($request->hotel_star){
                $update['hotel_star'] = $request->hotel_star;
            }
            if($request->total_days){
                $update['total_days'] = (int) $request->total_days;
            }
            if($request->price_start_from){
                $update['change_price_start_from'] = $request->price_start_from;
            }
            if ($request->hasFile('image')) {
                $imageMake = Image::make($request->file('image'));

                $img =  (string) $imageMake->encode('webp');

                $profilePhotoPath =  WebProductImage::DIR_IMAGE . pathinfo($request->file('image')->hashName(), PATHINFO_FILENAME) . '.webp';

                Storage::put($profilePhotoPath, $img);
                $update['image_thumbnail'] = $profilePhotoPath;
            }

            $products = WebProduct::where('sub_category_id', $request->id)->get();
            foreach ($products as $item) {
                $item->update($update);
            }

            if($request->slug) {
                $slug = $request->slug;
                $checkExistSlug = WebSubcategory::whereNot('id', $request->id)->where('slug', $slug)->count();
                if ($checkExistSlug > 0) {
                    $slug = $slug . "-" . $checkExistSlug + 1;
                }
                $sub_category = WebSubcategory::find($request->id);
                DB::table('web_content_settings')
                ->where('page', 'wisata-halal-'.$sub_category->slug)
                ->update(['page'=>'wisata-halal-'.$slug]);
                $sub_category->update(['slug' => $request->slug]);
            }

            try {
                $paths = ['/*'];
                $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
            } catch (\Throwable $th) {
                //
            }

            return response()->json([
                'success' => true
            ]);
        }

        $product = WebProduct::find($request->id);
        $product->update([
            ''.$request->section.'' => $request->content
        ]);

        return response()->json($product);
    }

    public function halalTourProducts() {
        $orderBy = request()->query('sortBy', 'price_start_from::int');
        if(request()->query('sortBy') == 'price_start_from' && request()->query('sortDesc') == 'false'){
            $orderBy = 'price_start_from::int';
        }

        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage', 10);

        $query = DB::table('web_sub_categories')->select([
            'web_sub_categories.id','web_sub_categories.name','web_sub_categories.slug'
        ])
        ->whereIn('web_sub_categories.id', array(7, 8, 10, 11));
        $query->groupByRaw('web_sub_categories.id');
        $products = $query->paginate($perPage)
        ->withQueryString()
        ->withPath(self::SPA_PATH);

        foreach ($products as $key => $product) {
            $webProduct = DB::table('web_products')
            ->select(['name','umroh_trips.title','package_type','trip_id','umroh_trips.location_destination_id','web_products.flights',
            'web_products.image_thumbnail','web_products.alt_image_thumbnail','web_products.title_image_thumbnail','web_products.price_start_from','web_products.package_price',
            'umroh_trips.total_days','umroh_trips.ustadz','departure_at', 'web_products.total_days as web_total_days',
            'web_products.hotel_star as hotel_star', 'web_products.change_price_start_from', 'web_products.product_name'])
            ->join('umroh_trips', 'umroh_trips.id', 'web_products.trip_id')
            ->where('web_products.sub_category_id', $product->id)->where('departure_date', '>', now())
            ->where('web_products.status', 1)
            ->where('web_products.deleted', 0)
            ->orderByRaw($orderBy ." ". $sortBy)->first();
            if($webProduct) {
                $product->ustadz = $webProduct->ustadz??'-';
                $product->total_days = $webProduct->total_days;

                $product->flights = $webProduct->flights;
                $product->alt_image_thumbnail = $webProduct->alt_image_thumbnail;
                $product->title_image_thumbnail = $webProduct->title_image_thumbnail;
                $product->price_start_from = intval($webProduct->price_start_from);
                $product->package_price = intval($webProduct->package_price);
                $product->departure_at = $webProduct->departure_at;

                $package = DB::table('package_umroh_trips')->select([
                    DB::raw('MAX(GREATEST(star_hotel_makkah::integer, star_hotel_madinah::integer)) as star'),
                    DB::raw('MIN(LEAST(star_hotel_makkah::integer, star_hotel_madinah::integer)) as low_star')
                ])
                ->where('id', $webProduct->package_type)
                ->first();
                $product->star = $package->star ?? 3;
                $product->low_star = $package->low_star ?? 3;

                $hotel = DB::table('hotel_umroh_trips')->select([
                    DB::raw('MAX(GREATEST(star::integer)) as star'),
                    DB::raw('MIN(LEAST(star::integer)) as low_star')
                ])->where('umroh_trip_id', $webProduct->trip_id)->first();

                if ($hotel->star) {
                    $product->star = $hotel->star ?? 3;
                    $product->low_star = $hotel->low_star ?? 3;
                }

                $destinations = "";
                if ($webProduct->location_destination_id) {
                    $location_ids = Str::replace('[', '', $webProduct->location_destination_id);
                    $location_ids = Str::replace(']', '', $location_ids);
                    $location_ids = explode(',', $location_ids);
                    if(!$location_ids) {
                        $destinations = City::select(['name'])
                        ->whereIn('id', $location_ids)
                        ->pluck('name');
                    }
                }

                $product->image_thumbnail = $webProduct->image_thumbnail;
                if ($webProduct->image_thumbnail == null) {
                    $thumbnail = asset('images/thumbnail_umrah_ruby.webp');
                    $package = DB::table('package_umroh_trips')->select('name')->where('id', $webProduct->package_type)->first();
                    if ($package->name == "Ruby") {
                        $thumbnail = asset('images/thumbnail_umrah_ruby.webp');
                    }
                    if ($package->name == "Emerald") {
                        $thumbnail = asset('images/thumbnail_umrah_emerald.webp');
                    }
                    if ($package->name == "Sapphire") {
                        $thumbnail = asset('images/thumbnail_umrah_sapphire.webp');
                    }
                    if ($package->name == "Lebih Hemat") {
                        $thumbnail = asset('images/thumbnail_umrah_ruby.webp');
                    }
                    $product->image_thumbnail = $thumbnail;
                }

                // OPTIONAL WEB UPDATED
                $product->option_total_days = (int) $webProduct->total_days;
                $product->option_hotel_star = $product->star;
                $product->option_price_start_from = $product->price_start_from;
                $product->option_product_name = $product->name;
                if(!empty($webProduct->web_total_days)){
                    $product->option_total_days = $webProduct->web_total_days;
                }
                if(!empty($webProduct->hotel_star)){
                    $product->option_hotel_star = $webProduct->hotel_star;
                }
                if(!empty($webProduct->web_total_days)){
                    $product->option_price_start_from = (int) $webProduct->change_price_start_from;
                }
                if(!empty($webProduct->product_name)){
                    $product->option_product_name = $webProduct->product_name;
                }

                $product->destinations = $destinations;

                $product->destination = DB::table('web_content_settings')->where('page', 'wisata-halal-'.$product->slug)->where('section', 'section1-a')->first()->title ?? '';
            } else {
                unset($products[$key]);
            }
        }

        $sortedResult = $products->getCollection();
        $sortedResult = $sortedResult->sortBy('price_start_from')->values();
        if($orderBy == 'price_start_from' && $sortBy == 'asc') {
            $sortedResult = $sortedResult->sortBy('price_start_from')->values();
        }
        if($orderBy == 'price_start_from' && $sortBy == 'desc') {
            $sortedResult = $sortedResult->sortByDesc('price_start_from')->values();
        }
        if($orderBy == 'departure_date' && $sortBy == 'asc') {
            $sortedResult = $sortedResult->sortBy('departure_at')->values();
        }
        $products->setCollection($sortedResult);
        return response()->json($products);
    }

    public function hajiProducts() {
        $products = $this->getContentProductByCategory(2);

        return response()->json($products);
    }

    public function umrohProducts() {
        $response = array(
            'productHemat' => $this->getContentProductByCategory(1, 1),
            'productNyaman' => $this->getContentProductByCategory(1, 2),
            'productUstadzSalim' => $this->getContentProductByCategory(1, 3)
        );

        return response()->json($response);
    }

    private function getContentProductByCategory($category, $subcategory=null) {
        $query = WebContentProduct::select([
            'web_content_products.*', 'packages.image_icon', 'packages.name as package_name', 'web_categories.name as category_name', 'web_categories.slug as category_slug', 'web_sub_categories.name as subcategory_name', 'web_sub_categories.slug as subcategory_slug'
        ])->leftjoin('packages', 'packages.id', 'web_content_products.package_id')
        ->leftjoin('web_categories', 'web_categories.id', 'web_content_products.category_id')
        ->leftjoin('web_sub_categories', 'web_sub_categories.id', 'web_content_products.sub_category_id');

        if($category) {
            $query->where('web_content_products.category_id', $category);
        }
        if($subcategory) {
            $query->where('web_content_products.sub_category_id', $subcategory);
        }

        return $query->orderBy('web_content_products.order', 'asc')->get();
    }

    public function productCategories() {
        $query = DB::table('web_categories')->select([
            'web_categories.id','web_categories.name'
        ]);
        $response = $query->get();

        return response()->json($response);
    }

    public function productSubCategories() {
        $query = DB::table('web_sub_categories')->select([
            'web_sub_categories.id','web_sub_categories.name'
        ]);
        if(request()->category) {
            $query->where('category_id', request()->category);
        }
        $response = $query->get();

        return response()->json($response);
    }

    public function productPackages() {
        $query = DB::table('packages')->select([
            'packages.id','packages.name'
        ]);
        $response = $query->get();

        return response()->json($response);
    }

    public function productPackagesPost(Request $request){
        if(!empty($request->get('id'))){
            $otherPackages = OtherPackage::findOrFail($request->get('id'));
        }
        else{
            $otherPackages = new OtherPackage();
            $otherPackages->category = $request->get('category');
        }

        if ($request->hasFile('image')) {
            $imageMake = Image::make($request->file('image'));

            $img =  (string) $imageMake->encode('webp');

            $profilePhotoPath =  OtherPackage::DIR_IMAGE . pathinfo($request->file('image')->hashName(), PATHINFO_FILENAME) . '.webp';

            Storage::put($profilePhotoPath, $img);
            $otherPackages->image = $profilePhotoPath;
        }
        $otherPackages->alt_image = $request->get('alt_image');
        $otherPackages->title_image = $request->get('title_image');
        $otherPackages->package_name = $request->get('package_name');
        $otherPackages->order = $request->get('order');
        $otherPackages->url = $request->get('url');
        $otherPackages->marketing_description = $request->get('marketing_description');
        $otherPackages->save();

        
        return response()->json($otherPackages);
    }

    public function deleteProductPackagesPost($id){
        OtherPackage::where('id', $id)->delete();
    }

    public function postContentProduct(Request $request)
    {
        $request->validate([
            'description' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'currency' => 'required'
        ]);

        $currency = "IDR";
        if($request->currency == "Rp")
            $currency = "IDR";
        if($request->currency == "$")
            $currency = "USD";

        if(!isset($request->price_start_from)) {
            $productLower = UmrohTrip::getLowestPrice($request->sub_category_id, $request->package_id, $currency);
            if($productLower) {
                $request->merge([
                    'price_start_from' => $productLower
                ]);
            } else {
                throw new ErrorMessageException("Product dengan kategori tersebut belum tersedia, silahkan masukkan harga secara manual");
            }
        }

        if($request->hasFile('image')) {
            $imageMake = Image::make($request->file('image'));

            $img =  (string) $imageMake->encode('webp');

            $profilePhotoPath =  WebContentProduct::DIR_IMAGE . pathinfo($request->file('image')->hashName(), PATHINFO_FILENAME) . '.webp';

            Storage::put($profilePhotoPath, $img);
            $request['image_url'] = $profilePhotoPath;
        }

        $product = WebContentProduct::find($request->id);
        if($product) {
            $product->update($request->all());
        } else {
            WebContentProduct::create($request->all());
        }

        

        return response()->json($product);
    }

    public function deleteContentProduct(Request $request)
    {
        $product = WebContentProduct::find($request->id);
        if($product) {
            $product->delete();
        }

        

        return response()->json($product);
    }

    public function departureDates(Request $request)
    {
        $query = DB::table('umroh_trips')
        ->select('departure_at')
        ->join('web_products', 'web_products.trip_id', 'umroh_trips.id')
        ->join('package_umroh_trips', 'package_umroh_trips.umroh_trip_id', 'umroh_trips.id')
        ->join('web_sub_categories', 'web_sub_categories.id', 'package_umroh_trips.sub_category_id');
        
        if($request->date) {
            $dateXplode = explode('to', $request->date);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
            $query->whereBetween('departure_at', [$start, $end]);
        }
        if($request->category) {
            $query->where('web_sub_categories.slug', 'like', '%'.$request->category.'%');
        }
        if($request->package) {
            $query->where('package_umroh_trips.name', 'like', $request->package);
        }
        
        $departureDates = $query->whereNull('umroh_trips.deleted_at')
        ->where('umroh_trips.is_published', true)
        ->where('package_umroh_trips.is_published', true)
        ->where('web_products.status', 1)
        ->where('web_products.deleted', 0)
        ->where('umroh_trips.available_seats', '>', 0)
        ->whereDate('departure_at', '>', Carbon::now())
        ->groupBy('departure_at')
        ->orderBy('departure_at', 'asc')
        ->get();

        foreach ($departureDates as $value) {
            $queryPakcage = DB::table('package_umroh_trips')
            ->select(['umroh_trips.title','package_umroh_trips.umroh_trip_id','package_umroh_trips.id','name','room_single_price','room_double_price','room_triple_price','room_quad_price','room_queen_price'])
            ->join('umroh_trips', 'umroh_trips.id', 'package_umroh_trips.umroh_trip_id')
            ->where('umroh_trips.available_seats', '>', 0)
            ->where('departure_at', $value->departure_at);
            if($request->package) {
                $queryPakcage->where('package_umroh_trips.name', 'like', $request->package);
            }
            $packages = $queryPakcage->get();

            $package_prices = [];
            foreach ($packages as $package) {
                $arrPrice = [$package->room_quad_price, $package->room_triple_price, $package->room_double_price, $package->room_single_price, $package->room_queen_price];
                $package_price = 0;
                if(array_filter($arrPrice)) {
                    $package_price = min(array_filter($arrPrice));
                }
                $package_prices[] = $package_price;
                // $package->available_seats = UmrohTrip::getAvailableSeat($package->umroh_trip_id, $package->id);
            }

            $value->price_start_from = 0;
            if($package_prices) {
                $value->price_start_from = min($package_prices);
            }
            // $value->package = $packages;
        }

        return response()->json($departureDates);
    }

    public function downloadItinerary($name)
    {
        if($name == 'Haji Furoda') {
            $fileName = "Itinerary_Haji_Furoda.pdf";
            $file = storage_path('private_assets/images/Itinerary_Haji_Furoda.pdf');
        } else if($name == 'Haji Plus') {
            $fileName = "Itinerary_Haji_Plus.pdf";
            $file = storage_path('private_assets/images/Itinerary_Haji_Plus.pdf');
        } else {
            return response()->json(['nothing' => 'here'], 404);
        }

        $headers = array(
            'Content-Type: application/pdf',
        );

        return response()->download($file, $fileName, $headers);
    }
}
