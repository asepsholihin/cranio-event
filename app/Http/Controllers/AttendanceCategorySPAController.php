<?php

namespace App\Http\Controllers;

use App\Models\AttendanceCategory;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;

class AttendanceCategorySPAController extends Controller
{
    const SPA_PATH = '/attendance-category';

    public function __construct()
    {
        $this->middleware('permission:event-attendance-view')->only(['index','show','queryCategories']);
        $this->middleware('permission:event-attendanc-add-or-edit')->only(['store']);
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
            AttendanceCategory::tableSearch()
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

        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'status' => $status_value
        ]);

        $request->all();

        AttendanceCategory::updateOrCreate(['id' => $request->get('id')], $request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AttendanceCategory  $attendance_category
     * @return \Illuminate\Http\Response
     */
    public function show(AttendanceCategory $attendance_category)
    {
        return response()->json($attendance_category->toArray());
    }

    public function destroy(AttendanceCategory $attendance_category)
    {
        $attendance_category->delete();
    }

    public function queryCategories(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = AttendanceCategory::select(['id','name'])
            ->where('name',  $request->get('q'))
            ->orWhere('name', 'like', $search)
            ->get();
        return response()->json($result);
    }
}
