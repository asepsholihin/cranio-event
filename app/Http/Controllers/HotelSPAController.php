<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotelRoomType;
use App\Models\HotelRateHistory;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use App\File\Image\HotelInformation as HotelInformationImage;
use App\File\PDF\HotelInformation;
use DB;
use Carbon\Carbon;

class HotelSPAController extends Controller
{
    const SPA_PATH = '/hotel';

    public function __construct()
    {

        $this->middleware('permission:event-attendance-view')->only(['index', 'show', 'queryHotels']);
        $this->middleware('permission:event-attendance-add-or-edit')->only(['store']);
        // $this->middleware('permission:land-arrangement-view')->only(['index','show','queryHotels']);
        // $this->middleware('permission:land-arrangement-add-or-edit')->only(['store']);
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
        $hotels = Hotel::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH);

        foreach ($hotels->items() as $hotel) {
            $last_rate = HotelRateHistory::where('hotel_id', $hotel->id)->orderBy('id', 'DESC')->first();
            $hotel->last_updated_rate_at = $last_rate->updated_at ?? '';
            $hotel->last_updated_by_name = $last_rate->updated_by_name ?? '';
        }
        return response()->json($hotels);
    }

    public function store(Request $request)
    {
        $request->validate([
            'city_id' => 'required',
            'name' => 'required',
            'star' => 'required|numeric'
        ]);

        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid
        ]);

        Hotel::updateOrCreate(['id' => $request->get('id')], $request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Hotel  $city
     * @return \Illuminate\Http\Response
     */
    public function show(Hotel $hotel)
    {
        return response()->json($hotel->toArray());
    }

    public function destroy(Hotel $hotel)
    {
        $hotelRoomType = HotelRoomType::firstWhere(['hotel_id'=>$hotel->id]);
        if ($hotelRoomType) {
            throw new ErrorMessageException('Hotel ini digunakan oleh room type '.$hotelRoomType->name.'');
        }
        $hotel->delete();
    }

    public function queryHotels(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search .'%';
        $query = Hotel::select(['id','name','city_id']);
        if(!empty($request->get('q'))) {
            $query->where(function($q) use($search) {
                $q->where('name',  $request->get('q'))
                ->orWhere('name', 'like', $search)
                ->orWhere('pic_name', 'like', $search);
            });
        }
        if(!empty($request->city_id)) {
            $query->where('city_id', $request->city_id);
        }
        if(!empty($request->makkah_madinah)) {
            $query->whereIn('city_id', [2,3]);
        }

        $result = $query->get();
        return response()->json($result);
    }

    public function viewGraphic($hotelId)
    {
        return (new HotelInformationImage($hotelId))->download();
    }

    public function postHotelRate(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $uid = auth()->user()->id;
        $transactionDate = str_replace("/","-","01/".$request->transaction_date);
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid,
            'transaction_date' => date('Y-m-d', strtotime($transactionDate))
        ]);

        HotelRateHistory::updateOrCreate([
            'hotel_id' => $request->id,
            'transaction_date' => $request->transaction_date
        ], $request->all());
    }

    public function hotelRateHistories($hotelId)
    {
        $hotelRateHistories = HotelRateHistory::
        select('hotel_rate_histories.*', 'master_hotels.name')
        ->where('hotel_id', $hotelId)
        ->join('master_hotels', 'master_hotels.id', 'hotel_rate_histories.hotel_id')
        ->orderBy('hotel_rate_histories.transaction_date', 'DESC')
        ->get();

        foreach ($hotelRateHistories as $value) {
            $value->transaction_date_formated = "Periode ". Carbon::parse($value->transaction_date)->isoFormat('MMMM Y');
        }

        return response()->json($hotelRateHistories);
    }

    public function getHotelByCity($id){
        $hotels = Hotel::where('city_id', $id)->get();
        return response()->json($hotels);
    }
}
