<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebCategory;
use App\Models\UmrohTrip;
use App\Http\Requests\Catalog\WebCategoryRequest;
use Meema\CloudFront\Facades\CloudFront;

class WebCategorySPAController extends Controller
{
    const SPA_PATH = '/catalog/categories';

    public function __construct()
    {
        // if (! request()->is('api/*')) {
        //     $this->middleware('permission:catalog-category-view')->only(['index']);
        // }
        $this->middleware('permission:catalog-category-add-or-edit')->only(['store', 'destroy']);
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
        $categories = WebCategory::tableSearch()
            ->with(['sub_categories'])
            ->orderBy($orderBy, $sortBy)
            ->paginate($perPage)
            ->withQueryString()
            ->withPath(self::SPA_PATH);

        return response()->json($categories);
    }

    public function getAllCategories(){
        $data = WebCategory::select('*', 'name as label')->get();
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  WebCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WebCategoryRequest $request)
    {
        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid
        ]);

        $category = WebCategory::updateOrCreate(
            ['id' => $request->get('id')],
            $request->only([
                'id',
                'name',
                'slug',
                'description',
                'order',
                'active',
                'status',
                'created_by',
                'updated_by'
            ])
        );

        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }

        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  WebCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(WebCategory $category)
    {
        if (in_array($category->id, array(1,2,3))) {
            return response()->json([
                'success' => false,
                'message'  => 'Kategori default tidak dapat di hapus',
            ], 422);
        }
        $checkRelation = UmrohTrip::where('category_id', $category->id)->first();
        if($checkRelation) {
            return response()->json([
                'success' => false,
                'message'  => 'Kategori terpasang pada Umroh, tidak dapat di hapus',
            ], 422);
        }
        $category->delete();
    }

    /**
     * Display the specified resource.
     *
     * @param  WebCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function show(WebCategory $category)
    {
        return response()->json($category->toArray());
    }

    public function categoriesSearch(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = WebCategory::select(['id','name'])->get();
        return response()->json($result);
    }
}
