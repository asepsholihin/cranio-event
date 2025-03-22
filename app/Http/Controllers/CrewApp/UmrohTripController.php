<?php

namespace App\Http\Controllers\CrewApp;

use App\Http\Controllers\Controller;
use App\Models\Crew;
use App\Models\UmrohTrip;
use App\Models\EventAttendance;
use App\Models\Participant;
use App\Models\ParticipantUmrohTrip;
use App\Models\Attendance;
use App\Models\Baggage;
use App\Models\DetailBaggage;
use App\Models\PackageUmrohTrip;
use App\Models\Item;
use App\Models\AttendanceOpenRegistration;
use App\File\PDF\LetterParticipant as PDFLetterParticipant;
use App\Models\EventOpenRegistration;
use App\Models\LogAttendance;
use App\Models\LogLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Requests\CrewApp\Participant\StoreBaggageRequest;
use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

class UmrohTripController extends Controller
{
    public function __construct()
    {
        App::setLocale('id');
    }

    public function departures(Request $request)
    {
        $umrohTripPaginatedData = UmrohTrip::tableSearch()->paginate(10);
        $umrohTrips = $umrohTripPaginatedData->items();
        foreach($umrohTrips as $row) {
            $mutawwif = Participant::select('name')->find($row->mutawwif);
            $event = EventAttendance::select('event_date')->find($row->id);
            $row->mutawwif = $mutawwif->name ?? "";
            $row->manasik_date = $event->event_date ?? "";
        }

        return response()->json($umrohTripPaginatedData);
    }

    public function departureList(Request $request)
    {
        $umrohTrips = UmrohTrip::tableSearch()->limit(5)->get();
        $data = [];
        foreach($umrohTrips as $row) {
            $mutawwif = Participant::select('name')->find($row->mutawwif);
            $event = EventAttendance::select('event_date')->find($row->id);
            $row['mutawwif'] = $mutawwif->name ?? "";
            $row['manasik_date'] = $event->event_date ?? "";
            $data[] = $row;
        }

        return response()->json($data);
    }

    public function departureDetail($id, Request $request)
    {
        $response = UmrohTrip::find($id);
        $mutawwif = Participant::select('name')->find($response->mutawwif);
        $response['mutawwif'] = $mutawwif->name ?? "";

        $bags = Baggage::join('participant_umroh_trips', 'participant_umroh_trips.participant_id', '=', 'baggages.participant_id')
        ->where('baggages.umroh_trip_id', $id)->sum('total_bags','total_cabin');

        $response['total_bags'] = $bags->total_bags ?? 0;
        $response['total_cabin'] = $bags->total_cabin ?? 0;

        return response()->json($response);
    }

    public function departureParticipant(Request $request)
    {
        $perPage = request()->query('perPage', 10);
        $participant = ParticipantUmrohTrip::tableSearch()->paginate($perPage)->withQueryString();
        $data = [];
        foreach($participant as $row) {
            $baggage = Baggage::where('participant_id',$row->participant_id)->where('umroh_trip_id',$row->umroh_trip_id)->latest()->first();
            $row['baggage_id'] = ($baggage) ? $baggage->id : null;
            $row['total_bags'] = ($baggage) ? $baggage->total_bags : null;
            $row['total_cabin'] = ($baggage) ? $baggage->total_cabin : null;
            $row['baggage_notes'] = ($baggage) ? $baggage->notes : null;
            $lastLocation = 'Bandara Keberangkatan';
            if($baggage) {
                if($baggage->location_id == 1) $lastLocation = 'Bandara Keberangkatan';
                if($baggage->location_id == 2) $lastLocation = 'Checkout Hotel';
                if($baggage->location_id == 3) $lastLocation = 'Bandara Transit';
                if($baggage->location_id == 4) $lastLocation = 'Bandara Kepulangan';
            }
            $row['last_location'] = $lastLocation;
            $row['departure_from'] = Attendance::join('event_attendances', 'event_attendances.id', 'attendances.event_id')->where('participant_id', $row->participant_id)->where('event_attendances.umroh_trip_id', $row->umroh_trip_id)->first()->departure_from_update ?? null;

            $logAttendance = LogAttendance::join('participants', 'log_attendances.participant_id', '=', 'participant.id')
            ->select('log_attendances.created_at')
            ->where('summary_attendance_id', request()->query('summaryAttendanceId', 0))
            ->where('participant_id', $row->participant_id)->first();
            if($logAttendance)
                $row['check_in_at'] = date('d/m/Y H:i', strtotime($logAttendance->created_at));;

            $data[] = $row;
        }

        return response()->json($participant);
    }

