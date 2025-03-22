<?php

namespace App\Http\Controllers;

use App\Exceptions\ErrorMessageException;
use App\Http\Requests\StoreTestimonialRequest;
use App\Models\Testimonial;
use Illuminate\Http\Request;

use Image;
use Illuminate\Support\Facades\Storage;

class TestimonialSPAController extends Controller
{
    const SPA_PATH = '/testimonial';

    public function __construct()
    {
        if (!request()->is('api/*')) {
            $this->middleware('permission:testimonial-view')->only(['index','show','queryTestimonials']);
        }
        $this->middleware('permission:testimonial-add-or-edit')->only(['store']);
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
            Testimonial::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    public function testimonials(Request $request)
    {
        $query = Testimonial::select('customer_name','customer_job','customer_photo','testimony','order', 'category', 'subcategory', 'package' ,'publish_status','trip', 'id as ids_track')->whereNull('deleted_at')->where('publish_status', 1)->orderBy('order', 'asc');
        if(!empty(request()->category) && !empty(request()->subcategory) && !empty(request()->package)) {
            $query->where('category', 'like', '%'.request()->category.'%')
            ->where('subcategory', 'like', '%'.request()->subcategory.'%')
            ->where('package', 'like', '%'.request()->package.'%');
        }
        if(!empty(request()->category) && !empty(request()->subcategory) && empty(request()->package)) {
            $query->where('category', 'like', '%'.request()->category.'%')
            ->where('subcategory', 'like', '%'.request()->subcategory.'%')
            ->whereNull('package');
        }
        if(!empty(request()->category) && empty(request()->subcategory) && empty(request()->package)) {
            $query->where('category', 'like', '%'.request()->category.'%')
            ->whereNull('subcategory')->whereNull('package');
        }
        $testimonial = $query->orderBy('created_at', 'desc')->get();
        if ($testimonial->count() == 0) {
            return response()->json([]);
        }

        return response()->json(
            $testimonial->random(($request->count) ? $request->count : $testimonial->count())
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTestimonialRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTestimonialRequest $request)
    {
        Testimonial::updateOrCreate(['id' => $request->get('id')], $request->except(['image']));

        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Testimonial  $testimonial
     * @return \Illuminate\Http\Response
     */
    public function show(Testimonial $testimonial)
    {
        return response()->json($testimonial->toArray());
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        
    }

    public function queryTestimonials(Request $request)
    {
        $request->validate(['q' => 'required']);

        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = Testimonial::select(['id','customer_name','customer_job','customer_photo'])
            ->where('customer_name',  $request->get('q'))
            ->orWhere('customer_job', 'like', $search)
            ->limit(10)
            ->get();
        return response()->json($result);
    }

    public function testimonialsWeb(Request $request){
        if(empty($request->get('order')) || $request->get('order') == '' || $request->get('order') == 'null')
        {
            throw new ErrorMessageException("Harap isi urutan");
        }

        if(!empty($request->get('id'))){
            $testimonial = Testimonial::findOrFail($request->get('id'));
        }
        else{
            $testimonial = new Testimonial();
        }
        if ($request->hasFile('image')) {
            $imageMake = Image::make($request->file('image'));
            $img = (string) $imageMake
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->encode('webp',100);

            $profilePhotoPath =  Testimonial::DIR_IMAGE . pathinfo($request->file('image')->hashName(), PATHINFO_FILENAME) . '.webp';

            Storage::put($profilePhotoPath, $img);
            $testimonial->customer_photo = $profilePhotoPath;
        }
        $testimonial->customer_name = $request->get('customer_name');
        $testimonial->customer_job = $request->get('customer_job');
        $testimonial->testimony = $request->get('testimony');
        $testimonial->order = $request->get('order');
        $testimonial->trip = $request->get('trip');
        $testimonial->category = $request->get('category');
        $testimonial->save();

        
    }

    public function deleteTestiomnial($id){
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->deleted_at = date('Y-m-d H:i:s');
        $testimonial->save();

        
        return response()->json($testimonial);
    }
}
