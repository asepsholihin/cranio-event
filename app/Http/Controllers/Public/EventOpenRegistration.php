<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterEvent\SubmitRegister;
use App\Mail\EventAttandance\OpenRegistration;
use App\Models\Participant;
use App\Models\AttendanceOpenRegistration;
use App\Models\EventOpenRegistration as ModelsEventOpenRegistration;
use App\Models\EventTicketTransaction;
use App\Models\EventOpenSeat;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use SnappyImage;
use Carbon\Carbon;
use App\Jobs\SendWhatsappQREventOpenRegistration;
use App\Jobs\SendWhatsappTicketEvent;
use App\Actions\Xendit\Xendit;
use DB;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class EventOpenRegistration extends Controller
{
    public function events(Request $request)
    {
        $perPage = request()->get('perPage', 12);
        $query = ModelsEventOpenRegistration::select([
            'uuid', 'name', 'description', 'close_registration','speaker', 'event_date', 'event_at', 'event_end_at', 
            'location', 'is_paid_event', 'number_of_seats', 'price', 'slug', 'image_url', 'image_thumbnail_url'])
            ->where('close_registration', false)
            ->whereDate('event_date', '>=', now());

        if ($request->q) {
            $keywords = "%".$request->q."%";
            $query->where(function($q) {
                $q->where('name', 'like', $keywords)
                ->orWhere('speaker', 'like', $keywords)
                ->orWhere('location', 'like', $keywords)
                ->orWhere('event_date', 'like', $keywords);
            });
        }
        if ($request->paidEvent) {
            $paidEvent = 0;
            if($request->paidEvent == "true")
                $paidEvent = 1;
            $query->where('is_paid_event', $paidEvent);
        }

        $query->orderBy('event_date', 'ASC');
        $events = $query->paginate($perPage);

        // $data = array();
        // foreach ($events->items() as $event) {
        //     if($event->available_seats <= 0) {
        //         $data[] = $event;
        //     }
        // }

        return response()->json($events);
    }

    public function relatedEvents(Request $request)
    {
        $count = ModelsEventOpenRegistration::where('close_registration', false)->count();

        $randomCount = 1;
        if ($request->count <= $count) {
            $randomCount = $request->count;
        }

        $article = ModelsEventOpenRegistration::where('close_registration', false);

        if ($request->paidEvent) {
            $paidEvent = 0;
            if($request->paidEvent == "true")
                $paidEvent = 1;
            $article->where('is_paid_event', $paidEvent);
        }

        if ($request->exceptSlug !== "") {
            $article->whereNot('slug', $request->exceptSlug);
        }

        return response()->json(
            ($article->get()->isEmpty() == false) ? $article->get()->random($randomCount) : []
        );
    }

    public function show($eventId)
    {
        if (! Str::isUuid($eventId)) {
            return response()->json(['nothing' => 'here'], 404);
        }

        $eventOpenRegistration = ModelsEventOpenRegistration::select(['uuid', 'name', 'description', 'close_registration'])->find($eventId);

        if ($eventOpenRegistration == null) {
            return response()->json(['nothing' => 'here'], 404);
        }

        if ($eventOpenRegistration->close_registration) {
            return response()->json(['nothing' => 'here'], 404);
        }

        return response()->json($eventOpenRegistration->toArray());
    }

    public function getEventDetail($eventId)
    {
        if (! Str::isUuid($eventId)) {
            return response()->json(['nothing' => 'here'], 404);
        }

        $eventOpenRegistration = ModelsEventOpenRegistration::select(['uuid', 'name', 'description', 'close_registration'])->find($eventId);

        if ($eventOpenRegistration == null) {
            return response()->json(['nothing' => 'here'], 404);
        }

        if ($eventOpenRegistration->close_registration) {
            return response()->json(['nothing' => 'here'], 404);
        }

        return response()->json($eventOpenRegistration->toArray());
    }

    public function eventDetail($slug)
    {
        $eventOpenRegistration = ModelsEventOpenRegistration::select([
            'uuid', 'name', 'description', 'close_registration','speaker', 'event_date', 'event_at', 'event_end_at', 
            'location', 'is_paid_event', 'number_of_seats', 'price', 'slug', 'image_url', 'image_thumbnail_url'])->where('slug', $slug)->first();

        if ($eventOpenRegistration == null) {
            return response()->json(['nothing' => 'here'], 404);
        }

        if ($eventOpenRegistration->close_registration) {
            return response()->json(['nothing' => 'here'], 404);
        }

        return response()->json($eventOpenRegistration->toArray());
    }

    public function store(SubmitRegister $request)
    {
        $barcode = Str::uuid()->toString();
        $request->merge(['barcode' => $barcode]);
        $participant = AttendanceOpenRegistration::create($request->all());
        $fileName = $barcode . ".jpg";

        $img = SnappyImage::setOption('width', 100)->loadView('barcode.participant', compact('participant'));
        $img->save(storage_path('app/'.$fileName));
        $storageKey = AttendanceOpenRegistration::S3_PATH_BARCODE . "{$fileName}";
        Storage::put($storageKey, fopen(storage_path('app/'.$fileName), 'r'));
        unlink(storage_path('app/'.$fileName));
        $expiredAt = now('UTC')->addDays(1);
        $params = [
            'Content-Disposition' => 'attachment',
            'ResponseExpires' => $expiredAt->format('D, d M Y H:i:s \G\M\T')
        ];

        $email = new OpenRegistration($participant);
        Mail::to($participant->email)->queue($email);

        $presignedUrl = Storage::temporaryUrl($storageKey, $expiredAt, $params);
        return response()->json(['barcodeUrl' => $presignedUrl]);
    }

    public function eventRegistration(Request $request)
    {
        $barcode = Str::uuid()->toString();
        $phoneNumber = request()->no_hp;

        if(!is_numeric(request()->pax)) {
            return response()->json([
                'success' => false,
                'message'  => 'Mohon masukkan angka saja, Contoh: 5',
            ], 422);
        }

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

        if (request()->slug =="" || request()->name == "" || request()->no_hp == "") {
            return response()->json([
                'success' => false,
                'message'  => 'Harap masukkan data anda',
            ], 422);
        }

        $checkExist = AttendanceOpenRegistration::where('event_open_registration_id', request()->slug)
        ->where('no_hp', $phoneNumber)->first();
        if($checkExist) {
            return response()->json([
                'success' => false,
                'message'  => 'Nomor Whatsapp sudah terdaftar, mohon gunakan nomor lain.',
            ], 422);
        }

        DB::transaction(function() use($phoneNumber, $barcode) {
            $objAttendee = AttendanceOpenRegistration::create([
                'event_open_registration_id' => request()->slug,
                'barcode' => $barcode,
                'name' => request()->name,
                'no_hp' => $phoneNumber,
                'is_alumni' => request()->is_alumni,
                'pax' => request()->pax
            ]);
    
            $eventOpenRegistration = ModelsEventOpenRegistration::select(['uuid','name'])->find(request()->slug);
    
            for ($i=0; $i < request()->pax; $i++) {
                # code...
                $barcode = Str::uuid()->toString();
                $checkBarcodeExist = EventOpenSeat::where('barcode', $barcode)->first();
                if($checkBarcodeExist) {
                    $barcode = Str::uuid()->toString();
                }
    
                $eventOpenSeat = EventOpenSeat::create([
                    'event_id' => $eventOpenRegistration->uuid,
                    'parent_account_id' => $objAttendee->id,
                    'seat_name' => $objAttendee->name . " - " . ($i+1),
                    'seat_number' => Str::lower($objAttendee->name),
                    'gender' => 1,
                    'barcode' => $barcode
                ]);
    
                $fileName = Str::slug($objAttendee->name, '_') . "_barcode_" .Carbon::now()->timestamp. ".jpg";
    
                $img = SnappyImage::setOption('width', 100)->loadView('barcode.event_seat', compact(['eventOpenSeat', 'objAttendee']));
                $img->save(storage_path('app/'.$fileName));
                $storageKey = AttendanceOpenRegistration::S3_PATH_BARCODE . "/{$fileName}";
                Storage::put($storageKey, fopen(storage_path('app/'.$fileName), 'r'));
                
                $eventOpenSeat->barcode_thumbnail = $storageKey;
                $eventOpenSeat->save();
    
                SendWhatsappQREventOpenRegistration::dispatch($eventOpenRegistration->name, $phoneNumber, $objAttendee->name, $eventOpenSeat->barcode_thumbnail)->delay(Carbon::now()->addSeconds(($i*30)));
            }
        });

        return response()->json([
            'success' => true,
            'message'  => 'Jazakumullah khairan, berhasil daftar'
        ], 200);
    }

    public function eventBooking(Request $request)
    {
        $eventId = request()->event_id;
        $phoneNumber = request()->no_hp;
        $email = request()->email;

        if(!is_numeric($phoneNumber) || Str::length($phoneNumber) < 10) {
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

        if (request()->event_id =="" || request()->name == "" || request()->no_hp == "" || request()->email == "" || request()->pax == "") {
            return response()->json([
                'success' => false,
                'message'  => 'Harap lengkapi data anda',
            ], 422);
        }

        if (! Str::isUuid($eventId)) {
            return response()->json([
                'success' => false,
                'message'  => 'Event tidak tersedia',
            ], 422);
        }

        $event = ModelsEventOpenRegistration::find(request()->event_id);
        if(!$event) {
            return response()->json([
                'success' => false,
                'message'  => 'Event tidak tersedia',
            ], 422);
        }
        if($event->available_seats < 1) {
            return response()->json([
                'success' => false,
                'message'  => 'Tiket tidak tersedia',
            ], 422);
        }
        if(request()->pax > $event->available_seats) {
            return response()->json([
                'success' => false,
                'message'  => 'Tiket yang tersedia hanya tersisa ' . $event->available_seats,
            ], 422);
        }

        // Check Masih ada prores pembayarang yang belum selesai
        $checkIfStillProcess = EventTicketTransaction::where('no_hp', $phoneNumber)->where('event_id', request()->event_id)->where('transaction_status', 'WAITING PAYMENT')->first();
        if($checkIfStillProcess) {
            return response()->json([
                'success' => false,
                'message'  => 'Nomor Whatsapp sudah terdaftar, mohon selesaikan proses pembayaran tiket yang sudah kami kirimkan melalui Whatsapp dan Email.',
            ], 422);
        }

        $checkIfStillProcess = EventTicketTransaction::where('no_hp', $phoneNumber)->where('event_id', request()->event_id)->whereIn('transaction_status', ['WAITING PAYMENT', 'PAID'])->first();
        if($checkIfStillProcess) {
            return response()->json([
                'success' => false,
                'message'  => 'Nomor Whatsapp sudah terdaftar, mohon gunakan nomor lain.',
            ], 422);
        }

        if((request()->pax_akhwat + request()->pax_ikhwan) != request()->pax) {
            return response()->json([
                'success' => false,
                'message'  => 'Jumlah ikhwan dan akhwat tidak sesuai dengan jumlah pax: ' . request()->pax,
            ], 422);
        }

        $transaction = EventTicketTransaction::create([
            'event_id' => $event->uuid,
            'transaction_id' => Str::uuid()->toString(),
            'transaction_date' => Carbon::now(),
            'name' => request()->name,
            'no_hp' => $phoneNumber,
            'email' => request()->email,
            'is_alumni' => request()->is_alumni,
            'pax' => request()->pax,
            'total_payable' => (request()->pax * $event->price),
            'transaction_status' => "WAITING PAYMENT",
            'last_umroh_trip' => request()->last_umroh_trip,
            'notes' => request()->notes,
            'pax_ikhwan' => request()->pax_ikhwan,
            'pax_akhwat' => request()->pax_akhwat
        ]);
        $transaction->setTransactionCode();

        $body = [
            'external_id' => $transaction->transaction_id,
            'description' => $event->name,
            'amount' => $transaction->total_payable,
            'invoice_duration' => 900,
            'currency' => 'IDR',
            'reminder_time' => 1,
            "customer" => [
                "given_names" => $transaction->name,
                "surname" => $transaction->name,
                "email" => $transaction->email,
                "mobile_number" => $transaction->no_hp,
            ],
            "success_redirect_url" => "https://www.jejakimani.com/success-checkout-event/" . Crypt::encryptString($transaction->transaction_id),
            "failure_redirect_url" => "https://www.jejakimani.com",
            "items" => [
                [
                    "name" => "Tiket " . $event->name . " untuk " . $transaction->pax . " orang",
                    "quantity" => $transaction->pax,
                    "price" => $event->price,
                    "category" => "Event",
                    "url" => $event->slug
                ]
            ],
        ];

        $result = Xendit::createInvoice($body);
        if($result['invoice_url']){
            $transaction->update(['invoice_url' => $result['invoice_url']]);
        
            DB::table('log_event_ticket_transactions')->insert([
                'transaction_id' => $transaction->id,
                'transaction_status' => "WAITING PAYMENT",
                'payment_information' => $result,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        return response()->json([
            'success' => true,
            'transaction_id' => Crypt::encryptString($result['external_id']),
            'invoice_url' => $result['invoice_url'],
            'message'  => 'Jazakumullah khairan, berhasil daftar'
        ], 200);
    }

    public function eventBookingCallback(Request $request)
    {
        $transaction = EventTicketTransaction::where('transaction_id', $request->external_id)->first();
        if(!$transaction) {
            return response()->json([
                'success' => false,
                'message'  => 'Transaksi tidak tersedia',
            ], 422);
        }

        DB::transaction(function() use($transaction, $request) {
            DB::table('log_event_ticket_transactions')->insert([
                'transaction_id' => $transaction->id,
                'transaction_status' => $request->status,
                'payment_information' => json_encode($request->all()),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
    
            $transaction->update([
                'send_email_invoice' => 1,
                'transaction_status' => $request->status,
                'payment_method' => $request->payment_method
            ]);

            if($request->status == "PAID") {
                $barcode = Str::uuid()->toString();
                $checkBarcodeExist = AttendanceOpenRegistration::where('barcode', $barcode)->first();
                if($checkBarcodeExist) {
                    $barcode = Str::uuid()->toString();
                }

                $participant = AttendanceOpenRegistration::updateOrCreate(
                [
                    'event_open_registration_id' => $transaction->event_id,
                    'no_hp' => $transaction->no_hp,
                ],
                [
                    'event_open_registration_id' => $transaction->event_id,
                    'barcode' => $barcode,
                    'name' => $transaction->name,
                    'no_hp' => $transaction->no_hp,
                    'is_alumni' => $transaction->is_alumni,
                    'pax' => $transaction->pax,
                    'last_umroh_trip' => $transaction->last_umroh_trip,
                    'notes' => $transaction->notes,
                    'pax_ikhwan' => $transaction->pax_ikhwan,
                    'pax_akhwat' => $transaction->pax_akhwat
                ]);
        
                $fileName = Str::slug($participant->name, '_') . "_barcode_" .Carbon::now()->timestamp. ".jpg";
        
                $img = SnappyImage::setOption('width', 100)->loadView('barcode.participant', compact('participant'));
                $img->save(storage_path('app/'.$fileName));
                $storageKey = AttendanceOpenRegistration::S3_PATH_BARCODE . "/{$fileName}";
                Storage::put($storageKey, fopen(storage_path('app/'.$fileName), 'r'));
                
                $participant->barcode_thumbnail = $storageKey;
                $participant->save();

                // $event = ModelsEventOpenRegistration::find($transaction->event_id);
                // SendWhatsappTicketEvent::dispatch($event, $transaction->pax, $transaction->no_hp, $transaction->name, $participant->barcode_thumbnail);

                $transaction->update([
                    'send_ticket' => 1,
                ]);
            }
    
        });

        return response()->json([
            'success' => true,
            'message'  => 'Jazakumullah khairan'
        ], 200);
    }

    public function eventBookingSuccess($transactionId)
    {
        try {
            $transactionId = Crypt::decryptString($transactionId);
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message'  => 'Transaksi tidak tersedia',
            ], 422);
        }

        $transaction = EventTicketTransaction::where('transaction_id', $transactionId)->first();
        if(!$transaction) {
            return response()->json([
                'success' => false,
                'message'  => 'Transaksi tidak tersedia',
            ], 422);
        }

        $event = ModelsEventOpenRegistration::find($transaction->event_id);
        $transaction->event = $event;

        return response()->json($transaction);
    }
}