    public function departureParticipantDetail($participantId, Request $request)
    {
        $participantUmrohTrip = ParticipantUmrohTrip::tableSearch()->where('participant_id', $participantId)->first();
        $participant = Participant::find($participantId);
        $baggage = Baggage::where('participant_id',$participantId)->where('umroh_trip_id',$participantUmrohTrip->umroh_trip_id)->latest()->first();
        $participant['packageName'] = $participantUmrohTrip->packageName;
        $participant['room_type'] = $participantUmrohTrip->room_type;
        $participant['group_hotel_room'] = $participantUmrohTrip->group_hotel_room;
        $participant['room_no_hotel_mekkah'] = $participantUmrohTrip->room_no_hotel_mekkah;
        $participant['room_no_hotel_madinah'] = $participantUmrohTrip->room_no_hotel_madinah;
        $participant['baggage_id'] = ($baggage) ? $baggage->id : null;
        $participant['total_bags'] = ($baggage) ? $baggage->total_bags : null;
        $participant['total_cabin'] = ($baggage) ? $baggage->total_cabin : null;
        $participant['baggage_notes'] = ($baggage) ? $baggage->notes : null;
        $participant['departure_seat'] = ($baggage) ? $participantUmrohTrip->departure_seat : null;
        $participant['return_seat'] = ($baggage) ? $participantUmrohTrip->return_seat : null;

        return response()->json($participant);
    }

    public function receiveBagSummary(Request $request)
    {
        $query = DB::table('baggages')->select(DB::raw('COALESCE(SUM(total_bags), 0) as total_bags'), DB::raw('COALESCE(SUM(total_cabin), 0) as total_cabin'))
        ->join('participants', 'participant.id','baggages.participant_id');
        if (!empty($request->umrohTripId)) {
            $query->where('umroh_trip_id', $request->umrohTripId);
        }
        if (!empty($request->package)) {
            $query->where('package_umroh_trip_id', $request->package);
        }
        if (!empty($request->q)) {
            $query->where('name', 'like', '%'.$request->q.'%');
        }
        $response = $query->first();

        return response()->json($response);
    }

    public function receiveBagDetail(Request $request)
    {
        $baggage = DB::table('baggages');
        if($request->baggageId) {
            $baggage->where('id', $request->baggageId);
        }
        if($request->locationId) {
            $baggage->where('location_id', $request->locationId);
        }
        if($request->cityId) {
            $baggage->where('city_id', $request->cityId);
        }
        if($request->umrohTripId) {
            $baggage->where('umroh_trip_id', $request->umrohTripId);
        }
        $response = $baggage->orderBy('id', 'desc')->first();

        if($response) {
            $location = 'Bandara Keberangkatan';
            if($response->location_id == 1) $location = 'Bandara Keberangkatan';
            if($response->location_id == 2) $location = 'Checkout Hotel';
            if($response->location_id == 3) $location = 'Bandara Transit';
            if($response->location_id == 4) $location = 'Bandara Kepulangan';

            $response->last_location = $location;
            $response->images = DetailBaggage::where('baggage_id', $response->id)->get();
        }

        return response()->json($response);
    }

