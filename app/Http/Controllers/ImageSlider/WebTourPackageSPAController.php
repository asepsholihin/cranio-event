<?php

namespace App\Http\Controllers\ImageSlider;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageSlider\StoreWebTourPackageRequest;
use App\Models\WebTourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Meema\CloudFront\Facades\CloudFront;
use Meema\CloudFront\Jobs\InvalidateCache;

class WebTourPackageSPAController extends Controller
{
    const SPA_PATH = '/images-slider/tour-package';

    public function __construct()
    {
        if (!request()->is('api/*')) {
            $this->middleware('permission:images-slider-view')->only(['index','show','queryWebTourPackage']);
        }
        $this->middleware('permission:images-slider-add-or-edit')->only(['store','import']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orderBy = request()->query('sortBy', 'id');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage', 10);
        return response()->json(
            WebTourPackage::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreWebTourPackageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWebTourPackageRequest $request)
    {
        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid
        ]);

        WebTourPackage::updateOrCreate(['id' => $request->get('id')], $request->except(['image']));
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
     * @param  WebTourPackage  $tour_package
     * @return \Illuminate\Http\Response
     */
    public function show(WebTourPackage $tour_package)
    {
        return response()->json($tour_package->toArray());
    }

    public function destroy(WebTourPackage $tour_package)
    {
        $tour_package->delete();
    }

    public function queryWebTourPackage(Request $request)
    {
        $request->validate(['q' => 'required']);

        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = WebTourPackage::select(['id','image_url','order','url'])
            ->where('url',  $request->get('q'))
            ->orWhere('order', 'like', $search)
            ->limit(10)
            ->get();
        return response()->json($result);
    }
}
