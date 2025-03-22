<?php

namespace App\Http\Controllers\ImageSlider;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageSlider\StoreWebItineraryRequest;
use App\Models\WebItinerary;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;


class WebItinerarySPAController extends Controller
{
    const SPA_PATH = '/images-slider/itinerary';

    public function __construct()
    {
        if (!request()->is('api/*')) {
            $this->middleware('permission:images-slider-view')->only(['index', 'show', 'queryWebItinerary']);
            $this->middleware('permission:images-slider-add-or-edit')->only(['store', 'import']);
        }
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
            WebItinerary::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreWebItineraryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWebItineraryRequest $request)
    {
        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid
        ]);

        WebItinerary::updateOrCreate(['id' => $request->get('id')], $request->except(['image']));

        
    }

    /**
     * Display the specified resource.
     *
     * @param  WebItinerary  $itinerary
     * @return \Illuminate\Http\Response
     */
    public function show(WebItinerary $itinerary)
    {
        return response()->json($itinerary->toArray());
    }

    public function destroy(WebItinerary $itinerary)
    {
        $itinerary->delete();
    }

    public function queryWebItinerary(Request $request)
    {
        $request->validate(['q' => 'required']);

        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = WebItinerary::select(['id', 'image_url', 'package_duration', 'notes'])
            ->where('notes', 'like', $search)
            ->orWhere('package_duration', 'like', $search)
            ->limit(10)
            ->get();
        return response()->json($result);
    }

    public function getList(Request $request, $category, $subcategory){
        $data = WebItinerary::where('category_id', $category)->where('subcategory_id', $subcategory);
        if(!empty($request->limit)){
            $data = $data->limit($request->limit);
        }
        $data = $data->get();
        return response()->json($data);
    }
}
