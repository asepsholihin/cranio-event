<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\WebProduct;
use App\Models\OtherPackage;
use App\Models\Package;
use App\Models\Itinerary;
use App\Models\Facility;
use App\Models\WebSubcategory;
use App\Models\WebProductImage;
use App\Http\Requests\Catalog\WebProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Image;
use Meema\CloudFront\Facades\CloudFront;

class WebProductSPAController extends Controller
{
    const SPA_PATH = '/catalog/products';

    public function __construct()
    {
        if (!request()->is('api/*')) {
            $this->middleware('permission:catalog-product-view')->only(['index']);
        }
        $this->middleware('permission:catalog-product-add-or-edit')->only(['store', 'destroy']);
    }

    /**
     * Display resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orderBy = request()->query('sortBy', 'departure_date');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage', 10);
        $products = WebProduct::tableSearchPublic()
            ->orderBy($orderBy, $sortBy)
            ->paginate($perPage)
            ->withQueryString()
            ->withPath(self::SPA_PATH);
        return response()->json($products);
    }

    public function allProducts()
    {
        $products = WebProduct::where('status', 1)->get();

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
     * Store a newly created resource in storage.
     *
     * @param  WebProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WebProductRequest $request)
    {
        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid
        ]);

        $product = WebProduct::updateOrCreate(
            ['id' => $request->get('id')],
            $request->only([
                'id',
                'sub_category_id',
                'trip_id',
                'package_type',
                'name',
                'title',
                'slug',
                'description',
                'flights',
                'price_start_from',
                'package_price',
                'status',
                'departure_date',
                'meta_title',
                'meta_keyword',
                'meta_description',
                'canonical',
                'created_by',
                'updated_by',
                'alt_image_background',
                'title_image_background',
                'alt_image_thumbnail',
                'title_image_thumbnail',
                'meta_index',
                'itinerary_description'
            ])
        );

        $this->storeFile($request, $product, ['image_thumbnail', 'image_background', 'image_itinerary', 'product_images']);

        if ($request->product_images) {
            foreach (json_decode($request->product_images) as $key => $value) {
                $img =  (string) Image::make(file_get_contents($value))->encode('webp');
                $photoPath =  WebProductImage::DIR_IMAGE . "product-image-".time() . '.webp';
                $storeImage = Storage::put($photoPath, $img);

                WebProductImage::create([
                    'web_product_id' => $product->id,
                    'image_url' => $photoPath,
                    'type' => 'slider',
                    'status' => 1,
                    'alt_image' => $product->slug,
                    'title_image' => $product->title,
                    'created_by' => $uid,
                    'updated_by' => $uid,
                ]);
            }
        }

        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }

        return response()->json(WebProduct::latest()->first());
    }

    /**
     * Store file.
     *
     * @param  WebProductRequest  $request
     * @param  WebProduct  $setting
     * @param  array  $keys
     * @return bool
     */
    private function storeFile(WebProductRequest $request, WebProduct $product, array $keys = [])
    {
        $files = [];

        foreach ($keys as $key) {
            if ($request->hasFile($key)) {
                $img =  (string) Image::make($request->file($key))->encode('webp');
                $photoPath =  WebProduct::DIR_FILE . pathinfo($request->file($key)->hashName(), PATHINFO_FILENAME) . '.webp';
                $storeImage = Storage::put($photoPath, $img);
                $files = array_merge($files, [$key => $photoPath]);
            }
        }

        return !empty($files)
            ? $product->update($files)
            : true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  WebProduct  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(WebProduct $product)
    {
        $product->delete();

        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }
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
            ->select('web_products.*', 'package_umroh_trips.name as package_name', 'web_sub_categories.name as sub_category_name')
            ->where('web_products.slug', $slug);
            // ->where('web_products.sub_category_id', $category);
        $product = $query->first();

        if ($product != null) {
            $departure_dates = WebProduct::select('departure_date', 'slug','price_start_from')->where('sub_category_id', $product->sub_category_id)->whereDate('departure_date', '>', now())->orderBy('departure_date', 'asc')->get();
            $product->departure_dates = $departure_dates;
            $package = Package::where('name', $product->package_name)->first();
            $product->itineraries = Itinerary::select('title')->where('package_type', $package->id)->get();
            $product->facilities = Facility::select('title', 'image_icon')->where('package_type', $package->id)->get();
            $product->images = WebProductImage::where('web_product_id', $product->id)->get();
        }

        return response()->json($product);
    }

