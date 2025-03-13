<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BookingTransaction;
use App\Models\InvoiceUmrohTrip;
use App\Models\Participant;
use App\Models\ParticipantUmrohTrip;
use App\Models\OrderUmrohTrip;
use App\Models\OrderItemUmrohTrip;
use App\Models\LogMitraFee;
use App\Models\RoomUmrohTrip;
use App\Models\EventAttendance;
use App\Models\Attendance;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Actions\Xendit\Xendit;
use App\Jobs\SendWhatsappBookingInvoice;
use App\Jobs\SendWhatsappHistoryInvoice;
use Carbon\Carbon;
use SnappyImage;
use DB;

class BookingOrderController extends Controller
{
    public function invoiceDetail($transactionId)
    {
        $transaction = BookingTransaction::where('transaction_id', $transactionId)->orderBy('id', 'DESC')->first();
        if(!$transaction) {
            return response()->json([
                'success' => false,
                'message'  => 'Transaksi tidak tersedia',
            ], 422);
        }

        $invoice = InvoiceUmrohTrip::find($transaction->invoice_umroh_trip_id);
        $transaction->invoice = $invoice;

        return response()->json($transaction);
    }

    public function bookingDetail(Request $request)
    {
        $bookingOrder = strtoupper(str_replace("-","/",$request->booking_order));
        $order = DB::table('order_umroh_trips')->select('id','order_no','name','no_hp','due_payment','paid','umroh_trip_id','total_pax_trip','booking_pax_trip')->where('no_hp', $request->no_hp)->where('order_no', $bookingOrder)->whereNull('order_umroh_trips.deleted_at')->first();
        if(!$order) {
            return response()->json([
                'success' => false,
                'message'  => 'Transaksi tidak tersedia',
            ], 422);
        }
        $umrohTrip = DB::table('umroh_trips')->select('min_down_payment', 'currency')->where(['id' => $order->umroh_trip_id])->first();
        $minDownPayment = $umrohTrip->min_down_payment ?? 10000000;
        $minDownPayment = ($minDownPayment * $order->total_pax_trip);

        $description = "Sisa Pembayaran Anda " . $umrohTrip->currency ." ". number_format($order->due_payment, 0, ',', '.');
        if($order->paid < $minDownPayment) {
            $description = "Sisa Pembayaran DP " . $umrohTrip->currency ." ". number_format(($minDownPayment - $order->paid), 0, ',', '.');
        }
        
        $description_pelunasan = "Sisa Pembayaran Seluruhnya " . $umrohTrip->currency ." ". number_format($order->due_payment, 0, ',', '.');

        $order->min_down_payment = $minDownPayment;
        $order->text_notes = $description;
        $order->text_pelunasan = $description_pelunasan;
        $order->item_pax = OrderItemUmrohTrip::find($request->item_id)->booking_pax ?? 0;

        return response()->json($order);
    }

