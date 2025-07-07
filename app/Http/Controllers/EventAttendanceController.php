<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\EventAttendance;
use App\Models\ParticipantUmrohTrip;
use App\Models\ParticipantBooking;
use App\Models\Participant;
use App\Models\Booking;
use App\Models\UmrohTrip;
use App\Models\OrderUmrohTrip;
use App\Models\LogQontakBroadcast;
use App\Models\BookingHotelEvent;
use App\Models\HotelEventRate;
use App\Models\EventTimeline;
use App\Models\MasterHotelEvent;
use App\Exports\ParticipantEventExport;
use App\Exports\ParticipantEventUpdateExport;
use App\Exports\AttendanceReportExport;
use App\Imports\ParticipantEventImport;
use App\File\PDF\AttendanceReport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventAttandance\BarcodeRegistration;
use App\Jobs\SendWhatsappQREventConfirmation;
use App\Jobs\SendWhatsappLinkEventConfirmation;
use App\Jobs\CreateBookingHotelEvent;
use App\Jobs\SendParticipantBarcodeEmail;
use App\File\Image\BarcodeParticipant;
use App\File\Image\BarcodeEventParticipant;
use App\File\Image\BarcodeText;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use DB;

class EventAttendanceController extends Controller
{
    const SPA_PATH = '/event-attendance';
    public function __construct()
    {
        App::setLocale('id');
        $this->middleware('permission:event-attendance-view')->only(['index', 'show', 'umrohTripSearch']);
        $this->middleware('permission:event-attendance-add-or-edit')->only(['store', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orderBy = request()->query('sortBy', 'event_date');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage', 10);
        return response()->json(
            EventAttendance::tableSearch()
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
            'name' => 'required|max:255',
            'event' => 'required|max:255',
            'event_date' => 'required|date',
        ]);

        if($request->open_gate_split) {
            $request->merge(['open_gate_split'=> 1, 'open_gate' => json_encode($request->open_gate)]);
        } else {
            $request->merge(['open_gate_split'=> 0, 'open_gate' => null]);
        }

        DB::transaction(function() use($request) {
            $eventAttendance = EventAttendance::updateOrCreate(['id' => $request->get('id')], $request->all());

            if ($request->has('copyEventId') && $request->copyEventId != 0) {
                $copyAttendance = Attendance::where('event_id', $request->copyEventId)->get();
                foreach ($copyAttendance as $attendee) {
                    Attendance::create(['event_id' => $eventAttendance->id, 'participant_id' => $attendee->participant_id]);
                }
            }

            $participantBooking = ParticipantBooking::join('bookings', 'participant_bookings.booking_id', 'bookings.id')
            ->where('bookings.order_status', Booking::STATUS_PAID)
            ->get();
            foreach ($participantBooking as $participant) {
                Attendance::firstOrCreate(
                    ['event_id' => $eventAttendance->id, 'participant_id' => $participant->participant_id],
                    ['event_id' => $eventAttendance->id, 'participant_id' => $participant->participant_id]
                );
            }
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EventAttendance $event
     * @return \Illuminate\Http\Response
     */
    public function show(EventAttendance $event_attendance)
    {
        if(request()->online == 'online') {
            $query = Attendance::selectRaw("COALESCE(SUM(DISTINCT(CASE WHEN check_in_at_online IS NOT NULL THEN 1 ELSE 0 END)), 0) AS total_checkin, COALESCE(SUM(DISTINCT(CASE WHEN confirm_at IS NOT NULL THEN 1 ELSE 0 END)), 0) AS total_confirm, count(distinct(attendances.participant_id)) AS total_attendance");
            $query->whereNotNull('session');
        } else {
            $query =  Attendance::selectRaw("COALESCE(SUM(CASE WHEN check_in_at IS NOT NULL THEN 1 ELSE 0 END), 0) AS total_checkin, COALESCE(SUM(DISTINCT(CASE WHEN confirm_at IS NOT NULL THEN 1 ELSE 0 END)), 0) AS total_confirm, count(distinct(attendances.participant_id)) AS total_attendance");
            $query->whereNull('session');
        }
        $attendee = $query->where('event_id', $event_attendance->id)->first()->toArray();
        $event = $event_attendance->toArray();
        return response()->json(array_merge($attendee, $event));
    }

    public function destroy(EventAttendance $event_attendance)
    {
        $event_attendance->delete();
    }

    public function umrohTripSearch(Request $request)
    {
        $request->validate(['q' => 'nullable', 'id' => 'nullable']);
        $search = $request->get('q');

        if (!is_null($request->get('id'))) {
            $result = UmrohTrip::select(['id', 'title'])
                ->where('id', $request->get('id'))
                ->get();
            return response()->json($result);
        }

        $search = '%' . $search . '%';
        $result = UmrohTrip::select(['id', 'title'])
            ->where('title', 'like', $search)
            ->orWhere('airlines', 'like', $search)
            ->orderBy('departure_at', 'desc')
            ->get();
        return response()->json($result);
    }

    public function updateManasikTable(Request $request)
    {
        $query = ParticipantUmrohTrip::where('umroh_trip_id', $request->umroh_trip_id);
        if($request->id) {
            $query->where('participant_id', $request->id);
        }
        if($request->participantIds) {
            $query->whereIn('participant_id', $request->participantIds);
        }
        $query->update(['manasik_table' => $request->manasik_table]);
    }

    public function departureConfirmationByAdmin(Request $request)
    {
        DB::transaction(function() use($request) {
            $attendance = Attendance::where('participant_id', request()->id)->where('event_id', request()->event_id)->whereNull('session')->first();    
            $attendance->update([
                'departure_from_update' => request()->departure_from_update,
            ]);
        });

        // return response()->json($response);
    }

    public function departureConfirmation(Request $request)
    {
        if (empty($request->participantIds)) {
            return response()->json([
                'success' => false,
                'message'  => 'Harap pilih participant yang akan dikonfirmasi',
            ], 422);
        }
        if(request()->hotel_name != "Rumah") {
            if (empty($request->checkin_date)) {
                return response()->json([
                    'success' => false,
                    'message'  => 'Harap pilih tanggal checkin hotel',
                ], 422);
            }
            if (empty($request->checkout_date)) {
                return response()->json([
                    'success' => false,
                    'message'  => 'Harap pilih tanggal checkout hotel',
                ], 422);
            }
        }

        DB::transaction(function() use($request) {
            $participants = DB::table('participants')->select('participants.id', 'participants.no_hp')
            ->join('attendances', 'participants.id', 'attendances.participant_id')
            ->whereNull('participants.deleted_at')
            ->whereIn('participants.id', request()->participantIds)
            ->where('attendances.event_id', request()->event_id)
            ->get();

            foreach ($participants as $key => $participant) {
                $participantUmrohTrip = ParticipantUmrohTrip::select('participant_umroh_trips.umroh_trip_id', 'participant_umroh_trips.room_type', 'participant_umroh_trips.group_bus','participant_umroh_trips.package_umroh_trip_id','participant_umroh_trips.order_umroh_trip_id')->where('participant_id', $participant->id)->where('umroh_trip_id', request()->umroh_trip_id)->first();
                $attendance = Attendance::where('participant_id', $participant->id)->where('event_id', request()->event_id)->first();    
                $attendance->update([
                    'package_umroh_trip_id' => $participantUmrohTrip->package_umroh_trip_id ?? null, 
                    'group_bus' => $participantUmrohTrip->group_bus ?? null, 
                    'departure_from' => request()->hotel_name, 
                    'departure_confirmation_at' => Carbon::now(),
                    'departure_confirmation_by' => $participant->id ?? null
                ]);

                if(request()->hotel_name != "Rumah") {
                    $room_price_pax = 0;
                    $additional_cost = 0;

                    $room_rate = HotelEventRate::find(request()->room_type);
                    $room_type = $room_rate->item_name ?? null;
                    $room_price_pax = $room_rate->item_price ?? 0;

                    if(request()->additional_item == "Bus") {
                        $additional_item = "Bus";
                        $additional_cost = 150000;
                    } else if(request()->additional_item == "Tidak membutuhkan tambahan") {
                        $additional_item = "-";
                        $additional_cost = 0;
                    } else {
                        $roomAds_rate = HotelEventRate::find(request()->additional_item);
                        $additional_item = $roomAds_rate->item_name;
                        $additional_cost = $roomAds_rate->item_price;
                    }

                    $checkInDate = (request()->checkin_date != "Invalid date") ? request()->checkin_date : null;
                    $checkOutDate = (request()->checkout_date != "Invalid date") ? request()->checkout_date : null;
                    $nights = Carbon::parse( $checkInDate )->diffInDays( $checkOutDate );
                    if($nights == 0) $nights = 1;
                    $total_rates = (intval($room_price_pax) * request()->total_room);
                    $total_adds = (intval($additional_cost) * request()->additional_pax);
                    $total_amount = ($total_rates + $total_adds) * $nights;

                    $formFill = [
                        'event_id' => request()->event_id,
                        'hotel_name' => request()->hotel_name,
                        'participant_id' => $participant->id,
                        'umroh_trip_id' => $participantUmrohTrip->umroh_trip_id ?? null,
                        'order_umroh_trip_id' => $participantUmrohTrip->order_umroh_trip_id ?? null,
                        'package_umroh_trip_id' => $participantUmrohTrip->package_umroh_trip_id ?? null,
                        'phone_number' => $participant->no_hp,
                        'checkin_date' => $checkInDate,
                        'checkout_date' => $checkOutDate,
                        'room_type' => $room_type,
                        'room_price_pax' => $room_price_pax,
                        'total_room' => request()->total_room,
                        'total_pax' => request()->total_pax,
                        'additional_item' => $additional_item,
                        'additional_pax' => request()->additional_pax,
                        'additional_cost'=> $additional_cost,
                        'total_amount' => $total_amount,
                        'payment_method' => "Xendit",
                        'created_by' => $participant->id ?? null,
                        'assigned_participant' => json_encode(request()->participantIds)
                    ];
                    if($total_amount > 0) {
                        CreateBookingHotelEvent::dispatch($formFill);
                    }
                }
            }
        });

        return response()->json([
            'success' => true,
            'message'  => 'Jazakumullah khairan, berhasil dikonfirmasi'
        ], 200);
    }

    public function participantDetail($id)
    {
        $attendance = Attendance::where('participant_id', $id)->first();
        $participant = ParticipantUmrohTrip::select('participants.name', 'participant_umroh_trips.manasik_table', 'participants.id')->join('participants', 'participant_umroh_trips.participant_id', 'participants.id')->where('participants.id', $attendance->participant_id)->first();
        return response()->json($participant);
    }

    public function participantUnattendeeList($eventId)
    {
        $participant = Attendance::select('participants.id', 'participants.name', 'check_in_at')
            ->join('participants', 'participants.id', 'attendances.participant_id')
            ->where('event_id', $eventId)
            ->get();

        return response()->json($participant);
    }

    public function generateEventLink(Request $request)
    {
        $eventAttendance = EventAttendance::find($request->id);
        $slug = Str::slug($eventAttendance->name, '-');

        $slugSession = "";
        $bodyUpdate = array();
        if($request->manasik_online) {
            $session = 1;
            if($eventAttendance->session != null) {
                $session = intval($eventAttendance->session) + 1;
            }

            $sessionInformation = json_decode($eventAttendance->session_information) ?? array();

            $bodyUpdate = array_merge($bodyUpdate, [
                'session' => $session,
                'session_information' => array_merge($sessionInformation, array(["session"=>$session,"created_at"=>Carbon::now()]))
            ]);
            $slugSession = "sesi-".$session;
        }

        $checkExistSlug = EventAttendance::whereNot('id', $request->id)->where('slug', $slug)->count();
        if ($checkExistSlug > 0) {
            $slug = $slug . "-" . $checkExistSlug + 1;
        }

        $bodyUpdate = array_merge($bodyUpdate, ['slug' => $slug]);

        $eventAttendance->update($bodyUpdate);

        if(!isset($request->manasik_online)) {
            if ($request->participantId) {
                SendWhatsappLinkEventConfirmation::dispatch($eventAttendance, $request->participantId);
            } else {
                $umrohTrip = UmrohTrip::find($eventAttendance->umroh_trip_id);
                $queryParticipants = Participant::select('participants.id')
                ->join('attendances', 'participants.id', 'attendances.participant_id')
                ->where('attendances.event_id', $eventAttendance->id)
                ->groupBy('participants.id');
                if ($request->participantIds) {
                    $queryParticipants->whereIn('participants.id', $request->participantIds);
                }
                $participants = $queryParticipants->get();
                
                foreach ($participants as $key => $participant) {
                    $logQontakBroadcast = LogQontakBroadcast::where('participant_id', $participant->id)
                    ->where('type', 'Link Event')->where('event_id',$eventAttendance->id)
                    ->where('status', 'Delivered')->first();
                    if(!$logQontakBroadcast) {
                        SendWhatsappLinkEventConfirmation::dispatch($eventAttendance, $participant->id)->delay(Carbon::now()->addSeconds(($key*30)));
                    }
                }
            }
            return "https://www.jejakimani.com/event-confirmation/" . $eventAttendance->slug . "/". $slugSession;
        }
        if($request->manasik_online) {
            return "https://www.jejakimani.com/manasik-online/" . $eventAttendance->slug . "/". $slugSession;
        }
    }

    public function generateDepartureConfirmationLink($eventId)
    {
        $eventAttendance = EventAttendance::find($eventId);
        if($eventAttendance->slug == null) {
            $slug = Str::slug($eventAttendance->name, '-');
            $checkExistSlug = EventAttendance::whereNot('id', $eventId)->where('slug', $slug)->count();
            if ($checkExistSlug > 0) {
                $slug = $slug . "-" . $checkExistSlug + 1;
            }
            $eventAttendance->update(['slug' => $slug]);
        }

        $text = "https://www.jejakimani.com/departure-confirmation/" . $eventAttendance->slug;

        return (new BarcodeText($text))->download();
    }

    public function chartAttendance(Request $request)
    {
        $query = Attendance::
        join('participants', 'participants.id', 'attendances.participant_id')
        ->join('event_attendances', 'event_attendances.id', 'attendances.event_id')
        ->join('participant_bookings', 'participant_bookings.participant_id', 'attendances.participant_id')
        ->where('event_id', $request->eventId);
        if ($request->booking) {
            $query->join('participant_bookings', 'participant_bookings.participant_id', 'attendances.participant_id')->where('participant_bookings.booking_order_no', $request->booking);
        }
        if ($request->online == 'online') {
            $query->selectRaw("
            COALESCE(SUM(CASE WHEN check_in_at_online IS NOT NULL THEN 1 ELSE 0 END), 0) AS total_checkin, (COUNT(DISTINCT(attendances.participant_id))-COALESCE(SUM(CASE WHEN check_in_at_online IS NOT NULL THEN 1 ELSE 0 END), 0)) as total_uncheckin,
            (SELECT COUNT(DISTINCT(absensi.participant_id)) FROM attendances as absensi WHERE event_id=".$request->eventId.") as total_attendance
            ");
            $query->whereNotNull('attendances.session');

            if ($request->session) {
                $query->where('attendances.session', 'sesi-'.$request->session);
            }
        } else {
            $query->selectRaw("COALESCE(SUM(CASE WHEN check_in_at IS NOT NULL THEN 1 ELSE 0 END), 0) AS total_checkin, COALESCE(SUM(DISTINCT(CASE WHEN check_in_at IS NULL THEN 1 ELSE 0 END)), 0) AS total_uncheckin, COUNT(DISTINCT(attendances.participant_id)) AS total_attendance");
            $query->whereNull('attendances.session');
        }
        $attendance = $query->first();
        $data = array();

        $data['categories'] = array('Total Checkin', 'Total Belum Checkin');
        $data['data'] = array(intval($attendance->total_checkin), intval($attendance->total_attendance - $attendance->total_checkin));

        return response()->json($data);
    }

    public function chartAttendanceConfirmation(Request $request)
    {
        $query = Attendance::selectRaw(
            "COALESCE(SUM(CASE WHEN confirm_at IS NOT NULL  THEN 1 ELSE 0 END), 0) AS total_confirm,
             COALESCE(SUM(CASE WHEN confirm_at IS NULL THEN 1 ELSE 0 END), 0) AS total_unconfirm"
        )
        ->join('participants', 'participants.id', 'attendances.participant_id')
        ->join('event_attendances', 'event_attendances.id', 'attendances.event_id')
        ->join('participant_bookings', 'participant_bookings.participant_id', 'attendances.participant_id')
        ->where('event_id', $request->eventId);
        $attendance = $query->first();

        $data = array();

        $data['categories'] = array('Sudah Konfirmasi', 'Belum Konfirmasi');
        $data['data'] = array(intval($attendance->total_confirm), intval($attendance->total_unconfirm));

        return response()->json($data);
    }

    public function chartAttendanceConfirmationByPackage(Request $request)
    {
        $query = Attendance::
        join('event_attendances', 'event_attendances.id', 'attendances.event_id')
        ->leftjoin('participant_umroh_trips', function ($join) {
            $join->on('participant_umroh_trips.participant_id', 'attendances.participant_id');
            $join->on('participant_umroh_trips.umroh_trip_id', 'event_attendances.umroh_trip_id');
        })
        ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
        ->where('event_id', $request->eventId);

        if ($request->online == 'online') {
            $query->select([
                DB::raw("COALESCE(SUM(CASE WHEN check_in_at_online IS NOT NULL THEN 1 ELSE 0 END), 0) AS total_checked"),
                DB::raw("COALESCE(SUM(CASE WHEN confirm_at IS NOT NULL  THEN 1 ELSE 0 END), 0) AS total_confirmed"),
                DB::raw("COALESCE(SUM(CASE WHEN confirm_at IS NULL THEN 1 ELSE 0 END), 0) AS total_unconfirmed"),
                DB::raw("package_umroh_trips.name as package_name"),
                DB::raw("(SELECT COUNT(DISTINCT(absensi.participant_id)) FROM attendances as absensi 
                JOIN event_attendances ON event_attendances.id=absensi.event_id
                JOIN participant_umroh_trips ON participant_umroh_trips.participant_id=absensi.participant_id AND participant_umroh_trips.umroh_trip_id=event_attendances.umroh_trip_id
                JOIN package_umroh_trips as package ON package.id=participant_umroh_trips.package_umroh_trip_id
                WHERE package.name=package_umroh_trips.name AND event_id=".$request->eventId.") as total_attendance"),
            ]);
            $query->whereNotNull('attendances.session');
            if($request->session) {
                $query->where('attendances.session', 'sesi-'.($request->session));
            }
        } else {
            $query->selectRaw("
            COALESCE(SUM(CASE WHEN check_in_at IS NOT NULL THEN 1 ELSE 0 END), 0) AS total_checked, 
            COALESCE(SUM(CASE WHEN check_in_at IS NULL THEN 1 ELSE 0 END), 0) AS total_unchecked, 
            COALESCE(SUM(CASE WHEN confirm_at IS NOT NULL  THEN 1 ELSE 0 END), 0) AS total_confirmed, 
            COALESCE(SUM(CASE WHEN confirm_at IS NULL THEN 1 ELSE 0 END), 0) AS total_unconfirmed, 
            COUNT(DISTINCT(attendances.participant_id)) AS total_attendance, package_umroh_trips.name as package_name");
            $query->whereNull('attendances.session');
        }

        if ($request->booking) {
            $query->where('participant_umroh_trips.booking_order_no', $request->booking);
        }

        $attendances = $query->groupBy('package_umroh_trips.name')->get();

        $data = array();
        $categories = array();
        $colors = array();
        $unconfirmed = array();
        $confirmed = array();
        $unchecked = array();
        $checked = array();

        foreach ($attendances as $value) {
            $categories[] = $value->package_name;
            $unconfirmed[] = $value->total_unconfirmed;
            $confirmed[] = $value->total_confirmed;
            $unchecked[] = ($value->total_attendance - $value->total_checked);
            $checked[] = $value->total_checked;
            
            if ($value->package_name == "Ruby") {
                $colors[] = '#dc3545';
            }
            if ($value->package_name == "Emerald") {
                $colors[] = '#28a745';
            }
            if ($value->package_name == "Sapphire Plus") {
                $colors[] = '#66b0ff';
            }
            if ($value->package_name == "Sapphire") {
                $colors[] = '#007bff';
            }
            if ($value->package_name == "Onyx") {
                $colors[] = '#d7b143';
            }
            if (str_contains($value->package_name, "Yaqin")) {
                $colors[] = '#d7b143';
            }
            if ($value->package_name == "Lebih Hemat") {
                $colors[] = '#ffc107';
            }
        }

        $participantIds = Attendance::select('attendances.participant_id')
        ->join('participants', 'participants.id', 'attendances.participant_id')
        ->join('event_attendances', 'event_attendances.id', 'attendances.event_id')
        ->join('participant_umroh_trips', function ($join) {
            $join->on('participant_umroh_trips.participant_id', 'attendances.participant_id');
            $join->on('participant_umroh_trips.umroh_trip_id', 'event_attendances.umroh_trip_id');
        })
        ->where('event_id', $request->eventId)
        ->get()->pluck('participant_id');

        $query = Attendance::whereNotIn('participant_id', $participantIds)->where('event_id', $request->eventId);
        if ($request->online == 'online') {
            $query->selectRaw("
            COALESCE(SUM(DISTINCT(CASE WHEN check_in_at_online IS NOT NULL THEN 1 ELSE 0 END)), 0) AS total_checked, 
            COUNT(DISTINCT(attendances.participant_id)) AS total_attendance,
            COALESCE(SUM(CASE WHEN confirm_at IS NOT NULL  THEN 1 ELSE 0 END), 0) AS total_confirmed, 
            COALESCE(SUM(CASE WHEN confirm_at IS NULL THEN 1 ELSE 0 END), 0) AS total_unconfirmed");
        } else {
            $query->selectRaw("
            COALESCE(SUM(CASE WHEN check_in_at IS NOT NULL THEN 1 ELSE 0 END), 0) AS total_checked, 
            COALESCE(SUM(CASE WHEN check_in_at IS NULL THEN 1 ELSE 0 END), 0) AS total_unchecked, 
            COALESCE(SUM(CASE WHEN confirm_at IS NOT NULL  THEN 1 ELSE 0 END), 0) AS total_confirmed, 
            COALESCE(SUM(CASE WHEN confirm_at IS NULL THEN 1 ELSE 0 END), 0) AS total_unconfirmed, 
            COUNT(DISTINCT(attendances.participant_id)) AS total_attendance");
        }
        $attendance_others = $query->first();
            
        array_push($categories, "Others");
        array_push($colors, "#28a745");
        array_push($unconfirmed, $attendance_others->total_unconfirmed);
        array_push($confirmed, $attendance_others->total_confirmed);
        array_push($unchecked, ($attendance_others->total_attendace - $attendance_others->total_checked));
        array_push($checked, $attendance_others->total_checked);

        $data['others'] = $attendance_others;
        $data['categories'] = $categories;
        $data['colors'] = $colors;
        $data['data_unconfirmed'] = $unconfirmed;
        $data['data_confirmed'] = $confirmed;
        $data['data_unchecked'] = $unchecked;
        $data['data_checked'] = $checked;

        return response()->json($data);
    }

    public function getEventDetail($slug)
    {
        $event = EventAttendance::select('event_attendances.*','umroh_trips.title', 'master_hotel_event.hotel_name','master_hotel_event.hotel_address')
        ->join('umroh_trips', 'umroh_trips.id', 'event_attendances.umroh_trip_id')
        ->leftjoin('master_hotel_event', 'master_hotel_event.id', 'event_attendances.transit_hotel_id')
        ->where('event_attendances.slug', $slug)->first();

        if($event) {
            $hotelRates = HotelEventRate::select('master_hotel_event_id', 'rate_category')->where('master_hotel_event_id', $event->transit_hotel_id)->groupBy('rate_category', 'master_hotel_event_id')->get();
            foreach($hotelRates as $rate){
                $rate->category_name = DB::table('hotel_event_rate_category')->where('id', $rate->rate_category)->first()->name ?? " - ";
                $rate->child = HotelEventRate::where('rate_category', $rate->rate_category)->where('master_hotel_event_id', $event->transit_hotel_id)->get();
            }
            $event->hotel_rates = $hotelRates;
        }

        return response()->json($event);
    }

    public function closedEvents(Request $request)
    {
        $query = EventAttendance::select(['event_attendances.id', 'event_attendances.umroh_trip_id', 'event_attendances.name','event_attendances.slug','event_attendances.event','event_attendances.event_date','event_attendances.location','umroh_trips.title'])
        ->join('umroh_trips', 'umroh_trips.id', 'event_attendances.umroh_trip_id');
        
        if ($request->date) {
            $query->where('event_date', $request->date);
        }
        
        $events = $query->get();
        
        return response()->json($events);
    }

    public function eventCheckParticipant(Request $request)
    {
        if (empty($request->no_hp)) {
            return response()->json([
                'success' => false,
                'message'  => 'Harap memasukkan Nomor HP yang terdaftar saat booking',
            ], 422);
        }

        if(!is_numeric($request->no_hp)) {
            return response()->json([
                'success' => false,
                'message'  => 'Nomor HP anda tidak valid',
            ], 422);
        }

        $phoneNumber = $request->no_hp;
        $originPhone = $phoneNumber;
        if (Str::startsWith($phoneNumber, '0')) {
            $phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $phoneNumber);
            $originPhone = substr($phoneNumber, 2);
        }
        if (Str::startsWith($phoneNumber, '62')) {
            $originPhone = substr($phoneNumber, 2);
        }

        $event = EventAttendance::where('slug', $request->slug)->first();
        if ($event->event_date < date('Y-m-d')) {
            return response()->json([
                'success' => false,
                'message'  => 'Event sudah berakhir',
            ], 422);
        }
        $participant = Attendance::select('participants.id', 'participants.no_hp')
            ->join('participants', 'participants.id', 'attendances.participant_id')
            ->where('event_id', $event->id)
            ->where('no_hp', 'like', '%' . $originPhone . '%')->first();

        if($participant) {
            $participantUmrohTrip = DB::table('participant_umroh_trips')->select('order_umroh_trip_id')
            ->where('participant_id', $participant->id)->first();
            $order = OrderUmrohTrip::select('order_umroh_trips.id')
            ->where('order_umroh_trips.id', $participant->order_umroh_trip_id)
            ->first();
            if ($order) {
                $participants = Participant::select('participants.id', 'participants.name', 'participants.no_hp', 'participants.birth_date', 'participants.profile_photo_path')
                    ->join('attendances', 'participants.id', 'attendances.participant_id')
                    ->join('participant_umroh_trips', 'participants.id', 'participant_umroh_trips.participant_id')
                    ->where('participant_umroh_trips.order_umroh_trip_id', $order->id)
                    ->where('attendances.event_id', $event->id)
                    ->groupBy('participants.id')
                    ->get();
            } else {
                $participants = Participant::select('participants.id', 'participants.name', 'participants.no_hp', 'participants.birth_date', 'participants.profile_photo_path')
                ->join('attendances', 'participants.id', 'attendances.participant_id')
                ->where('participants.no_hp', $phoneNumber)
                ->where('attendances.event_id', $event->id)
                ->groupBy('participants.id')
                ->get();
            }
        } else {
            return response()->json([
                'success' => false,
                'message'  => 'No. HP tidak terdaftar',
            ], 422);
        }

        foreach ($participants as $value) {
            $parentAttendance = DB::table('attendances')->select(['attendances.confirm_at', 'attendances.check_in_at' , 'attendances.check_in_at_online', 'attendances.confirm_by',  'attendances.session', 'attendances.departure_from_update', 'attendances.departure_confirmation_at'])
            ->where('event_id', $event->id)
            ->where('participant_id', $value->id)->first();
            $value->confirm_at = $parentAttendance->confirm_at;
            $value->check_in_at = $parentAttendance->check_in_at;
            $value->check_in_at_online = $parentAttendance->check_in_at_online;
            $value->confirm_by = $parentAttendance->confirm_by;
            $value->session = $parentAttendance->session;
            $value->departure_from_update = $parentAttendance->departure_from_update;
            $value->departure_confirmation_at = $parentAttendance->departure_confirmation_at;
            // if($value->check_in_at_online) {
                $attendances = DB::table('attendances')->select(['check_in_at_online', 'session'])
                ->where('event_id', $event->id)
                ->whereNotNull('check_in_at_online')
                ->where('participant_id', $value->id)->get();
                foreach ($attendances as $attendance) {
                    $attendance->session = str_replace("sesi-", "", $attendance->session);
                }
                $value->manasik_online = $attendances;
            // }
            $value->confirm_by_name = Participant::find($value->confirm_by)->name ?? '';
        }

        return response()->json($participants);
    }

    public function eventConfirmation(Request $request)
    {
        $event = EventAttendance::where('slug', $request->slug)->first();
        if ($event->event_date < date('Y-m-d')) {
            return response()->json([
                'success' => false,
                'message'  => 'Event sudah berakhir',
            ], 422);
        }
        $participant = Attendance::select('participants.id')->join('participants', 'participants.id', 'attendances.participant_id')->where('event_id', $event->id)->where('participants.id', $request->participant_id)->first();
        if (empty($participant)) {
            return response()->json([
                'success' => false,
                'message'  => 'Nomor HP yang tidak terdaftar',
            ], 422);
        }

        $phoneNumber = $request->no_hp;
        $originPhone = $phoneNumber;
        if (Str::startsWith($phoneNumber, '0')) {
            $phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $phoneNumber);
            $originPhone = substr($phoneNumber, 2);
        }
        if (Str::startsWith($phoneNumber, '62')) {
            $originPhone = substr($phoneNumber, 2);
        }
        $participantParent = Attendance::select('participants.id')->join('participants', 'participants.id', 'attendances.participant_id')->where('event_id', $event->id)->where('participants.no_hp', 'like', '%' . $originPhone . '%')->first();
        if (empty($participantParent)) {
            return response()->json([
                'success' => false,
                'message'  => 'Nomor HP yang tidak terdaftar',
            ], 422);
        }

        $attendance = Attendance::where('event_id', $event->id)->where('participant_id', $participant->id)->first();
        $attendance->update([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'confirm' => $request->confirm,
            'confirm_at' => Carbon::now(),
            'confirm_by' => $participantParent->id ?? $participant->id
        ]);

        if ($request->confirm == "hadir") {
            $participantDetail = Participant::find($participant->id);
            SendWhatsappQREventConfirmation::dispatch($event, $phoneNumber, $participantDetail->name, $participantDetail);
        }

        return response()->json([
            'success' => true,
            'message'  => 'Jazakumullah khairan, berhasil dikonfirmasi',
            'attendance' => $attendance
        ], 200);
    }

    public function closedEventConfirmation(Request $request)
    {
        $event = EventAttendance::where('slug', $request->slug)->first();
        if ($event->event_date < date('Y-m-d')) {
            return response()->json([
                'success' => false,
                'message'  => 'Event sudah berakhir',
            ], 422);
        }
        $participant = Attendance::select('participants.id')->join('participants', 'participants.id', 'attendances.participant_id')->where('event_id', $event->id)->where('participants.id', $request->participant_id)->first();
        if (empty($participant)) {
            return response()->json([
                'success' => false,
                'message'  => 'Nomor HP tidak terdaftar',
            ], 422);
        }

        $phoneNumber = $request->no_hp;
        $originPhone = $phoneNumber;
        if (Str::startsWith($phoneNumber, '0')) {
            $phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $phoneNumber);
            $originPhone = substr($phoneNumber, 2);
        }
        if (Str::startsWith($phoneNumber, '62')) {
            $originPhone = substr($phoneNumber, 2);
        }
        $participantParent = Attendance::select('participants.id')->join('participants', 'participants.id', 'attendances.participant_id')->where('event_id', $event->id)->where('participants.no_hp', 'like', '%' . $originPhone . '%')->first();
        if (empty($participantParent)) {
            return response()->json([
                'success' => false,
                'message'  => 'Nomor HP tidak terdaftar',
            ], 422);
        }

        $query = Attendance::where('event_id', $event->id);
        if(request()->session) {
            $query->where('session', request()->session);
        }
        $attendance = $query->where('participant_id', $participant->id)->first();
        if($attendance) {
            $attendance->update([
                'check_in_at_online' => Carbon::now(),
                'session' => request()->session??null
            ]);
        } else {
            $parentAttendance = Attendance::where('event_id', $event->id)->where('participant_id', $participant->id)->first();
            $parentAttendance->check_in_at_online = Carbon::now();
            $parentAttendance->session = request()->session??null;
            unset($parentAttendance->id);
            Attendance::create($parentAttendance->toArray());
        }

        return response()->json([
            'success' => true,
            'message'  => 'Jazakumullah khairan, berhasil dikonfirmasi'
        ], 200);
    }
    
    public function exportDeparture(Request $request) {

        $event = EventAttendance::find($request->eventId);
        $umrohTrip = UmrohTrip::find($event->umroh_trip_id);
        $fileName = $umrohTrip->title ?? '';

        $storageKey = "/Downloads/Participant-Manasik-Import-Template-" . $fileName . ".xlsx";
        Excel::store(new ParticipantEventExport($event, $umrohTrip), $storageKey);

        $expiredAt = now('UTC')->addDays(1);
        $params = [
            'Content-Disposition' => 'attachment'
        ];
        $presignedUrl = Storage::temporaryUrl($storageKey, $expiredAt, $params);
        return response()->json(['downloadLink' => $presignedUrl]);
    }

    public function exportDepartureUpdate(Request $request)
    {

        $event = EventAttendance::find($request->eventId);
        $umrohTrip = UmrohTrip::find($event->umroh_trip_id);
        $fileName = $umrohTrip->title ?? '';

        $storageKey = "/Downloads/Participant-Manasik-" . $fileName . ".xlsx";
        Excel::store(new ParticipantEventUpdateExport($event, $umrohTrip), $storageKey);

        $expiredAt = now('UTC')->addDays(1);
        $params = [
            'Content-Disposition' => 'attachment'
        ];
        $presignedUrl = Storage::temporaryUrl($storageKey, $expiredAt, $params);
        return response()->json(['downloadLink' => $presignedUrl]);
    }

    public function importDeparture(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|mimes:xlsx|file|max:1024'
        ]);

        $file = $request->file('file');

        $import = new ParticipantEventImport();
        $import->import($file);

        if (count($import->failures()) > 0) {
            return response()->json(['message' => $this->importErrorParse($import->failures())], 422);
        }

        $result = [
            'error' => false,
            'message' => 'Import Konfirmasi Keberangkatan Berhasil'
        ];
        return response()->json($result);
    }

    private function importErrorParse($errors)
    {
        $messages = [];
        foreach($errors as $error) {
            $messageErr = implode(', ', $error->errors());
            $no = $error->values()[0];
            $val = $error->values()[$error->attribute()];
            $messages[] = "#{$no}: ({$val}) {$messageErr}";
        }

        return implode('<br/><br/>', $messages);
    }

    public function refineLogQontakBroadcast($umrohTripId)
    {
        $queryParticipants = ParticipantUmrohTrip::select(['participant_id'])->where('participant_umroh_trips.umroh_trip_id', $umrohTripId);
        $participants = $queryParticipants->orderBy('participant_umroh_trips.created_at', 'ASC')->get();
        foreach ($participants as $key => $participant) {
            $logQontakBroadcast = LogQontakBroadcast::where('participant_id', $participant->participant_id)->update(['umroh_trip_id'=>$umrohTripId]);
        }
    }

    public function exportAttendanceReport(Request $request) {

        $event = EventAttendance::find($request->eventId);
        $umrohTrip = UmrohTrip::find($event->umroh_trip_id);
        $fileName = $umrohTrip->title ?? '';

        $storageKey = "Attendance-Report-" . $fileName . ".xlsx";
        if($request->type == "excel") {
            return Excel::download(new AttendanceReportExport($request->eventId), $storageKey);
        } else {
            return (new AttendanceReport($request->eventId))->download();
        }

    }

    public function checkBarcodeEvent(Request $request)
    {
        $participant = DB::table('participants')->select(['participants.id','participants.id as participant_id', 'barcode'])->where('id', $request->participantId)->first();
        $event = DB::table('event_attendances')->where('slug', $request->slug)->first();
        
        $barcode = (new BarcodeEventParticipant($participant, $event))->streamPublic();

        return response()->json(['barcode' => $barcode]);
    }

    public function sendBarcode(Request $request)
    {
        $event = EventAttendance::find($request->id);
        $participant = Participant::find($request->participantId);
        
        $email = new BarcodeRegistration($event, $participant);
        Mail::to($participant->email)->queue($email);

        return response()->json(['success' => true]);
    }

    public function multipleSendBarcode(Request $request)
    {
        $participants = Participant::whereIn('id', $request->participantIds)->get();

        foreach ($participants as $participant) {
            // Kirim sebagai job
            SendParticipantBarcodeEmail::dispatch($request->id, $participant->id);
        }
        
        return response()->json($participants);
    }

    public function chartManasikOnline(Request $request)
    {
        $event = EventAttendance::where('id', $request->eventId)->first();

        $sessions = [];
        for ($i=0; $i < $event->session; $i++) { 
            $attendance = Attendance::selectRaw("
            COALESCE(SUM(CASE WHEN check_in_at_online IS NOT NULL AND session = 'sesi-".($i+1)."' THEN 1 ELSE 0 END), 0) AS total_checked, 
            COALESCE(COUNT(DISTINCT(attendances.participant_id)), 0) AS total_attendance")
            ->where('event_id', $request->eventId)
            ->first();

            $attendance->date = "";
            if($event->session_information) {
                foreach(json_decode($event->session_information) as $session) {
                    if($session->session == ($i+1)) {
                        $attendance->date = Carbon::parse($session->created_at)->isoFormat('D MMMM Y');
                    }
                }
            }
            $attendance->title = 'Manasik Online Sesi ' . ($i+1);
            $attendance->session = ($i+1);
            $attendance->empty = false;
            if($attendance->total_checked == 0 && $attendance->total_unchecked == 0) {
                $attendance->empty = true;
            }
            $attendance->total_unchecked = ($attendance->total_attendance-$attendance->total_checked);
            $attendance->chartSeries = [$attendance->total_checked, ($attendance->total_attendance-$attendance->total_checked)];

            $attendance->chartOptions = [
                'chart' => [
                    'type' => 'donut',
                    'toolbar' => [
                        'show' => true
                    ]
                ],
                'legend' => array(
                    'position' => 'bottom'
                ),
                'labels'=> ['Total Checkin', 'Total Belum Checkin']
            ];
            
            $sessions[] = $attendance;
        }

        return response()->json($sessions);
    }
}
