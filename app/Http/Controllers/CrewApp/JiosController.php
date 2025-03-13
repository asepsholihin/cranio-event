<?php

namespace App\Http\Controllers\CrewApp;

use App\Exceptions\ErrorMessageException;
use App\Http\Controllers\Controller;
use App\Models\UmrohTrip;
use App\Models\Participant;
use App\Models\JiosSales;
use App\Models\Item;
use App\Models\ParticipantUmrohTrip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JiosController extends Controller
{
    public function __construct()
    {
        App::setLocale('id');
    }

    public function umrohTrips(Request $request)
    {
        $umrohTripPaginatedData = UmrohTrip::tableSearch()->paginate(5);
        $umrohTrips = $umrohTripPaginatedData->items();
        foreach($umrohTrips as $row) {
            $totalOrder = JiosSales::where('umroh_trip_id', $row->id)->get()->count();
            $totalItem = JiosSales::where('umroh_trip_id', $row->id)->sum('total_item');
            $totalTransaction = JiosSales::where('umroh_trip_id', $row->id)->sum('total_amount');
            $totalPaid = JiosSales::where('umroh_trip_id', $row->id)->sum('paid_amount');
            $row->total_order = (int)$totalOrder;
            $row->total_item = (int)$totalItem;
            $row->total_transaction = (int)$totalTransaction;
            $row->total_unpaid = (int)($totalTransaction - $totalPaid);
            $row->total_participant = $row->number_of_seats;
        }
        return response()->json($umrohTripPaginatedData);
    }

    public function umrohTripList(Request $request)
    {
        $list = UmrohTrip::tableSearch()->get();
        $data = [];
        foreach($list as $row) {
            $totalOrder = JiosSales::where('umroh_trip_id', $row->id)->get()->count();
            $totalItem = JiosSales::where('umroh_trip_id', $row->id)->sum('total_item');
            $totalTransaction = JiosSales::where('umroh_trip_id', $row->id)->sum('total_amount');
            $totalPaid = JiosSales::where('umroh_trip_id', $row->id)->sum('paid_amount');
            $row['total_order'] = $totalOrder;
            $row['total_item'] = $totalItem;
            $row['total_transaction'] = (int)$totalTransaction;
            $row['total_unpaid'] = (int)($totalTransaction - $totalPaid);
            $row['total_participant'] = $row->number_of_seats;
            $data[] = $row;
        }
        return response()->json($data);
    }

    public function jiosSalesList(Request $request)
    {
        $list = JiosSales::tableSearch()->get();
        $data = [];
        foreach($list as $row) {
            $participant = Participant::find($row->participant_id);
            $row['participant_name'] = $participant->name;
            $data[] = $row;
        }
        return response()->json($data);
    }

    public function jiosSalesDetail($id)
    {
        $jiosSales = JiosSales::find($id);
        $participant = ParticipantUmrohTrip::select([
           'participant.name', 'package_umroh_trips.name as package_name', 'participant_umroh_trips.room_type', 'participant_umroh_trips.group_hotel_room'  
        ])
        ->join('package_umroh_trips', 'participant_umroh_trips.umroh_trip_id', 'package_umroh_trips.umroh_trip_id')
        ->join('participant', 'participant.id', 'participant_umroh_trips.participant_id')->where('participant_umroh_trips.umroh_trip_id',$jiosSales->umroh_trip_id)->where('participant_id',$jiosSales->participant_id)->first();
        $jiosSales['notes'] = $jiosSales->notes??'Tidak ada catatan';
        $jiosSales['participant_name'] = $participant->name??'';
        $jiosSales['package_name'] = $participant->package_name??'';
        $jiosSales['room_type'] = $participant->room_type??'';
        $jiosSales['group_hotel_room'] = $participant->group_hotel_room??'';
        $jiosSales['mutawwif_name'] = Participant::find($jiosSales->mutawwif_id)->name??'';
        $jiosSales['items'] = Item::join('jios_sales_details', 'master_items.id', 'jios_sales_details.item_id')->where('jios_sale_id', $jiosSales->id)->get();
        
        return response()->json($jiosSales); 
    }

    public function itemList(Request $request)
    {
        $items = Item::select('master_items.*')
        ->join('master_categories', 'master_categories.id', 'master_items.category_id')
        ->where('master_items.status', 1)
        ->where('master_categories.name', 'Oleh-Oleh')
        ->get();
        
        return response()->json($items);
    }

    public function storeJiosSales(Request $request)
    {
        $validated = request()->validate([
            'umroh_trip_id' => 'required',
            'participant_id' => 'required',
            'items' => 'required|min:3',
        ],
        [
            'participant_id.required' => 'Participant belum dipilih',
            'items.min' => 'Produk belum dipilih'
        ]);

        DB::transaction(function () use($request) {
            $umrohTrip = UmrohTrip::find($request->umroh_trip_id);
            $mutawwif = Participant::where('id', $umrohTrip->mutawwif)->first();

            $invoiceNumber = JiosSales::generateCode();

            $request->merge([
                'mutawwif_id' => $mutawwif->id ?? 0,
                'invoice_number' => $invoiceNumber,
                'payment_method' => 1,
                'sales_status' => 1,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id
            ]);

            $jiosSales = JiosSales::create(
                $request->except('items')
            );

            foreach (json_decode($request->items) as $key => $row) {
                if ($row->qty == "0" || $row->qty == 0) {
                    throw new ErrorMessageException('Qty tidak boleh kosong');
                }

                $discountPerItem = 0;
                $totalPrice = ($row->qty * $row->price);
                DB::table('jios_sales_details')->insert([
                    'jios_sale_id' => $jiosSales->id,
                    'item_id' => $row->id,
                    'qty' => $row->qty,
                    'price_per_item' => $row->price,
                    'total_price' => $totalPrice,
                    'discount_per_item' => $discountPerItem,
                    'price_after_discount' => ($row->price - $discountPerItem),
                    'total_price_after_discount' => ($totalPrice - $discountPerItem)
                ]);
            }
        });

        return response()->json(['success' => 'ok']);
    }

    public function updateJiosSales(Request $request)
    {
        $jiosSales = JiosSales::find($request->jios_sale_id);
        $jiosSales->update([
            'sales_status'=>2, 
            'paid_amount'=>$jiosSales->total_amount, 
            'given_amount'=>$request->given_amount, 
            'change_amount'=>$request->change_amount
        ]);

        return response()->json(['success' => 'ok']);
    }
}
