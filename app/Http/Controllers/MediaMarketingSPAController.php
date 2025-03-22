<?php

namespace App\Http\Controllers;

use App\Models\MediaMarketing;
use App\Models\Mitra;
use App\Models\MediaMarketingImage;
use App\Http\Requests\StoreMediaMarketingRequest;
use App\Http\Requests\UpdateMediaMarketingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\File\PDF\MediaMarketingFlyer;
use SnappyImage;
use DB;

class MediaMarketingSPAController extends Controller
{
    const SPA_PATH = '/media-marketing';

    public function __construct()
    {
        $this->middleware('permission:mitra-media-view|media-marketing-view')->only(['index', 'show', 'mitraMedia', 'show', 'querysocialMedias']);
        $this->middleware('permission:media-marketing-add-or-edit')->only(['store', 'import']);
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
            MediaMarketing::tableSearch()
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
    public function store(StoreMediaMarketingRequest $request)
    {
        $request->merge([
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        if(is_array($request->file_image)) {
            $request->validate([
                'file_image.*' => 'file|mimes:jpg,png,webp|max:1536'
            ], [
                'file_image.uploaded' => 'Gambar terlalu besar, maximal ukuran 1.5MB'
            ]);
        } else {
            $request->validate([
                'file_image' => 'file|mimes:jpg,png,webp|max:1536'
            ], [
                'file_image.uploaded' => 'Gambar terlalu besar, maximal ukuran 1.5MB'
            ]);
        }

        DB::transaction(function() use($request) {
            $mediaMarketing = MediaMarketing::updateOrCreate(['id' => $request->get('id')], $request->except(['file_image']));

            if($request->images) {
                foreach ($request->images as $key => $value) {
                    MediaMarketingImage::create([
                        'media_marketing_id' => $mediaMarketing->id,
                        'order' => $key,
                        'image_url' => $value,
                    ]);
                }
            }
            if($request->images_order) {
                foreach (json_decode($request->images_order) as $image) {
                    MediaMarketingImage::find($image->id)->update([
                        'order' => $image->order
                    ]);
                }
            }
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MediaMarketing  $socialMedia
     * @return \Illuminate\Http\Response
     */
    public function show(MediaMarketing $media_marketing)
    {
        $media_marketing->images = MediaMarketingImage::where('media_marketing_id', $media_marketing->id)->orderBy('order', 'asc')->get();
        return response()->json($media_marketing->toArray());
    }

    public function destroy(MediaMarketing $media_marketing)
    {
        $media_marketing->delete();
    }

    public function queryMediaMarketing(Request $request)
    {
        $request->validate(['q' => 'required']);

        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = MediaMarketing::select(['id', 'image', 'tilte', 'category'])
            ->where('tilte',  $request->get('q'))
            ->orWhere('category', 'like', $search)
            ->limit(10)
            ->get();
        return response()->json($result);
    }

    public function mitraMedia(Request $request)
    {
        $orderBy = request()->query('sortBy', 'id');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage', 10);
        return response()->json(
            MediaMarketing::tableSearch()
                ->where('status', 1)
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    public function mitraMediaDownload($id)
    {
        $media = MediaMarketing::find($id);
        $mitra = Mitra::select('participant.id','name','no_hp','instagram','email')->join('participants', 'participant.id','mitra.participant_id')->where('user_id', auth()->user()->id)->first();
        // return view('image.mitra-media', compact(['media','mitra']))->render();
        return SnappyImage::setOption('width', 100)
        ->setOption('enable-local-file-access', true)
        ->setOption('encoding', 'utf-8')
        ->loadView('image.mitra-media',  compact(['media','mitra']))->inline();
    }

    public function deleteImage(Request $request)
    {
        $image = MediaMarketingImage::find($request->id);
        $image->delete();
    }

    public function previewFlyerPDF($id)
    {
        $mediaMarketing = MediaMarketing::find($id);
        
        return (new MediaMarketingFlyer($mediaMarketing))->stream();
    }

    public function downloadFlyerPDF($id)
    {
        $mediaMarketing = MediaMarketing::find($id);
        
        return (new MediaMarketingFlyer($mediaMarketing))->download();
    }
}
