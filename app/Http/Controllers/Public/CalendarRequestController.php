<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CalendarRequest;
use App\Models\Participant;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class CalendarRequestController extends Controller
{
    public function __construct()
    {

    }

    public function postCalendarRequest(Request $request)
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

        if (request()->name == "" || request()->no_hp == ""
        || request()->province == "" || request()->city == ""
        || request()->district == "" || request()->subdistrict == "" || request()->postalcode == "" || request()->address == "") {
            return response()->json([
                'success' => false,
                'message'  => 'Harap lengkapi data anda',
            ], 422);
        }

        $result = DB::transaction(function() use($request) {
            $calendarRequest = CalendarRequest::where('no_hp', $request->no_hp)->first();
            if($calendarRequest) {
                throw new ErrorMessageException("Harap masukkan nomor HP yang berbeda");
            }

            $body = [
                'name' => $request->name,
                'recipient_name' => $request->name_receive,
                'no_hp' => $request->no_hp,
                'departure_year' => $request->departure_year,
                'province' => $request->province,
                'city' => $request->city,
                'district' => $request->district,
                'subdistrict' => $request->subdistrict,
                'postalcode' => $request->postalcode,
                'address' => $request->address,
                'haji_khusus' => $request->haji_khusus,
                'mitra' => $request->mitra,
                'status' => 'pending'
            ];

            $calendarRequest = CalendarRequest::create($body);

            return $calendarRequest;
        });

        return response()->json([
            'success' => true,
            'message'  => 'Jazakumullah khairan, permintaan kalender sedang disiapkan.'
        ], 200);
    }
}