    public function relatedProducts()
    {
        $orderBy = request()->query('sortBy', 'departure_date');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $products = WebProduct::tableSearchPublic()
            ->orderBy($orderBy, $sortBy)
            ->limit(12)->get();
        return response()->json($products);
    }

    public function otherPackages()
    {
        $products = OtherPackage::tableSearch()
            ->orderBy('order', 'asc')
            ->limit(12)->get();
        return response()->json($products);
    }

    public function action(Request $request)
    {

        if ($request->has('noindex')) {
            $getWebProWebProducts = WebProduct::whereIn('id', $request->ids)->get();
            foreach ($getWebProWebProducts as $key => $webProduct) {
                $metaIndex = json_decode($webProduct->meta_index);
                if (is_array($metaIndex)) {
                    array_push($metaIndex, 'noindex');
                } else {
                    $metaIndex[] = 'noindex';
                }
                $webProduct->meta_index = array_unique($metaIndex);
                $webProduct->save();
            }
            return;
        }

        if ($request->has('nofollow')) {
            $getWebProWebProducts = WebProduct::whereIn('id', $request->ids)->get();
            foreach ($getWebProWebProducts as $key => $webProduct) {
                $metaIndex = json_decode($webProduct->meta_index);
                if (is_array($metaIndex)) {
                    array_push($metaIndex, 'nofollow');
                } else {
                    $metaIndex[] = 'nofollow';
                }
                $webProduct->meta_index = array_unique($metaIndex);
                $webProduct->save();
            }
            return;
        }

        if ($request->has('noimageindex')) {
            $getWebProWebProducts = WebProduct::whereIn('id', $request->ids)->get();
            foreach ($getWebProWebProducts as $key => $webProduct) {
                $metaIndex = json_decode($webProduct->meta_index);
                if (is_array($metaIndex)) {
                    array_push($metaIndex, 'noimageindex');
                } else {
                    $metaIndex[] = 'noimageindex';
                }
                $webProduct->meta_index = array_unique($metaIndex);
                $webProduct->save();
            }
            return;
        }

        if ($request->has('noarchive')) {
            $getWebProWebProducts = WebProduct::whereIn('id', $request->ids)->get();
            foreach ($getWebProWebProducts as $key => $webProduct) {
                $metaIndex = json_decode($webProduct->meta_index);
                if (is_array($metaIndex)) {
                    array_push($metaIndex, 'noarchive');
                } else {
                    $metaIndex[] = 'noarchive';
                }
                $webProduct->meta_index = array_unique($metaIndex);
                $webProduct->save();
            }
            return;
        }

        if ($request->has('nosnippet')) {
            $getWebProWebProducts = WebProduct::whereIn('id', $request->ids)->get();
            foreach ($getWebProWebProducts as $key => $webProduct) {
                $metaIndex = json_decode($webProduct->meta_index);
                if (is_array($metaIndex)) {
                    array_push($metaIndex, 'nosnippet');
                } else {
                    $metaIndex[] = 'nosnippet';
                }
                $webProduct->meta_index = array_unique($metaIndex);
                $webProduct->save();
            }
            return;
        }

        if ($request->has('delete')) {
            $getWebProWebProducts = WebProduct::whereIn('id', $request->ids)->delete();
            return;
        }

        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }
    }

    public function deleteImage(Request $request)
    {
        WebProductImage::find($request->id)->delete();

        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }

        return;
    }
}
