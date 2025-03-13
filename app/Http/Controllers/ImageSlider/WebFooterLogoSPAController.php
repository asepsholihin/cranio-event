<?php

namespace App\Http\Controllers\ImageSlider;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageSlider\StoreWebFooterLogoRequest;
use App\Models\WebFooterLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Meema\CloudFront\Facades\CloudFront;
use Meema\CloudFront\Jobs\InvalidateCache;

class WebFooterLogoSPAController extends Controller
{
    const SPA_PATH = '/images-slider/footer-logo';

    public function __construct()
    {
        if (!request()->is('api/*')) {
            $this->middleware('permission:images-slider-view')->only(['index','show','queryWebFooterLogo']);
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
            WebFooterLogo::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreWebFooterLogoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWebFooterLogoRequest $request)
    {
        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid
        ]);

        WebFooterLogo::updateOrCreate(['id' => $request->get('id')], $request->except(['image']));

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
     * @param  WebFooterLogo  $footer_logo
     * @return \Illuminate\Http\Response
     */
    public function show(WebFooterLogo $footer_logo)
    {
        return response()->json($footer_logo->toArray());
    }

    public function destroy(WebFooterLogo $footer_logo)
    {
        $footer_logo->delete();
    }

    public function queryWebFooterLogo(Request $request)
    {
        $request->validate(['q' => 'required']);

        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = WebFooterLogo::select(['id','image_url','order','url'])
            ->where('url', 'like', $search)
            ->orWhere('order', 'like', $search)
            ->limit(10)
            ->get();
        return response()->json($result);
    }
}
