<?php

namespace App\Http\Controllers;

use App\Models\FaqCategory;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use App\Models\FaqContent;
use Illuminate\Support\Str;
use Meema\CloudFront\Facades\CloudFront;
use Meema\CloudFront\Jobs\InvalidateCache;

class FaqCategorySPAController extends Controller
{
    const SPA_PATH = '/faq-category';

    public function __construct()
    {
        $this->middleware('permission:faq-view')->only(['index', 'show', 'queryCategories', 'queryParentCategories']);
        $this->middleware('permission:faq-add-or-edit')->only(['store']);
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
            FaqCategory::tableSearch()
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
        $existSlug = FaqCategory::where('slug', $slug)->where('id', '<>', $request->get('id'))->count();
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

        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }

        FaqCategory::updateOrCreate(['id' => $request->get('id')], $request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FaqCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function show(FaqCategory $faqCategory)
    {
        return response()->json($faqCategory->toArray());
    }

    public function destroy(FaqCategory $faqCategory)
    {
        $faq = FaqContent::firstWhere(['faq_category_id' => $faqCategory->id]);
        if ($faq) {
            throw new ErrorMessageException('Kategori ini digunakan dalam ' . $faq->title . '');
        }

        $faqCategory->delete();
    }

    public function queryCategories(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = FaqCategory::select(['id', 'name'])
            ->where('name',  $request->get('q'))
            ->orWhere('name', 'like', $search)
            ->get();
        return response()->json($result);
    }

    public function queryParentCategories(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = FaqCategory::select(['id', 'name'])
            ->where('is_parent', 1)
            ->where(function ($query) use ($request, $search) {
                $query->where('name', $request->get('q'));
                $query->orWhere('name', 'like', $search);
            })
            ->get();
        return response()->json($result);
    }

    public function faqCategories(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = FaqCategory::select(['id', 'name', 'slug'])
            ->where('status', 1)
            ->where('name', 'like', $search)
            ->get();
        return response()->json($result);
    }
}
