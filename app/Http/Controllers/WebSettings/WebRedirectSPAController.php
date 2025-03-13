<?php

namespace App\Http\Controllers\WebSettings;

use App\Http\Controllers\Controller;
use App\Models\WebRedirectUrl;
use Illuminate\Http\Request;
use Meema\CloudFront\Facades\CloudFront;

class WebRedirectSPAController extends Controller
{
    const SPA_PATH = '/web-redirect';

    public function __construct()
    {
        if (! request()->is('api/*')) {
            $this->middleware('permission:web-settings-view')->only(['redirect_urls']);
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
        $categories = WebRedirectUrl::tableSearch()
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
        $statusValue = 0;
        if ($request->status == "true") {
            $statusValue = 1;
        }
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'status' => $statusValue
        ]);

        $web_redirect = WebRedirectUrl::updateOrCreate(
            ['id' => $request->get('id')],
            $request->all()
        );

        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }

        return response()->json($web_redirect);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  WebRedirectUrl  $web_redirect
     * @return \Illuminate\Http\Response
     */
    public function destroy(WebRedirectUrl $web_redirect)
    {
        $web_redirect->delete();
    }

    /**
     * Display the specified resource.
     *
     * @param  WebRedirectUrl  $web_redirect
     * @return \Illuminate\Http\Response
     */
    public function show(WebRedirectUrl $web_redirect)
    {
        return response()->json($web_redirect->toArray());
    }

    public function redirects()
    {
        $urls = WebRedirectUrl::where('status', 1)->orderBy('id', 'asc')->get();
        
        return response()->json($urls);
    }
}
