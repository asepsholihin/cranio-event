<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Meema\CloudFront\Facades\CloudFront;
use Meema\CloudFront\Jobs\InvalidateCache;
use MadeITBelgium\SeoAnalyzer\SeoFacade as SEO;
use Qmas\KeywordAnalytics\Facade as Analytic;
use DB;

class ArticleSPAController extends Controller
{
    const SPA_PATH = '/article';

    public function __construct()
    {
        if (!request()->is('api/*')) {
            $this->middleware('permission:article-view')->only(['index', 'show', 'queryArticles']);
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
            Article::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    public function allArticles()
    {
        $query = Article::where('status', 1)->where('web_blogs.show_in_page', 'article');
        if (request()->showInPage) {
            $query->where('web_blogs.show_in_page', request()->showInPage);
        }
        $articles = $query->get();

        $response = array();
        foreach ($articles as $value) {
            $response[] = 'artikel/' . $value->slug;
        }
        return response()->json($response);
    }

    public function relatedArticles(Request $request)
    {
        $queryCount = Article::where('status', 1);
        if (request()->showInPage) {
            $queryCount->where('web_blogs.show_in_page', request()->showInPage);
        }
        $count = $queryCount->count();

        $randomCount = 1;
        if ($request->count <= $count) {
            $randomCount = $request->count;
        }

        $article = Article::where('status', 1);
        if ($request->exceptSlug !== "") {
            $article->whereNot('slug', $request->exceptSlug);
        }
        if (request()->showInPage) {
            $article->where('web_blogs.show_in_page', request()->showInPage);
        }

        return response()->json(
            ($article->get()->isEmpty() == false) ? $article->get()->random($randomCount) : []
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreArticleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreArticleRequest $request)
    {
        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid
        ]);

        $article = DB::transaction(function() use($request) {
            
            $article = Article::updateOrCreate(['id' => $request->get('id')], $request->except(['image']));

            return $article;
        });

        try {
            $paths = ['/*'];
            $result = CloudFront::invalidate($paths, \config('cloudfront.distribution_id'));

            $keywordCheck = Analytic::run($article->keywords, $article->meta_title, $article->meta_description, $article->content, $article->url)->getResults();
            $pageCheck = SEO::analyze($article->url);

            // FROM KEYWORD
            $totalScore = 0;
            foreach ($keywordCheck as $value) {
                if($value['type'] == "success") {
                    $totalScore += 100;
                }
                if($value['type'] == "warning") {
                    $totalScore += 50;
                }
                if($value['type'] == "error") {
                    $totalScore += 0;
                }
            }
            // FROM ANALYZER
            if($pageCheck['canonical']) {
                $totalScore += 100;
            }
            if($pageCheck['title']) {
                $totalScore += 100;
            }
            if($pageCheck['description']) {
                $totalScore += 100;
            }
            if($pageCheck['full_page']['headers']['h1']['count'] > 0) {
                $totalScore += 100;
            }
            if($pageCheck['loadtime'] < 1) {
                $totalScore += 100;
            }

            $totalInternalLink = $pageCheck['main_text']['links']['internal'];
            $totalExternalLink = $pageCheck['main_text']['links']['external'];

            $article->update([
                'seo_score' => floor($totalScore / 20),
                'seo_checks' => $keywordCheck,
                'count_external_link' => $totalExternalLink,
                'count_internal_link' => $totalInternalLink
            ]);
        } catch (\Throwable $th) {
            //
        }

        return response()->json(
            $article
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        return response()->json($article->toArray());
    }

    public function detail(Request $request, $slug)
    {
        $article = Article::where('slug', $slug)->first();
        if ($article) {
            $article->author = User::find($article->created_by)->name ?? 'Admin';
            $article->published_date = Carbon::parse($article->created_at)->format('d F Y');
        }
        return response()->json($article);
    }

    public function destroy(Article $article)
    {
        $article->delete();
    }

    public function queryArticles(Request $request)
    {
        $request->validate(['q' => 'required']);

        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = Article::select(['id', 'title', 'keywords', 'image_url'])
            ->where('title',  $request->get('q'))
            ->orWhere('keywords', 'like', $search)
            ->limit(10)
            ->get();
        return response()->json($result);
    }

    public function action(Request $request)
    {

        if ($request->has('noindex')) {
            $getArticles = Article::whereIn('id', $request->ids)->get();
            foreach ($getArticles as $key => $article) {
                $metaIndex = json_decode($article->meta_index);
                if (is_array($metaIndex)) {
                    array_push($metaIndex, 'noindex');
                } else {
                    $metaIndex[] = 'noindex';
                }
                $article->meta_index = array_unique($metaIndex);
                $article->save();
            }
            return;
        }

        if ($request->has('nofollow')) {
            $getArticles = Article::whereIn('id', $request->ids)->get();
            foreach ($getArticles as $key => $article) {
                $metaIndex = json_decode($article->meta_index);
                if (is_array($metaIndex)) {
                    array_push($metaIndex, 'nofollow');
                } else {
                    $metaIndex[] = 'nofollow';
                }
                $article->meta_index = array_unique($metaIndex);
                $article->save();
            }
            return;
        }

        if ($request->has('noimageindex')) {
            $getArticles = Article::whereIn('id', $request->ids)->get();
            foreach ($getArticles as $key => $article) {
                $metaIndex = json_decode($article->meta_index);
                if (is_array($metaIndex)) {
                    array_push($metaIndex, 'noimageindex');
                } else {
                    $metaIndex[] = 'noimageindex';
                }
                $article->meta_index = array_unique($metaIndex);
                $article->save();
            }
            return;
        }

        if ($request->has('noarchive')) {
            $getArticles = Article::whereIn('id', $request->ids)->get();
            foreach ($getArticles as $key => $article) {
                $metaIndex = json_decode($article->meta_index);
                if (is_array($metaIndex)) {
                    array_push($metaIndex, 'noarchive');
                } else {
                    $metaIndex[] = 'noarchive';
                }
                $article->meta_index = array_unique($metaIndex);
                $article->save();
            }
            return;
        }

        if ($request->has('nosnippet')) {
            $getArticles = Article::whereIn('id', $request->ids)->get();
            foreach ($getArticles as $key => $article) {
                $metaIndex = json_decode($article->meta_index);
                if (is_array($metaIndex)) {
                    array_push($metaIndex, 'nosnippet');
                } else {
                    $metaIndex[] = 'nosnippet';
                }
                $article->meta_index = array_unique($metaIndex);
                $article->save();
            }
            return;
        }

        if ($request->has('delete')) {
            $getArticles = Article::whereIn('id', $request->ids)->delete();
            return;
        }
    }

    public function articleSeoCheck($id)
    {
        $article = Article::select('*')->where('id', $id)->first();
        $keywordCheck = array();
        $pageCheck = array();
        try {
            $keywordCheck = Analytic::run($article->keywords, $article->meta_title, $article->meta_description, $article->content, $article->url)->getResults();
            $pageCheck = SEO::analyze($article->url);
        } catch (\Throwable $th) {
            // 
        }

        // FROM KEYWORD
        $totalScore = 0;
        foreach ($keywordCheck as $value) {
            if($value['type'] == "success") {
                $totalScore += 100;
            }
            if($value['type'] == "warning") {
                $totalScore += 50;
            }
            if($value['type'] == "error") {
                $totalScore += 0;
            }
        }
        // FROM ANALYZER
        if($pageCheck['canonical']) {
            $totalScore += 100;
        }
        if($pageCheck['title']) {
            $totalScore += 100;
        }
        if($pageCheck['description']) {
            $totalScore += 100;
        }
        if($pageCheck['full_page']['headers']['h1']['count'] > 0) {
            $totalScore += 100;
        }
        if($pageCheck['loadtime'] < 1) {
            $totalScore += 100;
        }

        $totalInternalLink = $pageCheck['main_text']['links']['internal'];
        $totalExternalLink = $pageCheck['main_text']['links']['external'];

        $article->update([
            'seo_score' => floor($totalScore / 20),
            'seo_checks' => $keywordCheck,
            'link_counter' => array('external_link' => $totalExternalLink,'internal_link' => $totalInternalLink)
        ]);
        return response()->json($article);
    }

    public function articlesSeoCheck()
    {
        $articles = Article::select('id','title','seo_checks', 'seo_score')->get();
        foreach ($articles as $article) {
            $article->score = $article->getCurrentScore();
            $article->scoreDetails = $article->getCurrentScoreDetails();
        }
        return response()->json($articles);
    }

    public function kajian()
    {
        $query = Article::where('status', 1)->where('web_blogs.type', 'kajian');
        if (request()->featured) {
            $query->limit(2);
        }
        $kajian = $query->get();

        return response()->json($kajian);
    }
}
