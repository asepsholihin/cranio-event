<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceOpenRegistration;
use App\Models\Participant;
use App\Models\EventOpenSeat;
use App\Models\LogQontakBroadcast;
use App\Models\EventTicketTransaction;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\EventOpenRegistration as ModelsEventOpenRegistration;
use App\Jobs\SendWhatsappQREventOpenRegistration;
use App\Jobs\SendWhatsappTicketEvent;
use Illuminate\Support\Facades\Storage;
use SnappyImage;
use DB;
use ZipArchive;

class AttendeeOpenRegistrationController extends Controller
{
    const SPA_PATH = '/event-attendee-open-registration';
    public function __construct()
    {
        $this->middleware('permission:event-attendance-view')->only(['index','show']);
        $this->middleware('permission:event-attendance-add-or-edit')->only(['store', 'destroy']);
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
            AttendanceOpenRegistration::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required_if:act,add|required_if:act,checkinBarcode',
            'participant_id' => 'required_if:act,add',
            'barcode' => 'required_if:act,checkinBarcode|uuid',
            'id' => 'required_if:act,checkin',
            'act' => 'required|in:add,remove,checkin,checkinBarcode',
        ],['barcode.required' => 'Barcode is invalid','barcode.uuid' => 'Barcode is invalid']);
        if ($request->act == 'checkin') {
            $attendee = AttendanceOpenRegistration::find($request->get('id'));
            $this->toogleCheckIn($attendee, $request->pax ?? 0);
            return response()->json(['success' => 'ok']);
        }

        if ($request->act == 'checkinBarcode') {
            return $this->checkInBarcode($request);
        }

        AttendanceOpenRegistration::updateOrCreate(['event_id' => $request->event_id, 'participant_id' => $request->participant_id], $request->all());
        return response()->json(['success' => 'ok']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AttendanceOpenRegistration $event
     * @return \Illuminate\Http\Response
     */
    public function show(AttendanceOpenRegistration $event_attendee)
    {
        return response()->json($event_attendee->toArray());
    }

    public function destroy(AttendanceOpenRegistration $event_attendee)
    {
        $event_attendee->delete();
    }

    private function toogleCheckIn(AttendanceOpenRegistration $attendee, $pax=null)
    {
        if($pax) {
            EventOpenSeat::where('parent_account_id', $attendee->id)->update(['check_in_at' => null]);
            $eventOpenSeat = EventOpenSeat::where('parent_account_id', $attendee->id)->limit($pax)->get();
            foreach ($eventOpenSeat as $value) {
                // Begin
                $value->check_in_at = now();
                $value->save();
            }
        }

        // Check
        $eventOpenSeatCount = EventOpenSeat::where('parent_account_id', $attendee->id)->whereNotNull('check_in_at')->count();
        if ($eventOpenSeatCount > 0) {
            $attendee->check_in_at = now();
        } else {
            $attendee->check_in_at = null;
        }
        $attendee->save();
    }

    private function checkInBarcode(Request $request)
    {
        $attendee = AttendanceOpenRegistration::where('event_open_registration_id', $request->event_id)
                        ->where('barcode',$request->barcode)
                        ->first();
        
        if (! $attendee) {
            $checkFromOpenSeat = EventOpenSeat::select('*','seat_name as name')->where('event_id', $request->event_id)
            ->where('barcode', $request->barcode)
            ->first();

            if($checkFromOpenSeat) {
                if ($checkFromOpenSeat->check_in_at != null) {
                    $checkInAt = Carbon::parse($checkFromOpenSeat->check_in_at)->format('M j, Y g:i:s A');
                    throw ValidationException::withMessages(['barcode' => ["{$checkFromOpenSeat->name} checked in already {$checkInAt}"]]);
                }
        
                $checkIn = now();
                $checkFromOpenSeat->check_in_at = $checkIn;
                $checkFromOpenSeat->save();
                
                return response()->json(['checkInAt' => Carbon::parse($checkIn)->format('Y-m-d H:i:s'), 'attendee' => $checkFromOpenSeat]);
            }

            if (! $checkFromOpenSeat) {
                throw ValidationException::withMessages(['barcode' => ['Attendee is not in this event']]);
            }
        }

        if ($attendee->check_in_at != null) {
            $checkInAt = Carbon::parse($attendee->check_in_at)->format('M j, Y g:i:s A');
            throw ValidationException::withMessages(['barcode' => ["{$attendee->name} checked in already {$checkInAt}"]]);
        }

        $checkIn = now();
        $attendee->check_in_at = $checkIn;
        $attendee->save();
        return response()->json(['checkInAt' => Carbon::parse($checkIn)->format('Y-m-d H:i:s'), 'attendee' => $attendee]);
    }

    public function sendBarcode(Request $request)
    {
        if($request->send_to_all) {
            $eventOpenRegistration = ModelsEventOpenRegistration::find($request->event_id);
            if($eventOpenRegistration->is_paid_event) {
                $seats = EventOpenSeat::where('event_id', $eventOpenRegistration->uuid)->get();
                foreach ($seats as $key => $seat) {
                    $participant = AttendanceOpenRegistration::find($seat->parent_account_id);
                    $logQontakBroadcast = LogQontakBroadcast::where('no_hp', $participant->no_hp)->where('type', 'Ticket Event')->where('event_id',$eventOpenRegistration->uuid)->where('status', 'Delivered')->first();
                    if(!$logQontakBroadcast) {
                        SendWhatsappTicketEvent::dispatch($eventOpenRegistration, $participant->pax, $participant->no_hp, $participant->name, $seat->barcode_thumbnail)->delay(Carbon::now()->addSeconds(($key*30)));
                    }
                }
            }
        } else {
            $participant = AttendanceOpenRegistration::find($request->id);
    
            $eventOpenRegistration = ModelsEventOpenRegistration::find($participant->event_open_registration_id);
            if($eventOpenRegistration->is_paid_event) {
                $seats = EventOpenSeat::where('event_id', $eventOpenRegistration->uuid)->where('parent_account_id', $participant->id)->get();
                foreach ($seats as $key => $seat) {
                    SendWhatsappTicketEvent::dispatch($eventOpenRegistration, $participant->pax, $participant->no_hp, $participant->name, $seat->barcode_thumbnail)->delay(Carbon::now()->addSeconds(($key*30)));
                }
            } else {
                SendWhatsappQREventOpenRegistration::dispatch($eventOpenRegistration->name, $participant->no_hp, $participant->name, $participant->barcode_thumbnail);
            }
        }

        return response()->json(['success' => true]);
    }

    public function mappingSeatAttendeeList(Request $request)
    {
        $search = '%' . $request->get('q') .'%';
        
        $query = AttendanceOpenRegistration::select(['id','name', 'no_hp', 'pax', 'pax_ikhwan', 'pax_akhwat'])
        ->where(function($q) use($search) {
            $q->where('name', 'like', $search);
        });
        $query->where('event_open_registration_id', request()->get('eventId'));
        $query->whereNotIn('id', EventOpenSeat::pluck('parent_account_id'));
        $result = $query->get();
        return response()->json($result);
    }

    public function reportCheckinEventOpenSeat(Request $request)
    {
        $search = '%' . $request->get('q') .'%';
        
        $query = AttendanceOpenRegistration::join('event_open_seats', 'attendance_open_registrations.id', 'event_open_seats.parent_account_id')
        ->select(['attendance_open_registrations.id', 'event_open_registration_id' ,'name', 'no_hp'])
        ->where(function($q) use($search) {
            $q->where('name', 'like', $search);
        });
        $query->whereNotNull('event_open_seats.check_in_at');
        $query->where('event_open_registration_id', request()->get('eventId'));
        $checkins = $query->groupBy('attendance_open_registrations.id')->get();

        foreach($checkins as $value) {
            $value->checked = EventOpenSeat::where('parent_account_id', $value->id)->whereNotNull('event_open_seats.check_in_at')->count();
            $value->unchecked = EventOpenSeat::where('parent_account_id', $value->id)->whereNull('event_open_seats.check_in_at')->count();
        }

        return response()->json($checkins);
    }

    public function eventOpenSeatSettledList(Request $request)
    {
        $query = EventOpenSeat::where('event_id', $request->eventId);
        if($request->checkin) {
            $query->whereNotNull('check_in_at');
        }
        $seats = $query->get();
        
        return response()->json($seats);
    }

    public function postMappingSeatAttendee(Request $request)
    {
        $objAttendee = $request->attendee;
        $arraySeats = $request->seats;

        // Reset Semua Seat
        if($request->reset_all_seat) {
            EventOpenSeat::where('event_id', $request->event_id)->delete();
            return response()->json([
                'success' => true,
                'message'  => 'Kursi sudah direset',
            ], 200); 
        }

        // Reset Seat Per Participant
        if($request->reset_seat) {
            $parentSeat = EventOpenSeat::select('parent_account_id')->where('seat_number', $request->seat_number)->first();
            EventOpenSeat::where('event_id', $request->event_id)->where('parent_account_id', $parentSeat->parent_account_id)->delete();
            return response()->json([
                'success' => true,
                'message'  => 'Kursi sudah direset',
            ], 200); 
        }

        if(count($arraySeats) != $objAttendee['pax']) {
            return response()->json([
                'success' => false,
                'message'  => 'Jumlah kursi belum sesuai',
            ], 422);
        }

        DB::transaction(function() use($objAttendee, $arraySeats, $request) {
            foreach ($arraySeats as $seat) {
                $barcode = Str::uuid()->toString();
                $checkBarcodeExist = EventOpenSeat::where('barcode', $barcode)->first();
                if($checkBarcodeExist) {
                    $barcode = Str::uuid()->toString();
                }
    
                $gender = "Ikhwan";
                if($seat['gender'] == 2) {
                    $gender = "Akhwat";
                }
    
                $eventOpenSeat = EventOpenSeat::create([
                    'event_id' => $request->event_id,
                    'parent_account_id' => $objAttendee['id'],
                    'seat_name' => $seat['name'] . " - " . $gender,
                    'seat_number' => Str::lower($seat['name']),
                    'gender' => $seat['gender'],
                    'barcode' => $barcode
                ]);
    
                $fileName = Str::slug($objAttendee['name'], '_') . "_barcode_" .Carbon::now()->timestamp. ".jpg";
        
                $img = SnappyImage::setOption('width', 100)->loadView('barcode.event_seat', compact(['eventOpenSeat', 'objAttendee']));
                $img->save(storage_path('app/'.$fileName));
                $storageKey = AttendanceOpenRegistration::S3_PATH_BARCODE . "/{$fileName}";
                Storage::put($storageKey, fopen(storage_path('app/'.$fileName), 'r'));
                
                $eventOpenSeat->barcode_thumbnail = $storageKey;
                $eventOpenSeat->save();
            }
        });

        return response()->json($request->all());
    }
    
    public function postCheckinEventOpenSeat(Request $request)
    {
        $attendee = EventOpenSeat::where('event_id', $request->event_id)
                        ->where('barcode', $request->barcode)
                        ->first();
        if (! $attendee) {
            return response()->json([
                'success' => false,
                'message'  => "Attendee is not in this event",
            ], 422);
        }

        if ($attendee->check_in_at != null) {
            $checkInAt = Carbon::parse($attendee->check_in_at)->format('M j, Y g:i:s A');
            return response()->json([
                'success' => false,
                'message'  => "{$attendee->name} checked in already {$checkInAt}",
            ], 422);
        }

        $checkIn = now();
        $attendee->check_in_at = $checkIn;
        $attendee->save();
        return response()->json(['checkInAt' => Carbon::parse($checkIn)->format('Y-m-d H:i:s'), 'attendee' => $attendee]);
    }

    public function postAddAttendee(Request $request)
    {
        $barcode = Str::uuid()->toString();
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
            if(!request()->update) {
                return response()->json([
                    'success' => false,
                    'message'  => 'Nomor HP anda tidak valid',
                ], 422);
            }
        }

        if(!request()->update) {
            $checkExist = AttendanceOpenRegistration::where('event_open_registration_id', request()->event_id)
            ->where('no_hp', $phoneNumber)->first();
            if($checkExist) {
                return response()->json([
                    'success' => false,
                    'message'  => 'Nomor Whatsapp sudah terdaftar, mohon gunakan nomor lain.',
                ], 422);
            }
        }

        DB::transaction(function() use($phoneNumber, $barcode) {

            if(request()->update) {
                $transaction = EventTicketTransaction::
                where('id', request()->id)
                ->update([
                    'event_id' => request()->event_id,
                    'name' => request()->name,
                    'no_hp' => $phoneNumber,
                    'email' => request()->email??null,
                    'is_alumni' => request()->is_alumni?? false,
                    'pax' => request()->pax,
                    'total_payable' => (request()->pax * 300000),
                    'transaction_status' => "PAID",
                    'payment_method' => "BANK_TRANSFER",
                    'send_email_invoice' => 1,
                    'send_ticket' => 1,
                    'last_umroh_trip' => request()->last_umroh_trip,
                    'notes' => request()->notes,
                    'pax_ikhwan' => request()->pax_ikhwan??0,
                    'pax_akhwat' => request()->pax_akhwat??0
                ]);
    
                $participant = AttendanceOpenRegistration::where('id', request()->id)
                ->update([
                    'name' => request()->name,
                    'no_hp' => $phoneNumber,
                    'is_alumni' => request()->is_alumni ?? false,
                    'pax' => request()->pax,
                    'pax_akhwat' => request()->pax_akhwat??0,
                    'pax_ikhwan' => request()->pax_ikhwan??0,
                    'last_umroh_trip' => request()->last_umroh_trip,
                    'notes' => request()->notes,
                ]);
            } else {
                $transaction = EventTicketTransaction::create([
                    'event_id' => request()->event_id,
                    'transaction_id' => Str::uuid()->toString(),
                    'transaction_date' => Carbon::now(),
                    'name' => request()->name,
                    'no_hp' => $phoneNumber,
                    'email' => request()->email??null,
                    'is_alumni' => request()->is_alumni?? false,
                    'pax' => request()->pax,
                    'total_payable' => (request()->pax * 300000),
                    'transaction_status' => "PAID",
                    'payment_method' => "BANK_TRANSFER",
                    'send_email_invoice' => 1,
                    'send_ticket' => 1,
                    'last_umroh_trip' => request()->last_umroh_trip,
                    'notes' => request()->notes,
                    'pax_ikhwan' => request()->pax_ikhwan??0,
                    'pax_akhwat' => request()->pax_akhwat??0
                ]);
                $transaction->setTransactionCode();
    
    
                $participant = AttendanceOpenRegistration::create([
                    'event_open_registration_id' => request()->event_id,
                    'barcode' => $barcode,
                    'name' => request()->name,
                    'no_hp' => $phoneNumber,
                    'is_alumni' => request()->is_alumni ?? false,
                    'pax' => request()->pax,
                    'pax_akhwat' => request()->pax_akhwat??0,
                    'pax_ikhwan' => request()->pax_ikhwan??0,
                    'last_umroh_trip' => request()->last_umroh_trip,
                    'notes' => request()->notes,
                ]);
            }
        });

        return response()->json(['success' => true]);
    }

    public function refineAttendeePaidEvent(Request $request)
    {
        $attendees = AttendanceOpenRegistration::where('event_open_registration_id', request()->event_id)->get();
        foreach ($attendees as $key => $value) {
            $phoneNumber = $value->no_hp;
            if (Str::startsWith($phoneNumber, '0')) {
                $phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $phoneNumber);
            }
            
            $value->update([
                'created_at' => Carbon::now(),
                'barcode' => Str::uuid()->toString()
            ]);

            $transaction = EventTicketTransaction::updateOrCreate(
            [
                'event_id' => request()->event_id,
                'email' => $value->email,
                'no_hp' => $phoneNumber,
            ],    
            [
                'event_id' => request()->event_id,
                'transaction_id' => Str::uuid()->toString(),
                'transaction_date' => Carbon::now(),
                'name' => $value->name,
                'no_hp' => $phoneNumber,
                'email' => $value->email,
                'is_alumni' => $value->is_alumni,
                'pax' => $value->pax,
                'total_payable' => ($value->pax * 300000),
                'transaction_status' => "PAID",
                'payment_method' => "BANK_TRANSFER",
                'send_email_invoice' => 1,
                'send_ticket' => 1,
                'last_umroh_trip' => $value->last_umroh_trip,
                'notes' => $value->notes,
                'pax_ikhwan' => $value->pax_ikhwan,
                'pax_akhwat' => $value->pax_akhwat
            ]);
            $transaction->setTransactionCode();
        }
    }

    public function downloadBarcode(Request $request)
    {
        $eventOpenRegistration = ModelsEventOpenRegistration::find($request->event_id);
        if($eventOpenRegistration->is_paid_event) {
            $seats = EventOpenSeat::where('event_id', $eventOpenRegistration->uuid)->get();
            
            $zip = new ZipArchive;
            $fileName = "download-barcodes-" . $eventOpenRegistration->slug . "-" . hrtime(true) . ".zip";
            if ($zip->open(storage_path('app/' . $fileName), ZipArchive::CREATE) === TRUE) {
                $this->addZipBarcodes($zip, $seats);
            }

            $zip->close();
            if (!file_exists(storage_path('app/' . $fileName))) {
                return response()->json([
                    'success' => false,
                    'message'  => 'There is no file to be downloaded',
                ], 422);
            }
            $storageKey = "/Downloads/{$fileName}";
            Storage::put($storageKey, fopen(storage_path('app/' . $fileName), 'r'));
            unlink(storage_path('app/' . $fileName));

            $expiredAt = now('UTC')->addDays(1);
            $params = [
                'Content-Disposition' => 'attachment',
                'ResponseExpires' => $expiredAt->format('D, d M Y H:i:s \G\M\T')
            ];

            $presignedUrl = Storage::temporaryUrl($storageKey, $expiredAt, $params);
            return response()->json(['downloadLink' => $presignedUrl]);
        }
    }

    private function addZipBarcodes($zip, $seats)
    {
        foreach ($seats as $file) {
            $participant = AttendanceOpenRegistration::find($file->parent_account_id);
            $path = "Barcodes/".$participant->name."/".$file->seat_name.".jpg";
            $fileLocation = Str::replace('https://www.jejakimani.com/','',$file->barcode_thumbnail);
            $zip->addFromString($path, Storage::get($fileLocation));
        }
    }

}
