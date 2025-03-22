<?php

namespace App\Http\Controllers\WebSettings;

use App\Http\Controllers\Controller;
use App\Models\WebNavbarSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class WebNavbarSPAController extends Controller
{
    const SPA_PATH = '/web-navbar';

    public function __construct()
    {
        if (!request()->is('api/*')) {
            $this->middleware('permission:web-settings-view')->only(['navbar']);
        }
        $this->middleware('permission:web-settings-add-or-edit')->only(['store', 'show', 'destroy']);
    }

    /**
     * Display resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orderBy = request()->query('sortBy', 'id');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage', 10);
        $categories = WebNavbarSetting::tableSearch()
            ->orderBy($orderBy, $sortBy)
            ->paginate($perPage)
            ->withQueryString()
            ->withPath(self::SPA_PATH);

        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $uid = auth()->user()->id;
        $showInMenuValue = 0;
        if ($request->show == "true") {
            $showInMenuValue = 1;
        }
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'show' => $showInMenuValue
        ]);

        $web_navbar = WebNavbarSetting::updateOrCreate(
            ['id' => $request->get('id')],
            $request->all()
        );

        

        return response()->json($web_navbar);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  WebNavbarSetting  $web_navbar
     * @return \Illuminate\Http\Response
     */
    public function destroy(WebNavbarSetting $web_navbar)
    {
        $web_navbar->delete();
    }

    /**
     * Display the specified resource.
     *
     * @param  WebNavbarSetting  $web_navbar
     * @return \Illuminate\Http\Response
     */
    public function show(WebNavbarSetting $web_navbar)
    {
        return response()->json($web_navbar->toArray());
    }

    public function queryNavbars(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = WebNavbarSetting::select(['id', 'title'])
            ->where('title', 'like', $search)
            ->get();
        return response()->json($result);
    }

    public function queryParentNavbars(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = DB::table('web_navbar_settings')->select(['id', 'title'])
            ->whereNull('parent_id')
            ->where('title', 'like', $search)
            ->get();
        return response()->json($result);
    }

    public function navbar()
    {
        $navbars = DB::table('web_navbar_settings')->select('id','title','url','parent_id','order','show')->where('show', 1)->whereNull('parent_id')->orderBy('order', 'asc')->get();
        foreach ($navbars as $navbar) {
            $children = DB::table('web_navbar_settings')->select('id','title','url','parent_id','order','show')->where('parent_id', $navbar->id)->where('show', 1)->whereNotNull('parent_id')->orderBy('order', 'asc')->get();
            $navbar->has_children = (count($children) > 0) ? true : false;
            $navbar->children = $children;
        }
        return response()->json($navbars);
    }

    public function postNavbar(Request $request)
    {
        if ($request->parent_id === 'null' || $request->parent_id === '') {
            $request->merge([
                'parent_id' => NULL
            ]);
        }
        
        WebNavbarSetting::updateOrCreate(['id' => $request->get('id')], $request->all());
    }
}
