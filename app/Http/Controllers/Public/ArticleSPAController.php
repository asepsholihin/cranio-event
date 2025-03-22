<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DB;


class ArticleSPAController extends Controller
{
    const SPA_PATH = '/article';

    public function index()
    {
        $orderBy = request()->query('sortBy', 'web_blogs.id');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage', 10);

        $query = DB::table('web_blogs');
        $query->leftjoin('web_blog_categories', 'web_blog_categories.id', '=', 'web_blogs.category_id')
        ->select(['web_blogs.slug', 'web_blogs.image_url', 'web_blogs.thumbnail_url', 'web_blogs.title', 'web_blogs.created_at', DB::raw('substr(web_blogs.content, 0, 144) as content')])
        ->where('status', 1)
        ->whereNull('web_blogs.deleted_at');

        if (request()->categoryId) {
            $query->where('web_blogs.category_id', request()->categoryId);
        }

        if (request()->category) {
            $query->where('web_blog_categories.slug', request()->category);
        }

        if (request()->showInPage) {
            $query->where('web_blogs.show_in_page', request()->showInPage);
        }

        if(request()->query('q')) {
            $search = '%' . request()->query('q') . '%';
            $query->where(function($q) use($search) {
                $q->where('title',  request()->query('q'))
                ->orWhere('title', 'like', $search)
                ->orWhere('keywords', 'like', $search);
            });
        }
        
        $articles = $query->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH);

        return response()->json($articles);
    }
    
    public function detail(Request $request, $slug)
    {
        $article = Article::where('slug', $slug)->first();
        if ($article) {
            $article->author = DB::table('users')->where('id', $article->created_by)->name ?? 'Admin';
            $article->published_date = Carbon::parse($article->created_at)->format('d F Y');
            $article->asatidz_name = DB::table('asatidz')->where('asatidz.id', $article->asatidz_id)->first()->name ?? '';
            $article->written_by_name = DB::table('asatidz')->where('asatidz.id', $article->written_by_id)->first()->name ?? '';

            $article->increment('views_count');
        }
        return response()->json($article);
    }

    public function allArticles()
    {
        $query = DB::table('web_blogs');
        $query->select(['web_blogs.slug', 'web_blogs.image_url', 'web_blogs.thumbnail_url', 'web_blogs.title', 'web_blogs.created_at', DB::raw('substr(web_blogs.content, 0, 144) as content')])
        ->where('status', 1)
        ->whereNull('web_blogs.deleted_at')
        ->where('web_blogs.show_in_page', 'article');
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
        $queryCount = DB::table('web_blogs')->where('status', 1)->whereNull('web_blogs.deleted_at');
        if (request()->showInPage) {
            $queryCount->where('web_blogs.show_in_page', request()->showInPage);
        }
        $count = $queryCount->count();

        $randomCount = 1;
        if ($request->count <= $count) {
            $randomCount = $request->count;
        }

        $article = DB::table('web_blogs');
        $article->select(['web_blogs.slug', 'web_blogs.image_url', 'web_blogs.thumbnail_url', 'web_blogs.title', 'web_blogs.created_at', DB::raw('substr(web_blogs.content, 0, 144) as content')])
        ->where('status', 1)
        ->whereNull('web_blogs.deleted_at');
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

    public function kajian()
    {
        $query = DB::table('web_blogs');
        $query->select(['web_blogs.slug', 'web_blogs.image_url', 'web_blogs.thumbnail_url', 'web_blogs.title', 'web_blogs.created_at', DB::raw('substr(web_blogs.content, 0, 144) as content')])
        ->where('status', 1)
        ->whereNull('web_blogs.deleted_at')
        ->where('web_blogs.type', 'kajian');
        if (request()->featured) {
            $query->limit(2);
        }
        $kajian = $query->get();

        return response()->json($kajian);
    }

    public function participantArticles(Request $request)
    {
        $orderBy = request()->query('sortBy', 'web_blogs.id');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage');

        $query = DB::table('web_blogs');
        $query->select(['web_blogs.slug', 'web_blogs.image_url', 'web_blogs.thumbnail_url', 'web_blogs.title', 'web_blogs.created_at', DB::raw('substr(web_blogs.content, 0, 144) as content'), 'type', 'youtube_link', 'asatidz.name as asatidz_name'])
        ->where('web_blogs.status', 1)
        ->whereNull('web_blogs.deleted_at')
        ->where('web_blogs.show_in_page', 'participant-room')
        ->leftjoin('asatidz', 'asatidz.id', 'web_blogs.asatidz_id')
        ->leftjoin('web_blog_categories', 'web_blog_categories.id', '=', 'web_blogs.category_id');
        if (request()->featured) {
            $query->limit(2);
        }
        if (request()->youtube_article) {
            $query->whereNotNull('web_blogs.youtube_link');
        }
        if (request()->type) {
            $query->where('web_blogs.type', request()->type);
        }
        if (request()->asatidz) {
            $query->where('asatidz.slug', request()->asatidz);
        }
        if (request()->category) {
            $query->where('web_blog_categories.slug', request()->category);
        }
        $query->orderBy($orderBy, $sortBy);

        if($perPage) {
            $kajian = $query->paginate($perPage);
        } else {
            $kajian = $query->get();
        }
        return response()->json($kajian);
    }

    public function articleEdit(Request $request)
    {
        if($request->views_count_visible) {
            DB::table('web_blogs')->update([
                'views_count_visible' => $request->views_count_visible
            ]);
        } else {
            DB::table('web_blogs')->where('id', $request->id)->update($request->all());
        }

        
    }

    public function asatidzArticles(Request $request)
    {
        $orderBy = request()->query('sortBy', 'web_blogs.id');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage');

        $query = DB::table('web_blogs');
        $query->select(['web_blogs.slug', 'web_blogs.image_url', 'web_blogs.thumbnail_url', 'web_blogs.title', 'web_blogs.created_at', DB::raw('substr(web_blogs.content, 0, 144) as content'), 'type', 'youtube_link', 'asatidz.name as asatidz_name'])
        ->where('web_blogs.status', 1)
        ->where('web_blogs.written_by_id', $request->asatidz_id)
        ->leftjoin('asatidz', 'asatidz.id', 'web_blogs.written_by_id')
        ->whereNull('web_blogs.deleted_at');
        $query->orderBy($orderBy, $sortBy);

        if($perPage) {
            $kajian = $query->paginate($perPage);
        } else {
            $kajian = $query->get();
        }
        return response()->json($kajian);
    }
}
