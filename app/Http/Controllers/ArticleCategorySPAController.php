<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleCategoryRequest;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;


class ArticleCategorySPAController extends Controller
{
    const SPA_PATH = '/article-category';

    public function __construct()
    {
        if (!request()->is('api/*')) {
            $this->middleware('permission:article-view')->only(['index','show','queryArticleCategories']);
        }
        $this->middleware('permission:article-add-or-edit')->only(['store']);
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
            ArticleCategory::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    public function allArticleCategorys()
    {
        $articles = ArticleCategory::where('status', 1)->get();

        $response = array();
        foreach($articles as $value) {
            $response[] = 'artikel/'.$value->slug;
        }
        return response()->json($response);
    }

    public function relatedArticleCategorys(Request $request)
    {
        $count = ArticleCategory::where('status', 1)->count();

        $randomCount = 1;
        if($request->count <= $count) {
            $randomCount = $request->count;
        }
        return response()->json(
            ArticleCategory::where('status', 1)->get()->random($randomCount)
        );
    } 

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreArticleCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreArticleCategoryRequest $request)
    {
        $article = ArticleCategory::updateOrCreate(['id' => $request->get('id')], $request->all());
        
        
        
        return response()->json(
            $article
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ArticleCategory  $article
     * @return \Illuminate\Http\Response
     */
    public function show(ArticleCategory $article_category)
    {
        return response()->json($article_category->toArray());
    }

    public function detail(Request $request, $slug)
    {
        $article = ArticleCategory::where('slug',$slug)->first();
        return response()->json($article);
    }

    public function destroy(ArticleCategory $article_category)
    {
        $article_category->delete();
    }

    public function queryArticleCategories(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search .'%';
        $query = ArticleCategory::select(['id','name'])->where('name', 'like', $search);
        if($request->id) {
            $query->where('id', '!=', $request->id);
        }
        $result = $query->get();
        return response()->json($result);
    }

    public function articleCategories(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search .'%';
        $query = ArticleCategory::select(['id','name','slug'])->where('name', 'like', $search);
        if($request->id) {
            $query->where('id', '!=', $request->id);
        }
        if($request->showInPage) {
            $query->join('web_blogs', 'web_blog_categories.id', '=', 'web_blogs.category_id');
            $query->where('web_blogs.show_in_page', $request->showInPage);
        }
        if($request->limit) {
            $query->limit($request->limit);
        }
        $result = $query->orderBy('order', 'asc')->get();
        return response()->json($result);
    }
}
