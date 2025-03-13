<?php

namespace App\Http\Controllers\ImageSlider;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageSlider\StoreWebWhyusRequest;
use App\Models\WebWhyus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Meema\CloudFront\Facades\CloudFront;
use Meema\CloudFront\Jobs\InvalidateCache;

class WebWhyusSPAController extends Controller
{
    const SPA_PATH = '/images-slider/whyus';

    public function __construct()
    {
        if (!request()->is('api/*')) {
            $this->middleware('permission:images-slider-view')->only(['index','show','queryWebWhyus']);
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
            WebWhyus::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreWebWhyusRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWebWhyusRequest $request)
    {
        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid
        ]);

        WebWhyus::updateOrCreate(['id' => $request->get('id')], $request->except(['image']));

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
     * @param  WebWhyus  $webWhyus
     * @return \Illuminate\Http\Response
     */
    public function show(WebWhyus $whyu)
    {
        return response()->json($whyu->toArray());
    }

    public function destroy(WebWhyus $whyu)
    {
        $whyu->delete();
    }

    public function queryWebWhyus(Request $request)
    {
        $request->validate(['q' => 'required']);

        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = WebWhyus::select(['id','image_url','order','title','url'])
            ->where('title',  $request->get('q'))
            ->orWhere('url', 'like', $search)
            ->limit(10)
            ->get();
        return response()->json($result);
    }
}