    public function receiveBagDetailFromParticipant(Request $request, $participantId)
    {
        $baggage = DB::table('baggages');
        if($request->umrohTripId) {
            $baggage->where('umroh_trip_id', $request->umrohTripId);
        }
        $baggage->where('location_id', 5);
        $baggage->where('city_id', 7);
        $baggage->where('participant_id', $participantId);
        $response = $baggage->orderBy('id', 'desc')->first();

        if($response) {
            $location = 'Bandara Keberangkatan';
            if($response->location_id == 1) $location = 'Bandara Keberangkatan';
            if($response->location_id == 2) $location = 'Checkout Hotel';
            if($response->location_id == 3) $location = 'Bandara Transit';
            if($response->location_id == 4) $location = 'Bandara Kepulangan';

            $response->last_location = $location;
            $response->images = DetailBaggage::where('baggage_id', $response->id)->get();
        }

        return response()->json($response);
    }

    public function eventList(Request $request)
    {
        if($request->closeRegistration == "true") {
            $query = EventAttendance::tableSearch();
        } else {
            $query = EventOpenRegistration::tableSearch();
        }
        if($request->eventDate) {
            $query->where('event_date', '=', date('Y-m-d', strtotime($request->eventDate)));
        } else {
            $query->where('event_date', '>=', Carbon::now());
        }


        $events = $query->orderBy('event_date', 'desc')->get()->toArray();

        $eventsArr = array();
        foreach ($events as $value) {
            $value['close_registration'] = false;
            if($request->closeRegistration == "true") {
                $value['close_registration'] = true;
            }
            $value['id'] = strval($value['id']);
            $eventsArr[] = $value;
        }

        return response()->json($eventsArr);
    }

    public function eventParticipant(Request $request)
    {
        if($request->closeRegistration == "true") {
            $query = Attendance::tableSearch();
            if($request->manasikTable) {
                $query->where('manasik_table', 'iLike', '%'.$request->manasikTable.'%');
            }
            $query->orderByRaw('checkin asc, participant_umroh_trips.no_urut asc, qontak asc, participant_umroh_trips.manasik_table asc, crew asc');
        } else {
            $query = AttendanceOpenRegistration::tableSearch();
        }
        $participant = $query->get();

        return response()->json($participant);
    }

    public function manasikTables(Request $request)
    {
        $participant = Attendance::
        join('participant_umroh_trips', 'participant_umroh_trips.participant_id', '=', 'attendances.participant_id')
        ->where('event_id',request()->query('eventId', 0))
        ->whereNotNull('manasik_table')
        ->select('participant_umroh_trips.manasik_table')
        ->groupBy('participant_umroh_trips.manasik_table')
        ->get();

        return response()->json($participant);
    }

    public function receiveBag(StoreBaggageRequest $request)
    {
        DB::transaction(function () use($request) {
            Baggage::updateOrCreate(
                [
                    'location_id' => $request->location_id,
                    'city_id' => $request->city_id,
                    'umroh_trip_id' => $request->umroh_trip_id,
                    'package_umroh_trip_id' => $request->package_umroh_trip_id,
                    'participant_id' => $request->participant_id,
                ],
                $request->except(['photo'])
            );
            $baggageId = Baggage::where('location_id',$request->location_id)
            ->where('city_id',$request->city_id)->where('umroh_trip_id',$request->umroh_trip_id)
            ->where('package_umroh_trip_id',$request->package_umroh_trip_id)->where('participant_id',$request->participant_id)->first();

            // DetailBaggage::where('baggage_id', $baggageId->id)->delete();
            if($request->image) {
                foreach($request->image as $item) {
                    DetailBaggage::create(
                        [
                            'baggage_id' => $baggageId->id,
                            'image' => $item
                        ]
                    );
                }
            }
        });
        return response()->json(['success' => 'ok']);
    }

