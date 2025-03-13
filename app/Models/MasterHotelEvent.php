<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterHotelEvent extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'master_hotel_event';

    protected $fillable = [
        'hotel_name',
        'hotel_city',
        'is_manasik',
        'is_transit',
        'hotel_pic',
        'hotel_pic_number',
        'hotel_map_url',
        'hotel_address',
        'manasik_hd_price',
        'manasik_fd_price',
        'prefer_for_manasik',
        'prefer_for_transit',
        'manasik_counter',
        'transit_counter',
        'status',
        'created_by',
        'updated_by',
        'updated_at',
        'deleted_at',
        'deleted_by',
    ];


    protected $appends = [
        'advantages',
        'disadvantages',
        'manasik_fd',
        'manasik_hd',
        'hotel_name_city',
        'updated_name',
        'hotel_rate',
        'counter_manasik_list',
        'counter_transit_list',
        'last_history',
        'newest_update'
    ];

    public function getNewestUpdateAttribute(){
        return $this->updated_at ?? " - ";
    }

    public function getHotelNameCityAttribute(){
        $data = City::where('id', $this->hotel_city)->first();
        return ucwords(strtolower($data->name)) ?? '';
    }

    public function getUpdatedNameAttribute(){
        $data = User::find($this->updated_by);
        return $data->name ?? '';
    }

    public function getAdvantagesAttribute()
    {
        $data = HotelEventCustomText::where('category', 'advantages')->where('master_hotel_event_id', $this->id)->get();
        return $data;
    }

    public function getDisadvantagesAttribute()
    {
        $data = HotelEventCustomText::where('category', 'disadvantages')->where('master_hotel_event_id', $this->id)->get();
        return $data;
    }

    public function getManasikFdAttribute()
    {
        $data = HotelEventCustomText::where('category', 'manasik_fd_price')->where('master_hotel_event_id', $this->id)->get();
        return $data;
    }

    public function getManasikHdAttribute()
    {
        $data = HotelEventCustomText::where('category', 'manasik_hd_price')->where('master_hotel_event_id', $this->id)->get();
        return $data;
    }

    public function getHotelRateAttribute(){
        $hotelEvents = DB::table('hotel_event_rate')->select('master_hotel_event_id', 'rate_category')->where('master_hotel_event_id', $this->id)->groupBy('rate_category', 'master_hotel_event_id')->get();
        foreach($hotelEvents as $event){
            $event->category_name = DB::table('hotel_event_rate_category')->where('id', $event->rate_category)->first()->name ?? " - ";
            $event->child = HotelEventRate::where('rate_category', $event->rate_category)->where('master_hotel_event_id', $this->id)->get();
        }
        return $hotelEvents;
    }

    public function getCounterManasikListAttribute(){
        $data = DB::table('hotel_event_counter')->leftJoin('umroh_trips', 'umroh_trips.id', 'hotel_event_counter.umroh_trip_id')->select('*', 'umroh_trips.title as departure_name')->where('master_hotel_event_id', $this->id)->where('purpose', 'manasik')->get();
        return $data;
    }

    public function getCounterTransitListAttribute(){
        $data = DB::table('hotel_event_counter')->leftJoin('umroh_trips', 'umroh_trips.id', 'hotel_event_counter.umroh_trip_id')->select('*', 'umroh_trips.title as departure_name')->where('master_hotel_event_id', $this->id)->where('purpose', 'transit')->get();
        return $data;
    }

    public function getLastHistoryAttribute(){
        $data = DB::table('hotel_event_counter')->where('master_hotel_event_id', $this->id)->orderBy('created_at', 'asc')->first();
        return $data->created_at ?? '';
    }

    public function scopeTableSearch($query)
    {
        if(!empty(request()->query('purpuses'))){
            if(request()->query('purpuses') == 2){
                $query->where('is_manasik', 'yes');
            }else if(request()->query('purpuses') == 3){
                $query->where('is_transit', 'yes');
            }else if(request()->query('purpuses') == 4){
                $query->where('is_transit', 'yes');
                $query->where('is_manasik', 'yes');
            }
        }
        if(!empty(request()->query('preference'))){
            if(request()->query('preference') == 1){
                $query->where('prefer_for_manasik', 'yes');
            }else if(request()->query('preference') == 2){
                $query->where('prefer_for_transit', 'yes');
            }else if(request()->query('preference') == 3){
                $query->where('prefer_for_manasik', 'yes');
                $query->where('prefer_for_transit', 'yes');
            }else if(request()->query('preference') == 4){
                $query->where('prefer_for_transit', 'no');
                $query->where('prefer_for_manasik', 'no');
            }
        }
        $search = '%' . request()->query('q') .'%';
        $query->where(function($q) use($search) {
            $q->where('hotel_name', 'like', $search)
            ->orWhere('hotel_pic', 'like', $search);
        });

        return $query;
    }
}