    public function generateInvoice(Request $request)
    {
        $order = DB::table('order_umroh_trips')->where('id', $request->booking_order_id)->first();
        if(!$order) {
            return response()->json([
                'success' => false,
                'message'  => 'Transaksi tidak tersedia',
            ], 422);
        }

        if($request->payment_amount <= 10000 && $request->pelunasan != 'true') {
            return response()->json([
                'success' => false,
                'message'  => 'Mohon maaf, minimal pembayaran harus lebih dari Rp10.000',
            ], 422);
        }

        if(!in_array($order->umroh_trip_id, [1,2,3])) {
            if($request->payment_amount > $order->due_payment) {
                return response()->json([
                    'success' => false,
                    'message'  => 'Melebihi sisa pembayaran',
                ], 422);
            }
        }

        $umrohTrip = DB::table('umroh_trips')->select('id','title','min_down_payment')->where(['id' => $order->umroh_trip_id])->first();
        $countInvoiceDP = DB::table('invoice_umroh_trips')->where('order_umroh_trip_id', $order->id)->where('description', 'like', '%dp%')->whereNot('status', 3)->whereNull('deleted_at')->count();
        $countInvoice = DB::table('invoice_umroh_trips')->where('order_umroh_trip_id', $order->id)->where('description', 'not like', '%dp%')->whereNot('status', 3)->whereNull('deleted_at')->count();
        $minDownPayment = $umrohTrip->min_down_payment ?? 10000000;
        $minDownPayment = ($minDownPayment * $order->total_pax_trip);

        if($order->paid < $minDownPayment) {
            if(($order->paid + $request->payment_amount) > $minDownPayment) {
                return response()->json([
                    'success' => false,
                    'message'  => 'Melebihi sisa pembayaran DP',
                ], 422);
            }
        }

        $paymentAmount = request()->payment_amount;

        $description = "Lanjutan ke-" . $countInvoice + 1;
        if(($order->paid + $request->payment_amount) < $minDownPayment) {
            $description = "DP Tahap " . $countInvoiceDP + 1;
        }
        if(($order->paid + $request->payment_amount) == $minDownPayment) {
            $description = "Pelunasan DP";
        }
        if(!in_array($umrohTrip->id, [1,2,3])) { //hanya berlaku untuk selain tanpa keberangkatan
            if($request->payment_amount == $order->due_payment) {
                $description = "Pelunasan";
                //SendWhatsappHistoryInvoice::dispatch($order);
            }
        }
        if($request->pelunasan == "true") {
            $description_pelunasan_dp = "";
            $dp_amount = ($minDownPayment - $order->paid);
            $total_amount = ($order->due_payment - $dp_amount);
            if($order->paid < $minDownPayment) {
                $description_pelunasan_dp = "Pelunasan DP dan ";
            }
            $description = $description_pelunasan_dp . "Pelunasan";
            $paymentAmount = $order->due_payment;
        }

        $orderFill = array_merge(request()->all(), [
            'invoice_no' => Str::uuid(),
            'order_umroh_trip_id' => $order->id,
            'description' => $description,
            'due_payment' => $order->due_payment,
            'status' => InvoiceUmrohTrip::STATUS_UNPAID
        ]);
        
        $result = DB::transaction(function () use ($order, $umrohTrip, $description, $paymentAmount) {
            $orderFill = [
                'name' => $order->name,
                'email' => $order->email,
                'no_hp' => $order->no_hp,
                'payment_amount' => 0,
                'payment' => $paymentAmount, 
                'invoice_no' => Str::uuid(),
                'order_umroh_trip_id' => $order->id,
                'description' => $description,
                'due_payment' => $order->due_payment,
                'status' => InvoiceUmrohTrip::STATUS_UNPAID
            ];

            $invoiceUmrohTrip = InvoiceUmrohTrip::create($orderFill);
            $invoiceUmrohTrip->setOrderNumber();

            // Booking Testing Xendit
            $externalId = strtoupper(str_replace("/","_",$invoiceUmrohTrip->invoice_no));
            $body = [
                'external_id' => $externalId,
                'description' => $umrohTrip->title .' - Invoice ' . $invoiceUmrohTrip->invoice_no,
                'amount' => $invoiceUmrohTrip->payment,
                'invoice_duration' => 86400,
                'currency' => 'IDR',
                'reminder_time' => 1,
                "customer" => [
                    "given_names" => $order->name,
                    "surname" => $order->name,
                    "mobile_number" => $order->no_hp,
                ],
                "success_redirect_url" => "https://www.jejakimani.com/success-checkout-booking/" . $externalId,
                "failure_redirect_url" => "https://www.jejakimani.com",
                "items" => [
                    [
                        "name" => $description,
                        "quantity" => 1,
                        "price" => $invoiceUmrohTrip->payment,
                    ]
                ],
            ];
            $result = Xendit::createInvoice($body);

            if($result['invoice_url']){
                $invoiceUmrohTrip->update(['invoice_url' => $result['invoice_url']]);
            
                DB::table('log_booking_umroh_trip_transactions')->insert([
                    'order_umroh_trip_id' => $order->id,
                    'invoice_umroh_trip_id' => $invoiceUmrohTrip->id,
                    'category' => "Invoice",
                    'transaction_id' => $externalId,
                    'transaction_status' => "WAITING PAYMENT",
                    'payment_information' => $result,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                SendWhatsappBookingInvoice::dispatch($order, $invoiceUmrohTrip);
            } else {
                throw new ErrorMessageException(json_encode($result));
            }

            return $invoiceUmrohTrip;
        });

        return response()->json([
            'success' => true,
            'invoice_url' => $result->invoice_url,
            'message'  => 'Jazakumullah khairan, Invoice berhasil di generate. Anda akan dialihkan ke halaman pembayaran.'
        ], 200);
    }

    public function assignParticipant(Request $request)
    {
        $order = OrderUmrohTrip::find($request->id);
        if(!$order) {
            return response()->json([
                'success' => false,
                'message'  => 'Transaksi tidak tersedia',
            ], 422);
        }

        $participants = json_decode($request->participants);

        foreach ($participants as $participant) {
            if($participant->name == "" || $participant->gender == "" || $participant->birth_date == "" || $participant->no_hp == "") {
                return response()->json([
                    'success' => false,
                    'message'  => 'Mohon lengkapi data bertanda *',
                ], 422);
            }
            if(!is_numeric($participant->no_hp)) {
                return response()->json([
                    'success' => false,
                    'message'  => 'No HP a/n '.$participant->name.' invalid, mohon hanya memasukkan angka saja',
                ], 422);
            }
            if(!is_numeric($participant->chest_size)) {
                return response()->json([
                    'success' => false,
                    'message'  => 'Lingkar Dada a/n '.$participant->name.' invalid, mohon hanya memasukkan angka saja',
                ], 422);
            }
            if(!is_numeric($participant->body_height)) {
                return response()->json([
                    'success' => false,
                    'message'  => 'Tinggi Badan a/n '.$participant->name.' invalid, mohon hanya memasukkan angka saja',
                ], 422);
            }
            if(($participant->nationality == "WNI" && $participant->nik == "" && !is_numeric($participant->nik))) {
                return response()->json([
                    'success' => false,
                    'message'  => 'NIK a/n '.$participant->name.' invalid, mohon hanya memasukkan angka saja',
                ], 422);
            }
            if(($participant->nationality == "WNI" && strlen($participant->nik) != 16)) {
                return response()->json([
                    'success' => false,
                    'message'  => 'NIK invalid, NIK a/n '.$participant->name.' harus 16 digit',
                ], 422);
            }
            if(($participant->nationality == "WNA" && $participant->kitas_number == "" && !is_numeric($participant->kitas_number))) {
                return response()->json([
                    'success' => false,
                    'message'  => 'KITAS a/n '.$participant->name.' invalid, mohon hanya memasukkan angka saja',
                ], 422);
            }
            try {
                Carbon::parse($participant->birth_date);
            } catch (\Carbon\Exceptions\InvalidFormatException $e) {
                return response()->json([
                    'success' => false,
                    'message'  => 'Tanggal lahir a/n '.$participant->name.' invalid',
                ], 422);
            }
            // Check NIK unique
            $checkNIK = Participant::select('name','nik')->where('nik', $participant->nik)->first();
            if($checkNIK) {
                return response()->json([
                    'success' => false,
                    'message'  => 'NIK a/n '.$checkNIK->name.' sudah terdaftar',
                ], 422);
            }
        }

        $orderItem = OrderItemUmrohTrip::find($request->v_item);
        if(!$orderItem) {
            return response()->json([
                'success' => false,
                'message'  => 'Transaksi tidak tersedia',
            ], 422);
        }

        $result = DB::transaction(function () use ($request, $order, $participants, $orderItem) {
            $bodyParticipant = array();
            foreach ($participants as $key => $participant) {
                $phoneNumber = $participant->no_hp;
                if (Str::startsWith($phoneNumber, '0')) {
                    $phoneNumber = Str::replaceFirst('0', 62, $phoneNumber);
                } else {
                    $phoneNumber = 62 . $phoneNumber;
                }
                $birthDate = Carbon::parse($participant->birth_date)->format('Y-m-d');
                $title = ($participant->gender == 1) ? "Mr" : "Ms";
                $price_per_pax = $orderItem->price;
                $total_discount = ($order->total_discount / $order->total_pax_trip);
                $price_after_discount = $price_per_pax - $total_discount;

                $package = DB::table('package_umroh_trips')->where('id', $orderItem->package_umroh_trip_id)->first();
                
                // Same Domisili
                if($participant->same_domisili) {
                    $participant->home_province = $participants[0]->home_province ?? null;
                    $participant->home_city = $participants[0]->home_city ?? null;
                    $participant->home_kecamatan = $participants[0]->home_kecamatan ?? null;
                    $participant->home_kelurahan = $participants[0]->home_kelurahan ?? null;
                    $participant->home_postalcode = $participants[0]->home_postalcode ?? null;
                    $participant->home_address = $participants[0]->home_address ?? null;
                }
                
                $needAssistance = $participant->need_assistance ?? null;
                $medicalRecord = isset($participant->medical_record) ? 'Ada' : 'Tidak Ada';
                $createParticipantForm = [
                    "no_hp" => $phoneNumber,
                    "name" => $participant->name,
                    "gender" => $participant->gender,
                    "birth_date" => $birthDate,
                    "nationality" => $participant->nationality,
                    "nik" => $participant->nik,
                    "kitas_number" => ($participant->kitas_number != "") ? $participant->kitas_number : null,
                    "home_province" => $participant->home_province ?? null,
                    "home_city" => $participant->home_city ?? null,
                    "home_kecamatan" => $participant->home_kecamatan ?? null,
                    "home_kelurahan" => $participant->home_kelurahan ?? null,
                    "home_postalcode" => $participant->home_postalcode ?? null,
                    "home_address" => $participant->home_address ?? null,
                    "body_size" => $participant->body_size,
                    "medical_record" => $medicalRecord,
                    "medical_description" => $participant->medical_record ?? null,
                    "chest_size" => ($participant->chest_size != "") ? $participant->chest_size : 0,
                    "body_height" => ($participant->body_height != "") ? $participant->body_height : 0,
                    "title" => $title,
                    "created_from" => Participant::CREATED_FROM_SPA,
                    "suggest_booking_order" => $order->order_no,
                    "suggest_package" => $package->name,
                    "suggest_room" => $orderItem->room_type,
                    "created_by" => $order->created_by_user_id,
                    "updated_by" => $order->created_by_user_id,
                ];

                $participant = Participant::create($createParticipantForm);
                $lastNoUrut = DB::table('participant_umroh_trips')->where('umroh_trip_id', $order->umroh_trip_id)->select('no_urut')->orderBy('no_urut', 'DESC')->first()->no_urut ?? 0;

                $createParticipantUmrohTripForm = [
                    'package_umroh_trip_id' => $orderItem->package_umroh_trip_id,
                    'room_type' => $orderItem->room_type,
                    'umroh_trip_id' => $order->umroh_trip_id,
                    'participant_id' => $participant->id,
                    'booking_order_no' => $order->order_no,
                    'order_item_umroh_trip_id' => $orderItem->id,
                    'order_umroh_trip_id' => $order->id,
                    'without_ticket' => $orderItem->without_ticket,
                    'ticket_type' => $orderItem->ticket_type,
                    'waiting_list' => $orderItem->waiting_list,
                    'price_per_pax' => $price_per_pax,
                    'discount' => $total_discount,
                    'price_after_discount' => $price_after_discount,
                    'no_urut' => $lastNoUrut + ($key+1),
                    "need_assistance" => $needAssistance,
                ];
    
                // Create Mitra Log Fee
                LogMitraFee::createLogFee($order->created_by_user_id, $orderItem->id);
                ParticipantUmrohTrip::create($createParticipantUmrohTripForm);
                $order->decrement('booking_pax_trip');
                $orderItem->decrement('booking_pax');
    
                // Check Room Group
                $lastRoomGroup = RoomUmrohTrip::join('participant_umroh_trips', 'participant_umroh_trips.id', 'room_umroh_trips.participant_umroh_trip_id')
                ->where('room_umroh_trips.umroh_trip_id', $order->umroh_trip_id)->where('participant_umroh_trips.package_umroh_trip_id', $orderItem->package_umroh_trip_id)
                ->select('participant_umroh_trips.group_hotel_room', 'room_umroh_trips.group_room')->orderBy('room_umroh_trips.group_room', 'DESC')->first();
    
                $maxRoomGroup = RoomUmrohTrip::join('participant_umroh_trips', 'participant_umroh_trips.id', 'room_umroh_trips.participant_umroh_trip_id')
                ->where('room_umroh_trips.umroh_trip_id', $order->umroh_trip_id)
                ->select('participant_umroh_trips.group_hotel_room', 'room_umroh_trips.group_room')->orderBy('room_umroh_trips.group_room', 'DESC')->first();
    
                $updateRoomGroup = "false";
                if($lastRoomGroup) {
                    if($maxRoomGroup->group_room > $lastRoomGroup->group_room) {
                        $updateRoomGroup = "true";
                        $roomUpdates = RoomUmrohTrip::where('room_umroh_trips.umroh_trip_id', $order->umroh_trip_id)->where('group_room', '>', $lastRoomGroup->group_room)->get();
                        foreach($roomUpdates as $room) {
                            ParticipantUmrohTrip::find($room->participant_umroh_trip_id)->update(['group_hotel_room' => ($room->group_room + 1)]);
                            RoomUmrohTrip::find($room->id)->update(['group_room' => ($room->group_room + 1)]);
                        }
                    }
                }   

                $checkEvent = EventAttendance::where('umroh_trip_id', $order->umroh_trip_id)->first();
                if($checkEvent) {
                    $fillAttendance = [
                        'event_id' => $checkEvent->id,
                        'participant_id' => $participant->id,
                        'umroh_trip_id' => $order->umroh_trip_id,
                        'package_umroh_trip_id' => $orderItem->package_umroh_trip_id
                    ];
                    $attendee = Attendance::updateOrCreate(['event_id' => $checkEvent->id, 'participant_id' => $participant->id], $fillAttendance);
                }
                
                $bodyParticipant[] = $participant;
            }
        });

        return response()->json([
            'success' => true,
            'message'  => 'Jazakumullah khairan, data Anda telah kami simpan.'
        ], 200);
    }
}
