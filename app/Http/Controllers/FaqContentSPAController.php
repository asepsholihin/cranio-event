<?php

namespace App\Http\Controllers;

use App\Models\FaqContent;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use Illuminate\Support\Str;
use Meema\CloudFront\Facades\CloudFront;
use Meema\CloudFront\Jobs\InvalidateCache;

class FaqContentSPAController extends Controller
{
    const SPA_PATH = '/faq-content';

    public function __construct()
    {
        $this->middleware('permission:faq-view')->only(['index','show','queryContents']);
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
            FaqContent::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required']);

        $status_value = 0;
        if ($request->status == "true") {
            $status_value = 1;
        }

        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'status' => $status_value
        ]);

        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));
        } catch (\Throwable $th) {
            //
        }

        FaqContent::updateOrCreate(['id' => $request->get('id')], $request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FaqContent  $category
     * @return \Illuminate\Http\Response
     */
    public function show(FaqContent $faqContent)
    {
        return response()->json($faqContent->toArray());
    }

    public function destroy(FaqContent  $faqContent)
    {
        $faqContent->delete();
    }

    public function queryContents(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = FaqContent::select(['id','title'])
            ->where('title',  $request->get('q'))
            ->orWhere('title', 'like', $search)
            ->get();
        return response()->json($result);
    }

    public function faqContents(Request $request)
    {
        $query = FaqContent::select(['id','faq_category_id','title','content'])->where('status', 1);
        if($request->category) {
            $faq_category_id = FaqCategory::where('slug', $request->category)->first()->id ?? '';
            if($faq_category_id) {
                $query->where('faq_category_id', $faq_category_id);
            }
        }
        if($request->q) {
            $query->where(function($q) {
                $search = request()->get('q');
                $search = '%' . $search .'%';
                $q->where('title', 'like', $search)
                ->orWhere('content', 'like', $search);
            });
        }
        $result = $query->get();
        return response()->json($result);
    }
}
