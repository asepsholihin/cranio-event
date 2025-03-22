<?php

namespace App\Http\Controllers;

use App\Models\GalleryContent;
use App\Models\GalleryCategory;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use Illuminate\Support\Str;
use Image;
use Illuminate\Support\Facades\Storage;



class GalleryContentSPAController extends Controller
{
    const SPA_PATH = '/gallery-content';

    public function __construct()
    {
        if (!request()->is('api/*')) {
            $this->middleware('permission:gallery-view')->only(['index', 'show', 'queryGalleries']);
        }
        $this->middleware('permission:gallery-add-or-edit')->only(['store']);
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
            GalleryContent::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    public function store(Request $request)
    {
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

        $status_value = 0;
        if ($request->status == "true") {
            $status_value = 1;
        }

        $uid = auth()->user()->id;
        $request->merge([
            'title' => $request->title ?? '',
            'created_by' => $uid,
            'updated_by' => $uid,
            'status' => $status_value
        ]);

        if(is_array($request->file_image)) {
            foreach($request->file_image as $file) {
                if ($request->hasFile(GalleryContent::IMAGE)) {
                    $imageMake = Image::make($file);
                    $img = (string) $imageMake
                        ->resize(1080, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })
                        ->encode('webp',100);

                    $profilePhotoPath =  GalleryContent::DIR_IMAGE . pathinfo($file->hashName(), PATHINFO_FILENAME) . '.webp';
                    Storage::put($profilePhotoPath, $img);
                    $request->merge(['image_url' => $profilePhotoPath]);
                }

                GalleryContent::updateOrCreate(['id' => $request->get('id')], $request->all());    
            }
        } else {
            if ($request->hasFile(GalleryContent::IMAGE)) {
                $imageMake = Image::make($request->file(GalleryContent::IMAGE));
                $img = (string) $imageMake
                    ->resize(1080, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->encode('webp',100);

                $profilePhotoPath =  GalleryContent::DIR_IMAGE . pathinfo($request->file(GalleryContent::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
                Storage::put($profilePhotoPath, $img);
                $request->merge(['image_url' => $profilePhotoPath]);
            }

            GalleryContent::updateOrCreate(['id' => $request->get('id')], $request->all());
        }

        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GalleryContent  $category
     * @return \Illuminate\Http\Response
     */
    public function show(GalleryContent $galleryContent)
    {
        return response()->json($galleryContent->toArray());
    }

    public function destroy(GalleryContent  $galleryContent)
    {
        $galleryContent->delete();

        
    }

    public function queryContents(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = GalleryContent::select(['id','title'])
            ->where('title',  $request->get('q'))
            ->orWhere('title', 'like', $search)
            ->get();
        return response()->json($result);
    }

    public function galleryContents(Request $request)
    {
        $query = GalleryContent::select(['id','gallery_category_id','title','image_url'])->where('status', 1);
        if($request->category) {
            $gallery_category_id = GalleryCategory::where('slug', $request->category)->first()->id ?? '';
            if($gallery_category_id) {
                $query->where('gallery_category_id', $gallery_category_id);
            }
        }
        if($request->q) {
            $query->where(function($q) {
                $search = request()->get('q');
                $search = '%' . $search .'%';
                $q->where('title', 'like', $search)
                ->orWhere('image_url', 'like', $search);
            });
        }
        $result = $query->get();
        return response()->json($result);
    }

    public function allGalleries()
    {
        $query = GalleryContent::where('status', 1);
        $galleries = $query->get();

        $response = array();
        foreach ($galleries as $value) {
            $response[] = 'artikel/' . $value->slug;
        }
        return response()->json($response);
    }

    public function relatedGalleries(Request $request)
    {
        $queryCount = GalleryContent::where('status', 1);
        $count = $queryCount->count();

        $randomCount = 1;
        if ($request->count <= $count) {
            $randomCount = $request->count;
        }

        $gallery = GalleryContent::where('status', 1);
        if ($request->exceptSlug !== "") {
            $gallery->whereNot('slug', $request->exceptSlug);
        }

        return response()->json(
            ($gallery->get()->isEmpty() == false) ? $gallery->get()->random($randomCount) : []
        );
    }

    public function featuredGallery()
    {
        $query = GalleryContent::where('status', 1);
        $galleries = $query->limit(5)->get();
        return response()->json($galleries);
    }
}
