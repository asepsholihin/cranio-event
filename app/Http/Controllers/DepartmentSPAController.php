<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserPlatform;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\WebSale;

class DepartmentSPAController extends Controller
{
    const SPA_PATH = '/departement';

    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::orderBy('id', 'asc')->get();
        foreach($departments as $department){
            $isActive = true;
            if($department->is_active > 1) $isActive = false;
            $department->is_active = $isActive;
        }
        return response()->json($departments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $department = new Department();
        $department->name = $request->name;
        $department->created_by = auth()->user()->id;
        $department->save();
        return response()->json($department);
    }

    public function changeStatus(Request $request)
    {
        $department = Department::findOrFail($request->id);
        if($request->is_active == false){
            $department->is_active = 2;
        }
        else{
            $department->is_active = 1;
        }
        $department->save();
    }

    public function show($id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Participant  $participant
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }


    public function queryDepartments(Request $request)
    {
        
        $query = Department::select(['id','name'])->where('is_active', 1);
        if(!empty($request->get('q'))) {
            $search = $request->get('q');
            $search = '%' . $search .'%';
            $query->where('name', 'like', $search);
        }
        $result = $query->get();
        return response()->json($result);
    }
}
