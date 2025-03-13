<?php

namespace App\Http\Controllers;

use App\Models\SocialMedia;
use App\Http\Requests\StoreSocialMediaRequest;
use App\Http\Requests\UpdateSocialMediaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialMediaSPAController extends Controller
{
    const SPA_PATH = '/social-media';

    public function __construct()
    {
        $this->middleware('permission:web-settings-view')->only(['index', 'show', 'querysocialMedias', 'barcode']);
        $this->middleware('permission:web-settings-add-or-edit')->only(['store', 'import']);
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
            SocialMedia::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoresocialMediasRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSocialMediaRequest $request)
    {
        $request->merge([
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        SocialMedia::updateOrCreate(['id' => $request->get('id')], $request->except(['file_icon']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SocialMedia  $socialMedia
     * @return \Illuminate\Http\Response
     */
    public function show(SocialMedia $social_medium)
    {
        return response()->json($social_medium->toArray());
    }

    public function destroy(SocialMedia $social_medium)
    {
        $social_medium->delete();
    }

    public function querySocialMedia(Request $request)
    {
        $request->validate(['q' => 'required']);

        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = SocialMedia::select(['id', 'social_media_name', 'social_media_link', 'icon'])
            ->where('social_media_name',  $request->get('q'))
            ->orWhere('social_media_link', 'like', $search)
            ->limit(10)
            ->get();
        return response()->json($result);
    }
}
