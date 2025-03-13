<?php

namespace App\Http\Controllers;

use App\Models\MasterUnit;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;

class MasterUnitSPAController extends Controller
{
    const SPA_PATH = '/master-unit';

    public function __construct()
    {
        $this->middleware('permission:jios-view')->only(['index','show','queryunits','queryParentunits']);
        $this->middleware('permission:jios-add-or-edit')->only(['store']);
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
            MasterUnit::tableSearch()
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

        MasterUnit::updateOrCreate(['id' => $request->get('id')], $request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MasterUnit $master_unit
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $master_unit)
    {
        return response()->json($master_unit->toArray());
    }

    public function destroy(Unit $master_unit)
    {
        // $hotel = Hotel::firstWhere(['unit_id'=>$master_unit->id]);
        // if ($hotel) {
        //     throw new ErrorMessageException('Unit ini digunakan oleh hotel '.$hotel->name.'');
        // }

        $master_unit->delete();
    }

    public function queryunits(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = MasterUnit::select(['id','name'])
            ->where('name',  $request->get('q'))
            ->orWhere('name', 'like', $search)
            ->get();
        return response()->json($result);
    }
}
