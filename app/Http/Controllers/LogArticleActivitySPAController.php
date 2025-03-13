<?php

namespace App\Http\Controllers;

use App\Models\LogArticleActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogArticleActivitySPAController extends Controller
{
    const SPA_PATH = '/log-article-activity';

    public function __construct()
    {
        $this->middleware('permission:log-activity-view')->only(['index']);
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
            LogArticleActivity::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }
}
