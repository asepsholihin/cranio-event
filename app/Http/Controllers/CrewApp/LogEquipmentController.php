<?php

namespace App\Http\Controllers\CrewApp;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\LogEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogEquipmentController extends Controller
{
    public function __construct()
    {
        App::setLocale('id');
    }

    public function equipmentList(Request $request)
    {
        $query = Equipment::select('sku_id')->whereNotNull('sku_id')->groupBy('sku_id');
        if($request->q) {
            $query->where(function($q) {
                $q->where('name', 'like', '%'.request()->q.'%');
                $q->orWhere('sku_id', 'like', '%'.request()->q.'%');
            });
        }
        
        $equipments = $query->limit(10)->get();

        foreach($equipments as $equipment) {
            $info = Equipment::where('sku_id', $equipment->sku_id)->first();
            $equipment->id = $info->id;
            $equipment->name = $info->name;
        }

        return response()->json($equipments);
    }

    public function postData(Request $request)
    {
        $items = json_decode(request()->items);
        // $items = json_decode(json_encode(request()->items));

        foreach ($items as $value) {
            if($value->qty == 0) {
                return response()->json([
                    'success' => false,
                    'message'  => 'QTY ' . $value->name .'-'. $value->sku . " kosong",
                ], 422);
            }
        }

        DB::transaction(function() use($items) {
            foreach ($items as $value) {
                $newLogo = 1;
                if($value->new_logo == 0 || $value->new_logo == 2) {
                    $newLogo = 2;
                }
                $equipment = Equipment::where('sku_id', $value->sku)->where('new_logo', $newLogo)->first();
                LogEquipment::create(
                [
                    'status' => request()->status, 
                    'new_logo' => $newLogo,
                    'sku_id' => $value->sku,
                    'equipment_id' => $equipment->id,
                    'qty' => $value->qty,
                    'notes' => request()->notes??'-',
                    'created_by' => Auth::id()
                ]);

                $equipment->update([
                    'qty' => $equipment->qty + $value->qty
                ]);
            }
        });

        return response()->json(['success' => 'ok']);
    }

}
