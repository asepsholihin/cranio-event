<?php

namespace App\Http\Controllers;

use App\Models\HotelRoomType;
use Illuminate\Http\Request;

class HotelRoomTypeSPAController extends Controller
{
    const SPA_PATH = '/hotel-room-type';

    public function __construct()
    {
        $this->middleware('permission:land-arrangement-view')->only(['index','show','queryHotelRoomTypes']);
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
            HotelRoomType::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required',
            'name' => 'required',
            'pax_per_room' => 'required|numeric|min:1|max:5',
            'price_per_pax' => 'required|numeric',
        ]);

        $status_value = 0;
        if ($request->status == "true") {
            $status_value = 1;
        }

        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'status' => $status_value,
        ]);

        $request->all();

        HotelRoomType::updateOrCreate(['id' => $request->get('id')], $request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HotelRoomType  $city
     * @return \Illuminate\Http\Response
     */
    public function show(HotelRoomType $hotelRoomType)
    {
        return response()->json($hotelRoomType->toArray());
    }

    public function destroy(HotelRoomType $hotelRoomType)
    {
        $hotelRoomType->delete();
    }

    public function queryHotelRoomTypes(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = HotelRoomType::select(['id','name'])
            ->where('name',  $request->get('q'))
            ->orWhere('name', 'like', $search)
            ->get();
        return response()->json($result);
    }
}
