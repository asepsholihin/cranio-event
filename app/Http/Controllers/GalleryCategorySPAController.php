<?php

namespace App\Http\Controllers;

use App\Models\GalleryCategory;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use App\Models\GalleryContent;
use Illuminate\Support\Str;

class GalleryCategorySPAController extends Controller
{
    const SPA_PATH = '/gallery-category';

    public function __construct()
    {

        if (!request()->is('api/*')) {
            $this->middleware('permission:gallery-view')->only(['index','show','queryCategories','queryParentCategories']);
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
            GalleryCategory::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);

        $status_value = 0;
        if ($request->status == "true") {
            $status_value = 1;
        }

        $is_parent = 1;
        if ($request->parent_id) {
            $is_parent = 0;
        }

        $slug = Str::slug(strtolower($request->name));
        $existSlug = GalleryCategory::where('slug', $slug)->where('id', '<>', $request->get('id'))->count();
        if ($existSlug) {
            $slug = $slug . $existSlug;
        }

        $uid = auth()->user()->id;
        $request->merge([
            'slug' => $slug,
            'created_by' => $uid,
            'updated_by' => $uid,
            'is_parent' => $is_parent,
            'status' => $status_value
        ]);

        $request->all();

        GalleryCategory::updateOrCreate(['id' => $request->get('id')], $request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GalleryCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function show(GalleryCategory $galleryCategory)
    {
        return response()->json($galleryCategory->toArray());
    }

    public function destroy(GalleryCategory $galleryCategory)
    {
        $gallery = GalleryContent::firstWhere(['gallery_category_id' => $galleryCategory->id]);
        if ($gallery) {
            throw new ErrorMessageException('Kategori ini digunakan dalam ' . $gallery->title . '');
        }

        $galleryCategory->delete();
    }

    public function queryCategories(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = GalleryCategory::select(['id', 'name'])
            ->where('name',  $request->get('q'))
            ->orWhere('name', 'like', $search)
            ->get();
        return response()->json($result);
    }

    public function queryParentCategories(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = GalleryCategory::select(['id', 'name'])
            ->where('is_parent', 1)
            ->where(function ($query) use ($request, $search) {
                $query->where('name', $request->get('q'));
                $query->orWhere('name', 'like', $search);
            })
            ->get();
        return response()->json($result);
    }

    public function galleryCategories(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = GalleryCategory::select(['id', 'name', 'slug'])
            ->where('status', 1)
            ->where('name', 'like', $search)
            ->get();
        return response()->json($result);
    }
}
