<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Booking;
use App\Models\TempBooking;
use App\Models\BookingReceipt;
use App\Models\ParticipantBooking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exceptions\ErrorMessageException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\File\PDF\InvoiceBookingPDF;
use Image;
use DB;
use Carbon\Carbon;

class RegistrationController extends Controller
{
    public function __construct()
    {

    }

    public function registeredAccount($uuid)
    {
        $result = TempBooking::where('uuid', $uuid)->first();
        if (!$result) {
            return response()->json([
                'success' => false,
                'message'  => 'Data tidak ditemukan',
            ], 404);
        }

        $booking = Booking::where('temp_booking_id', $uuid)->first();
        if ($booking) {
            $result->booking = $booking;
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ], 200);
    }

    public function bookingDetail(Request $request) {
        if($request->account_wa) {
            $booking = Booking::where('account_wa', $request->account_wa)->first();
        }
        if($request->code) {
            $booking = Booking::where('temp_booking_id', $request->code)->first();
        }

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message'  => 'Data tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $booking
        ], 200);
    }

    public function postData(Request $request)
    {
        $request->validate([
            'account_name' => 'required',
            'account_email' => 'required',
            'account_hospital' => 'required',
            'total_pax' => 'required|numeric|min:1|max:10',
            'price_per_pax' => 'required|numeric',
            'total_price' => 'required|numeric',
            'package' => 'required',
            'account_wa' => 'required|numeric|digits_between:8,13|unique:bookings',
        ],[
            'account_wa.unique' => 'Nomor WA sudah terdaftar',
            'account_wa.numeric' => 'Nomor WA harus berupa angka',
            'account_wa.digits_between' => 'Nomor WA minimal 8 digit dan maksimal 13 digit',
            'total_pax.max' => 'Maksimal peserta 10 orang',
        ]);

        $result = DB::transaction(function() use($request) {
            $request->merge([
                'uuid' => Str::uuid(),
            ]);
            TempBooking::create($request->toArray());
            return $request->uuid;
        });

        return response()->json([
            'success' => true,
            'data' => $result
        ], 200);
    }

    public function postDataParticipant(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'participants' => 'required',
        ]);

        $tempBooking = TempBooking::where('uuid', $request->id)->first();
        if (!$tempBooking) {
            return response()->json([
                'success' => false,
                'message'  => 'Data tidak ditemukan',
            ], 404);
        }
        
        $result = DB::transaction(function() use($request, $tempBooking) {
            $bodyUpdateTempBooking = [];
            $totalPax = $tempBooking->total_pax;
            $totalPrice = ($tempBooking->price_per_pax * $tempBooking->total_pax);
            if($request->new_total_pax) {
                $bodyUpdateTempBooking['total_pax'] = $request->new_total_pax;
                $totalPax = $request->new_total_pax;
                $totalPrice = ($tempBooking->price_per_pax * $totalPax);
            }
            $bodyUpdateTempBooking['status'] = 1;
            $tempBooking->where('uuid', $request->id)->update($bodyUpdateTempBooking);
            $status = ($tempBooking->total_price == 0) ? 'paid' : 'unpaid';
            $booking = Booking::create([
                'temp_booking_id' => $request->id,
                'booking_no' => Str::uuid(),
                'account_name' => $tempBooking->account_name,
                'account_email' => $tempBooking->account_email,
                'account_wa' => $tempBooking->account_wa,
                'account_hospital' => $tempBooking->account_hospital,
                'total_pax' => $totalPax,
                'pax_assign' => $totalPax,
                'price_per_pax' => $tempBooking->price_per_pax,
                'total_price' => $totalPrice,
                'total_paid' => 0,
                'total_unpaid' => $totalPrice,
                'package' => $tempBooking->package,
                'order_status' => $status,
            ]);
            $booking->setOrderNumber();

            $participants = json_decode($request->participants);
            foreach ($participants as $value) {
                $value = (array) $value;
                
                $participant = Participant::where('nik', $value['nik'])->first();
                
                if($participant) {
                    $participant->update($value);
                } else {
                    $participant = Participant::create($value);
                }

                ParticipantBooking::updateOrCreate([
                    'booking_id' => $booking->id,
                    'participant_id' => $participant->id,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Data berhasil disimpan"
        ], 200);
    }

    public function postDataPaymentConfirmation(Request $request) {
        $request->validate([
            'payment_amount' => 'required|numeric|min:1|max:1000000000',
            'bank_account' => 'required',
            'sender_name' => 'required',
            'image' => 'required|file|mimes:jpg,png|max:5000'
        ], [
            'image.uploaded' => 'Gambar terlalu besar, maximal ukuran 5MB'
        ]);

        if ($request->hasFile('image')) {
            $imageMake = Image::make($request->file('image'));
            $img =  (string) $imageMake->encode('webp');

            $profilePhotoPath =  BookingReceipt::DIR_FILE . pathinfo($request->file('image')->hashName(), PATHINFO_FILENAME) . '.webp';
            Storage::put($profilePhotoPath, $img);
            $request->merge(['evidence' => $profilePhotoPath]);
        }

        BookingReceipt::updateOrCreate(['id' => $request->get('id')], $request->except(['image']));

        return response()->json([
            'success' => true,
            'message' => "Data berhasil disimpan"
        ], 200);
    }

    public function checkParticipant(Request $request)
    {
        $participant = Participant::where('nik', $request->nik)->first();
        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => "Data tidak ditemukan"
            ], 200);
        }

        return response()->json([
            'success' => true,
            'data' => $participant
        ], 200);
    }

    public function downloadInvoicePDF($uuid)
    {
        $booking = Booking::where('temp_booking_id', $uuid)->first();
        return (new InvoiceBookingPDF($booking))->download();
    }
}
