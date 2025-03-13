<?php

namespace App\Http\Controllers;

use App\Models\FormSection;
use Illuminate\Http\Request;
use DB;

class FormSectionSPAController extends Controller
{
    const SPA_PATH = '/form-section';

    public function __construct()
    {
        $this->middleware('permission:survey-view')->only(['index', 'show', 'queryFormSectionSearch']);
        $this->middleware('permission:survey-add-or-edit')->only(['store']);
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
            FormSection::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::transaction(function() use($request) {
            $uid = auth()->user()->id;
            $request->merge([
                'created_by' => $uid,
                'updated_by' => $uid
            ]);

            $form_section = FormSection::updateOrCreate(['id' => $request->id], $request->all());
        });

        return response()->json($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FormSection $form_section
     * @return \Illuminate\Http\Response
     */
    public function show(FormSection $form_section)
    {
        return response()->json($form_section->toArray());
    }

    public function destroy(FormSection $form_section)
    {
        $form_section->delete();
    }

    public function queryFormSectionSearch(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = FormSection::select(['id','name'])
            ->where('name',  $request->get('q'))
            ->orWhere('name', 'like', $search)
            ->get();
        return response()->json($result);
    }
}