    public function deleteBaggageDetail(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);
        DetailBaggage::where('id', $request->id)->delete();
        return response()->json(['success' => 'ok']);
    }

    public function receiveBagList()
    {
        $baggages = Baggage::get();
        $response = [];
        foreach($baggages as $row) {
            $row['images'] = DetailBaggage::where('baggage_id', $row->id)->get();
            $response[] = $row;
        }
        return response()->json($response);
    }

    public function packages()
    {
        $packages = PackageUmrohTrip::where('umroh_trip_id', request()->query('umrohTripId', 0))->get();

        return response()->json($packages);
    }

    public function roomList()
    {
        $roomList = [];
        $package = request()->query('package', 0);
        $roomType = request()->query('roomType', 0);
        $keywords = request()->query('keywords', 0);
        $umrohTrip = UmrohTrip::find(request()->query('umrohTripId', 0));
        $queryGroupHotelRoom = ParticipantUmrohTrip::query()
        ->select([
            'participant_umroh_trips.group_hotel_room',
        ])
        ->join('participants', 'participant_umroh_trips.participant_id', '=', 'participant.id')
        ->where('participant_umroh_trips.umroh_trip_id', $umrohTrip->id)
        ->groupBy('group_hotel_room');
        if($package) {
            $queryGroupHotelRoom->where('participant_umroh_trips.package_umroh_trip_id', $package);
        }
        if($roomType) {
            $queryGroupHotelRoom->where('participant_umroh_trips.room_type', $roomType);
        }
        if($keywords) {
            $queryGroupHotelRoom->where(function($query) use ($keywords){
                $query->where('participant_umroh_trips.room_type', 'like', '%'.$keywords.'%');
                $query->orWhere('participant.name', 'like', '%'.$keywords.'%');
            });
        }
        $groupHotelRooms = $queryGroupHotelRoom->get();

        foreach($groupHotelRooms as $group) {
            $participant = ParticipantUmrohTrip::query()
            ->join('participants', 'participant_umroh_trips.participant_id', '=', 'participant.id')
            ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', '=', 'package_umroh_trips.id')
            ->join('umroh_trips', 'participant_umroh_trips.umroh_trip_id', '=', 'umroh_trips.id')
            ->select([
                'participant.name',
                'participant.gender',
                'participant.no_passport',
                'package_umroh_trips.name as package_name',
                'participant_umroh_trips.id',
                'participant_umroh_trips.room_type',
                'participant_umroh_trips.group_bus',
                'participant_umroh_trips.group_hotel_room',
                'participant_umroh_trips.room_group_notes',
                'participant_umroh_trips.room_no_hotel_mekkah',
                'participant_umroh_trips.room_no_hotel_madinah',
                'participant_umroh_trips.room_notes',
                'participant_umroh_trips.departure_seat',
                'participant_umroh_trips.return_seat',
            ])
            ->where('participant_umroh_trips.umroh_trip_id', $umrohTrip->id)
            ->where('participant_umroh_trips.group_hotel_room', $group->group_hotel_room)
            ->orderByRaw('room_type, group_hotel_room ASC NULLS LAST')->get();

            $group['package_name'] = ($participant) ? $participant[0]->package_name : null;
            $group['room_type'] = ($participant) ? $participant[0]->room_type : null;
            $group['room_group_notes'] = ($participant) ? $participant[0]->room_group_notes : null;
            $group['room_no_hotel_mekkah'] = ($participant) ? $participant[0]->room_no_hotel_mekkah : null;
            $group['room_no_hotel_madinah'] = ($participant) ? $participant[0]->room_no_hotel_madinah : null;
            $group['room_notes'] = ($participant) ? $participant[0]->room_notes : null;
            $group['departure_seat'] = ($participant) ? $participant[0]->departure_seat : null;
            $group['return_seat'] = ($participant) ? $participant[0]->return_seat : null;
            $group['participants'] = ($participant) ? $participant : null;
            $roomList[] = $group;
        }

        return response()->json($roomList);
    }

    public function updateParticipantRoom(Request $request)
    {
        $request->validate([
            'group_hotel_room' => 'required',
            'umroh_trip_id' => 'required',
            'type' => 'required',
            'room_no_hotel_mekkah' => 'required_if:type,1',
            'room_no_hotel_madinah' => 'required_if:type,2',
            'room_notes' => 'required_if:type,3',
        ]);
        ParticipantUmrohTrip::where('group_hotel_room', $request->group_hotel_room)
        ->where('umroh_trip_id', $request->umroh_trip_id)
        ->update($request->except(['type']));
        return response()->json(['success' => 'ok']);
    }

    public function updateParticipantSeat(Request $request)
    {
        $request->validate([
            'participant_id' => 'required',
            'umroh_trip_id' => 'required',
            'type' => 'required',
            'departure_seat' => 'required_if:type,1',
            'return_seat' => 'required_if:type,2'
        ]);


        $participantUmrohTrip = ParticipantUmrohTrip::where('participant_id', $request->participant_id)
        ->where('umroh_trip_id', $request->umroh_trip_id)->first();

        if($request->departure_seat) {
            $request->merge([
                'prev_departure_seat' => $participantUmrohTrip->departure_seat
            ]);
        } else {
            $request->merge([
                'prev_return_seat' => $participantUmrohTrip->return_seat
            ]);
        }

        $request->merge([
            'seat_updated_by' => auth()->user()->id,
            'seat_updated_at' => Carbon::now()
        ]);

        $participantUmrohTrip->update($request->except(['type','is_same']));
        return response()->json(['success' => 'ok']);
    }

    public function departureParticipantList(Request $request)
    {
        $participantUmrohTrips = ParticipantUmrohTrip::tableSearch()->orderBy('no_urut', 'desc')->get();

        return response()->json($participantUmrohTrips);
    }

    public function getUmrohByLinkDetail($slug){
        $getUmroh = UmrohTrip::get();
        foreach ($getUmroh as $key => $value) {
            if (hash('sha256', $value->id) === $slug) {
                $data['title'] = $value->title;
                $data['token'] = Crypt::encrypt($value->id);
                return response()->json($data);
                break;
            }
        }
        return [];
        // try {
        //     $has_decode = Crypt::decrypt($slug);
        //     $umroh = UmrohTrip::find($has_decode);
        //     return response()->json($umroh);
        // } catch (\Throwable $th) {
        //     return [];
        // }
    }

    public function checkParticipant(Request $request){
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
        if (Str::startsWith($phoneNumber, '62')) {
            $originPhone = substr($phoneNumber, 2);
        }
        if (Str::startsWith($phoneNumber, '0')) {
            $phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $phoneNumber);
            $originPhone = $phoneNumber;
        }
        $has_decode = Crypt::decrypt($request->token);
        $participantUmrohTrip = ParticipantUmrohTrip::where('umroh_trip_id', $has_decode)
                ->join('participants', 'participant.id', '=', 'participant_umroh_trips.participant_id')
                ->select('participant_umroh_trips.umroh_trip_id', 'participant_umroh_trips.participant_id',
                    'participant.name',
                    'participant.no_hp',
                    'participant.birth_date'
                    )->where('participant.no_hp', $originPhone)->get();
                    foreach($participantUmrohTrip as $jam){
                        unset($jam['id']);
                        $jam['umroh_trip_id'] = Crypt::encrypt($jam->umroh_trip_id);
                        $jam['participant_id'] = Crypt::encrypt($jam->participant_id);
                    }
        return response()->json($participantUmrohTrip);
    }

    public function generateFilePDF(Request $request){
        try {
            $participantId = Crypt::decrypt(request()->get('participant_id'));
            $umrohTripId = Crypt::decrypt(request()->get('umrohTripId'));

            $participantUmrohTrip = ParticipantUmrohTrip::where('umroh_trip_id', $umrohTripId)->where('participant_id', $participantId)->first();
            $participant = Participant::find($participantUmrohTrip->participant_id);
            if($participantUmrohTrip){
                try {
                    return (new PDFLetterParticipant(date('Y'), $participantUmrohTrip, $umrohTripId, request()->get('category'), true))->download();
                } catch (\Throwable $th) {
                    return response()->json([
                        'success' => false,
                        'message'  => 'Gagal Export',
                    ], 422);
                }
            }else{
                return response()->json([
                    'success' => false,
                    'message'  => 'Data Tidak Ditemukan',
                ], 422);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message'  => 'Error',
            ], 422);
        }
    }
}
