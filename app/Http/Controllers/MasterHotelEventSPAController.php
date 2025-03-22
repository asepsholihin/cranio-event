<?php

namespace App\Http\Controllers;

use App\Models\MasterHotelEvent;
use App\Models\HotelEventCustomText;
use App\Models\HotelEventRate;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Meema\CloudFront\Facades\CloudFront;

class MasterHotelEventSPAController extends Controller
{
    const SPA_PATH = '/master-hotel-event';

    public function __construct()
    {
        $this->middleware('permission:master-hotel-event-view')->only(['index','show']);
        $this->middleware('permission:master-hotel-event-add-or-edit')->only(['store']);
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
            MasterHotelEvent::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    public function store(Request $request)
    {
        $request->validate(['hotel_name' => 'required']);
        $masterHotel = array();
        $masterHotel['hotel_name'] = $request->hotel_name;
        $masterHotel['hotel_city'] = $request->hotel_city;
        $masterHotel['is_manasik'] = 'no';
        if($request->is_manasik){
            if($request->is_manasik == 1){
                $masterHotel['is_manasik'] = 'yes';
            }else{
                $masterHotel['is_manasik'] = 'no';
            }
        }
        $masterHotel['is_transit'] = 'no';
        if($request->is_transit){
            if($request->is_transit == 1){
                $masterHotel['is_transit'] = 'yes';
            }else{
                $masterHotel['is_transit'] = 'no';
            }
        }
        $masterHotel['hotel_pic'] = $request->hotel_pic;
        $masterHotel['hotel_pic_number'] = $request->hotel_pic_number;
        $masterHotel['hotel_address'] = $request->hotel_address;
        $masterHotel['hotel_map_url'] = $request->hotel_map_url;
        $masterHotel['manasik_hd_price'] = $request->manasik_hd_price;
        $masterHotel['manasik_fd_price'] = $request->manasik_fd_price;
        $masterHotel['updated_at'] = NULL;
        if($request->is_update){
            $masterHotel['updated_by'] = Auth::id();
            $masterHotel['updated_at'] = Carbon::now();
        }else{
            $masterHotel['created_by'] = Auth::id();
        }
        $master = MasterHotelEvent::updateOrCreate(['id'=>$request->id], $masterHotel);

        if($request->is_update){
            if($request->is_manasik == 0 || empty($request->is_manasik)){
                HotelEventCustomText::where('master_hotel_event_id', $master->id)->delete();
            }
            if($request->is_transit == 0 || empty($request->is_transit)){
                HotelEventRate::where('master_hotel_event_id', $master->id)->delete();
            }
        }

        if($request->manasik_ad_list){
            foreach($request->manasik_ad_list as $item)
            {
                if(empty($item['id'])){
                    $item['id'] = NULL;
                }
                if(!empty($item['custom_text'])){
                    HotelEventCustomText::updateOrCreate(['id'=>$item['id'], 'master_hotel_event_id' => $master->id],
                        [
                            'category'=> $item['category'],
                            'master_hotel_event_id' => $master->id,
                            'custom_text' => $item['custom_text']
                        ]
                    );
                }
            }
        }

        if($request->manasik_da_list){
            foreach($request->manasik_da_list as $item)
            {
                if(empty($item['id'])){
                    $item['id'] = NULL;
                }
                if(!empty($item['custom_text'])){
                    HotelEventCustomText::updateOrCreate(['id'=>$item['id'], 'master_hotel_event_id' => $master->id],
                        [
                            'category'=> $item['category'],
                            'master_hotel_event_id' => $master->id,
                            'custom_text' => $item['custom_text']
                        ]
                    );
                }
            }
        }

        if($request->is_manasik || $request->is_manasik == 1){
            foreach($request->manasik_hd_list as $item){
                if(empty($item['id'])){
                    $item['id'] = NULL;
                }
                if(!empty($item['custom_text'])){
                    HotelEventCustomText::updateOrCreate(['id'=>$item['id'], 'master_hotel_event_id' => $master->id],
                        [
                            'category'=> $item['category'],
                            'master_hotel_event_id' => $master->id,
                            'custom_text' => $item['custom_text']
                        ]
                    );
                }
            }

            foreach($request->manasik_fd_list as $item){
                if(empty($item['id'])){
                    $item['id'] = NULL;
                }
                if(!empty($item['custom_text'])){
                    HotelEventCustomText::updateOrCreate(['id'=>$item['id'], 'master_hotel_event_id' => $master->id],
                        [
                            'category'=> $item['category'],
                            'master_hotel_event_id' => $master->id,
                            'custom_text' => $item['custom_text']
                        ]
                    );
                }
            }
        }

        if($request->is_transit || $request->is_transit == 1){
            foreach($request->hotel_event_list as $item){
                foreach($item['child'] as $child){
                    if(empty($child['id'])){
                        $child['id'] = NULL;
                    }
                    if(!empty($child['item_name'])){
                        HotelEventRate::updateOrCreate(['id'=>$child['id'], 'master_hotel_event_id' => $master->id],
                            [
                                'rate_category'=> $item['rate_category'],
                                'master_hotel_event_id' => $master->id,
                                'item_name' => $child['item_name'],
                                'item_price' => $child['item_price'] ?? 0
                            ]
                        );
                    }
                }
            }
        }

        
    }

    public function prefer($id, Request $request){
        $preferToManasik = MasterHotelEvent::find($id);
        if($request->type == 'prefer_manasik'){
            $preferToManasik->prefer_for_manasik = "yes";
        }else if($request->type == 'prefer_transit'){
            $preferToManasik->prefer_for_transit = "yes";
        }else if($request->type == 'remove_manasik'){
            $preferToManasik->prefer_for_manasik = "no";
        }else if($request->type == 'remove_transit'){
            $preferToManasik->prefer_for_transit = "no";
        }
        if(empty($preferToManasik->updated_at)){
            $preferToManasik->updated_at = NULL;
        }
        $preferToManasik->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MasterHotelEvent  $master_hotel_event
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $master = MasterHotelEvent::find($id);
        $manasik = 0;
        if($master->is_manasik == 'yes'){
            $manasik = 1;
        }
        $transit = 0;
        if($master->is_transit == 'yes'){
            $transit = 1;
        }
        $response = [
            'id' => $master->id,
            'hotel_name' => $master->hotel_name,
            'hotel_city' => $master->hotel_city,
            'is_manasik' => $manasik,
            'is_transit' => $transit,
            'hotel_pic' => $master->hotel_pic,
            'hotel_pic_number' => $master->hotel_pic_number,
            'hotel_map_url' => $master->hotel_map_url,
            'manasik_hd_price' => $master->manasik_hd_price,
            'manasik_fd_price' => $master->manasik_fd_price,
            'hotel_address' => $master->hotel_address
        ];
        $fdArr = [];
        foreach($master->manasik_fd as $item){
            $fdArr[] = array(
                'id' => $item->id,
                'master_hotel_event_id' => $item->master_hotel_event_id,
                'category' => $item->category,
                'custom_text' => $item->custom_text
            );
        }
        $hdArr = [];
        foreach($master->manasik_hd as $item){
            $hdArr[] = array(
                'id' => $item->id,
                'master_hotel_event_id' => $item->master_hotel_event_id,
                'category' => $item->category,
                'custom_text' => $item->custom_text
            );
        }
        $adArr = [];
        foreach($master->advantages as $item){
            $adArr[] = array(
                'id' => $item->id,
                'master_hotel_event_id' => $item->master_hotel_event_id,
                'category' => $item->category,
                'custom_text' => $item->custom_text
            );
        }
        $daArr = [];
        foreach($master->disadvantages as $item){
            $daArr[] = array(
                'id' => $item->id,
                'master_hotel_event_id' => $item->master_hotel_event_id,
                'category' => $item->category,
                'custom_text' => $item->custom_text
            );
        }
        $hotArr = [];
        foreach($master->hotel_rate as $item){
            $childs = [];
            foreach($item->child as $child){
                $childs[] = array(
                    'master_hotel_event_id' => $item->master_hotel_event_id,
                    'id' => $child->id,
                    'item_name' => $child->item_name,
                    'item_price' => $child->item_price,
                    'rate_category' => $child->rate_category,
                );
            }

            $hotArr[] = array(
                'is_show' => true,
                'master_hotel_event_id' => $item->master_hotel_event_id,
                'rate_category' => $item->rate_category,
                'child' => $childs
            );
        }
        $response['manasik_fd_list'] = $fdArr;
        $response['manasik_hd_list'] = $hdArr;
        $response['manasik_ad_list'] = $adArr;
        $response['manasik_da_list'] = $daArr;
        $response['hotel_event_list'] = $hotArr;
        return response()->json($response);
    }

    public function destroy(MasterHotelEvent $master_hotel_event)
    {
        $master_hotel_event->deleted_by = auth()->user()->id;
        $master_hotel_event->save();
        $master_hotel_event->delete();

        // HotelEventCustomText::where('master_hotel_event_id', $master_hotel_event->id)->delete();
        // HotelEventRate::where('master_hotel_event_id', $master_hotel_event->id)->delete();
    }

    public function priceCategoryList(){
        $category = DB::table('hotel_event_rate_category')->select('*', 'name as label')->get();
        return response()->json($category);
    }

    public function getCounter($id){
        $data = DB::table('hotel_event_counter')->where('master_hotel_event_id', $id)->get();
        return response()->json($data);
    }

    public function getTransitHotel(){
        $data = MasterHotelEvent::where('is_transit', 'yes')->get();
        return response()->json($data);
    }

    public function getManasikHotel(){
        $data = MasterHotelEvent::where('is_manasik', 'yes')->get();
        return response()->json($data);
    }

    public function addCategory(Request $request){
        $category = DB::table('hotel_event_rate_category')->insert($request->all());
        return response()->json($category);
    }

    public function deleteItem(Request $request){
        if($request->type == 'ad'){
            HotelEventCustomText::where('category', 'advantages')->where('id', $request->id)->where('master_hotel_event_id', $request->master_hotel_event_id)->delete();
        }else if($request->type == 'da'){
            HotelEventCustomText::where('category', 'disadvantages')->where('id', $request->id)->where('master_hotel_event_id', $request->master_hotel_event_id)->delete();
        }else if($request->type == 'fd'){
            HotelEventCustomText::where('category', 'manasik_fd_price')->where('id', $request->id)->where('master_hotel_event_id', $request->master_hotel_event_id)->delete();
        }else if($request->type == 'hd'){
            HotelEventCustomText::where('category', 'manasik_hd_price')->where('id', $request->id)->where('master_hotel_event_id', $request->master_hotel_event_id)->delete();
        }else if($request->type == 'he'){
            HotelEventRate::where('rate_category', $request->rate_category)->where('master_hotel_event_id', $request->master_hotel_event_id)->delete();
        }else if($request->type == 'child'){
            HotelEventRate::where('id', $request->id)->where('master_hotel_event_id', $request->master_hotel_event_id)->delete();
        }
    }
}
