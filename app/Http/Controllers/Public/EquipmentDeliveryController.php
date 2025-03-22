<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EquipmentDelivery;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;
use DB;
use Carbon\Carbon;

class EquipmentDeliveryController extends Controller
{
    const SPA_PATH = '/equipment';

    public function equipmentDelivery($deliveryCode)
    {
        $equipmentDeliveries = DB::table('equipment_deliveries')
        ->select(['equipment_deliveries.id','equipment_deliveries.delivery_code','participant.name'])
        ->join('participants', 'participant.id', 'equipment_deliveries.participant_id')
        ->where('delivery_code', $deliveryCode)->get();

        foreach ($equipmentDeliveries as $key => $value) {
            $value->equipments = DB::table('equipment_details')
            ->select([
                'equipment_details.equipment_id',
                'equipment_details.qty',
                'equipments.name',
                'equipment_units.name as unit_name',
                'equipment_details.status'
            ])
            ->join('equipments', 'equipment_details.equipment_id', 'equipments.id')
            ->join('equipment_units', 'equipments.unit_id', 'equipment_units.id')
            ->where('equipment_delivery_id', $value->id)->get();
        }
        return response()->json($equipmentDeliveries);
    }

    public function deliveryConfirmation(Request $request)
    {
        $equipmentDelivery = DB::table('equipment_deliveries')->where('id', $request->id)->first();
        if(!$equipmentDelivery) {
            return response()->json([
                'success' => false,
                'message'  => 'Invalid data',
            ], 422);
        }

        $equipments = json_decode($request->equipments);
        DB::transaction(function () use ($request, $equipmentDelivery, $equipments) {
            
            foreach ($equipments as $equipment) {
                $status = 2;
                if($equipment->status == false) {
                    $status = 1;
                }
                $equipmentForm = [
                    'status' => $status,
                    'received_by' => ($status == 2) ? $equipmentDelivery->participant_id : null,
                    'received_date' => ($status == 2) ? Carbon::now() : null,
                    'updated_at' => Carbon::now()
                ];

                DB::table('equipment_details')
                ->where('equipment_delivery_id', $equipmentDelivery->id)
                ->where('participant_id', $equipmentDelivery->participant_id)
                ->where('equipment_id', $equipment->equipment_id)
                ->update($equipmentForm);
            }

            $profilePhotoPath = null;
            if($request->image) {
                $imageMake = Image::make(file_get_contents($request->image));
                $img =  (string) $imageMake
                        ->resize(500, null, function ($constraint) {$constraint->aspectRatio();})
                        ->encode('webp');
                    
                $profilePhotoPath =  EquipmentDelivery::DIR_RECEIVED_EVIDENCE . Str::uuid() . '.webp';
                Storage::put($profilePhotoPath, $img);
            }
            
            EquipmentDelivery::where('id', $request->id)->update([
                'received_notes' => $request->received_notes,
                'delivery_status' => 4,
                'received_evidence' => $profilePhotoPath,
                'updated_at' => Carbon::now()
            ]);
        });

        return response()->json([
            'success' => true,
            'message'  => 'Jazakumullah khairan, data Anda telah kami simpan.'
        ], 200);
    }
}
