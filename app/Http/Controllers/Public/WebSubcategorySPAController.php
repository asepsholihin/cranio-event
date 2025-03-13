<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\WebSubcategory;

class WebSubcategorySPAController extends Controller
{
    const SPA_PATH = '/catalog/sub-category';

    public function __construct()
    {
        
    }

    public function subcategoryDetail($slug)
    {
        $sub_category = WebSubcategory::where('slug', $slug)->first();
        return response()->json($sub_category);
    }
}
