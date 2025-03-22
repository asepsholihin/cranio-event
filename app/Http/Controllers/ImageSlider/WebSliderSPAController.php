<?php

namespace App\Http\Controllers\ImageSlider;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageSlider\StoreWebSliderRequest;
use App\Models\WebSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class WebSliderSPAController extends Controller
{
    const SPA_PATH = '/images-slider/main-visual';

    public function __construct()
    {
        if (!request()->is('api/*')) {
            $this->middleware('permission:images-slider-view')->only(['index','show','queryWebSlider']);
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
            WebSlider::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreWebSliderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWebSliderRequest $request)
    {
        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid
        ]);

        WebSlider::updateOrCreate(['id' => $request->get('id')], $request->except(['image']));

        
    }

    /**
     * Display the specified resource.
     *
     * @param  WebSlider  $webSlider
     * @return \Illuminate\Http\Response
     */
    public function show(WebSlider $main_visual)
    {
        return response()->json($main_visual->toArray());
    }

    public function destroy(WebSlider $main_visual)
    {
        $main_visual->delete();
    }

    public function queryWebSlider(Request $request)
    {
        $request->validate(['q' => 'required']);

        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = WebSlider::select(['id','title','image_url','slider_url'])
            ->where('title',  $request->get('q'))
            ->orWhere('image_url', 'like', $search)
            ->orWhere('slider_url', 'like', $search)
            ->limit(10)
            ->get();
        return response()->json($result);
    }
}
