<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebSubcategory;
use App\Models\PackageUmrohTrip;
use App\Http\Requests\Catalog\WebSubcategoryRequest;
use Meema\CloudFront\Facades\CloudFront;
use DB;

class WebSubcategorySPAController extends Controller
{
    const SPA_PATH = '/catalog/subcategories';

    public function __construct()
    {
        if (!request()->is('api/*')) {
            $this->middleware('permission:catalog-category-view')->only(['index']);
        }
        $this->middleware('permission:catalog-category-view')->only(['index']);
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
        $sub_categories = WebSubcategory::tableSearch()
            ->with(['category'])
            ->orderBy($orderBy, $sortBy)
            ->paginate($perPage)
            ->withQueryString()
            ->withPath(self::SPA_PATH);

        return response()->json($sub_categories);
    }

    public function getListSubCategories($id){
        $data = WebSubcategory::select('*', 'name as label')->where('category_id', $id)->get();
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  WebSubcategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WebSubcategoryRequest $request)
    {
        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid
        ]);

        if(request()->slug) {
            $sub_category = WebSubcategory::find($request->get('id'));
            $webContentPage = DB::table('web_content_settings')
            ->where('page', 'wisata-halal-'.$sub_category->slug)
            ->update(['page'=>'wisata-halal-'.request()->slug]);
        }

        $sub_category = WebSubcategory::updateOrCreate(
            ['id' => $request->get('id')],
            $request->only([
                'id',
                'category_id',
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

        return response()->json($sub_category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  WebSubcategory  $sub_category
     * @return \Illuminate\Http\Response
     */
    public function destroy(WebSubcategory $sub_category)
    {
        if (in_array($sub_category->id, array(1,2,3,4,5,6,7,8))) {
            return response()->json([
                'success' => false,
                'message'  => 'Kategori default tidak dapat di hapus',
            ], 422);
        }
        $checkRelation = PackageUmrohTrip::where('sub_category_id', $sub_category->id)->first();
        if($checkRelation) {
            return response()->json([
                'success' => false,
                'message'  => 'Sub Kategori terpasang pada Paket Umroh, tidak dapat di hapus',
            ], 422);
        }
        $sub_category->delete();
    }

    /**
     * Display the specified resource.
     *
     * @param  WebSubcategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function show(WebSubcategory $sub_category)
    {
        return response()->json(
            $sub_category
                ->load(['category'])
                ->toArray()
        );
    }

    public function subCategoriesSearch(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = WebSubcategory::select(['id','name'])->get();
        return response()->json($result);
    }
}
