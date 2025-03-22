<?php

namespace App\Http\Controllers\ImageSlider;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageSlider\StoreWebPartnerRequest;
use App\Models\WebPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;


class WebPartnerSPAController extends Controller
{
    const SPA_PATH = '/images-slider/partner';

    public function __construct()
    {
        if (!request()->is('api/*')) {
            $this->middleware('permission:images-slider-view')->only(['index','show','queryWebPartner']);
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
            WebPartner::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreWebPartnerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWebPartnerRequest $request)
    {
        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid
        ]);

        WebPartner::updateOrCreate(['id' => $request->get('id')], $request->except(['image']));
        

    }

    /**
     * Display the specified resource.
     *
     * @param  WebPartner  $webWhyus
     * @return \Illuminate\Http\Response
     */
    public function show(WebPartner $partner)
    {
        return response()->json($partner->toArray());
    }

    public function destroy(WebPartner $partner)
    {
        $partner->delete();
    }

    public function queryWebPartner(Request $request)
    {
        $request->validate(['q' => 'required']);

        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = WebPartner::select(['id','image_url','order','url'])
            ->where('url',  $request->get('q'))
            ->orWhere('order', 'like', $search)
            ->limit(10)
            ->get();
        return response()->json($result);
    }
}
