<?php

namespace App\Http\Controllers\ImageSlider;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageSlider\StoreWebProgramRequest;
use App\Models\WebProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;


class WebProgramSPAController extends Controller
{
    const SPA_PATH = '/images-slider/program';

    public function __construct()
    {
        if (!request()->is('api/*')) {
            $this->middleware('permission:images-slider-view')->only(['index','show','queryWebProgram']);
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
            WebProgram::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreWebProgramRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWebProgramRequest $request)
    {
        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid
        ]);

        WebProgram::updateOrCreate(['id' => $request->get('id')], $request->except(['image']));
        
    }

    /**
     * Display the specified resource.
     *
     * @param  WebProgram  $webWhyus
     * @return \Illuminate\Http\Response
     */
    public function show(WebProgram $program)
    {
        return response()->json($program->toArray());
    }

    public function destroy(WebProgram $program)
    {
        $program->delete();
    }

    public function queryWebProgram(Request $request)
    {
        $request->validate(['q' => 'required']);

        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = WebProgram::select(['id','image_url','order','url'])
            ->where('url',  $request->get('q'))
            ->orWhere('order', 'like', $search)
            ->limit(10)
            ->get();
        return response()->json($result);
    }
}
