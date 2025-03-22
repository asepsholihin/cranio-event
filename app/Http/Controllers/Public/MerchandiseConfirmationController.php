<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CalendarRequest;
use App\Models\ParticipantMerchandise;
use App\Models\Participant;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class MerchandiseConfirmationController extends Controller
{
    public function __construct()
    {

    }

    public function checkParticipant(Request $request)
    {
        $phoneNumber = request()->no_hp;
        if(!is_numeric($phoneNumber)) {
            return response()->json([
                'success' => false,
                'message'  => 'Nomor HP anda tidak valid',
            ], 422);
        }

        if (Str::startsWith($phoneNumber, '0')) {
            $phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $phoneNumber);
        } else {
            return response()->json([
                'success' => false,
                'message'  => 'Nomor HP anda tidak valid',
            ], 422);
        }

        $participant = ParticipantMerchandise::select('participant_merchandises.*')->join('participants', 'participant_merchandises.participant_id', 'participant.id')->where('participant.no_hp', $phoneNumber)->first();

        return response()->json($participant);
    }
    

    public function postData(Request $request)
    {
        if (request()->participant_id == "" || request()->province == "" || request()->city == ""
        || request()->district == "" || request()->subdistrict == "" || request()->postalcode == "" || request()->address == "") {
            return response()->json([
                'success' => false,
                'message'  => 'Harap lengkapi data anda',
            ], 422);
        }

        $result = DB::transaction(function() use($request) {
            $body = [
                'recipient_name' => $request->recipient_name,
                'province' => $request->province,
                'city' => $request->city,
                'district' => $request->district,
                'subdistrict' => $request->subdistrict,
                'postalcode' => $request->postalcode,
                'address' => $request->address,
                'status' => "confirmed"
            ];

            $participant = ParticipantMerchandise::updateOrCreate(['participant_id' => $request->participant_id], $body);

            return $participant;
        });

        return response()->json([
            'success' => true,
            'message'  => 'Jazakumullah Khoiron atas konfirmasi alamat penerimaan Anda.'
        ], 200);
    }
}
