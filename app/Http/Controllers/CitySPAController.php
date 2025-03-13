<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;

class CitySPAController extends Controller
{
    const SPA_PATH = '/city';

    public function __construct()
    {
        $this->middleware('permission:land-arrangement-view')->only(['index','show']);
        $this->middleware('permission:land-arrangement-add-or-edit')->only(['store']);
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
            City::tableSearch()
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

        City::updateOrCreate(['id' => $request->get('id')], $request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(City $city)
    {
        return response()->json($city->toArray());
    }

    public function destroy(City $city)
    {
        if (in_array($city->id, array(1,2,3))) {
            return response()->json([
                'success' => false,
                'message'  => 'Kota default tidak dapat di hapus',
            ], 422);
        }
        $hotel = Hotel::firstWhere(['city_id'=>$city->id]);
        if ($hotel) {
            throw new ErrorMessageException('City ini digunakan oleh hotel '.$hotel->name.'');
        }

        $city->delete();
    }

    public function queryCities(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = City::select(['id','name'])
            ->where('name',  $request->get('q'))
            ->orWhere('name', 'like', $search)
            ->get();
        return response()->json($result);
    }

    public function getAllCity(){
        $data = City::select('*', 'name as label')->get();
        return response()->json($data);
    }
}
