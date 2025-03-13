<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\AttendanceOpenRegistration;
use App\Models\EventOpenRegistration;
use App\Models\ParticipantUmrohTrip;
use Illuminate\Http\Request;
use App\File\Image\BarcodeText;
use Illuminate\Support\Facades\Storage;
use App\Support\StorageAttributes;
use Image;

class EventOpenRegistrationController extends Controller
{
    const SPA_PATH = '/event-open-registration';
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
        $orderBy = request()->query('sortBy', 'event_date');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage', 10);
        return response()->json(
            EventOpenRegistration::tableSearch()
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
            'name' => 'required',
            'description' => 'required',
            'location' => 'required',
            'event_date' => 'required|date',
            'speaker' => 'required',
            'number_of_seats' => 'numeric',
            'price' => 'numeric',
            'image' => 'nullable|file|mimes:jpg,png,webp|max:1536'
        ]);
        $is_paid_event = 0;
        if ($request->is_paid_event == "true") {
            $is_paid_event = 1;
        }
        $request->merge([
            'is_paid_event' => $is_paid_event
        ]);

        $uuid = $request->has('uuid')? $request->get('uuid'): Str::uuid();

        $slug = Str::slug($request->name, '-');
        $checkExist = EventOpenRegistration::where('slug', $slug)->count();
        if($checkExist > 0) {
            $slug = $slug . "-" . ($checkExist + 1);
        }
        $request->merge(['slug'=>$slug]);

        if ($request->hasFile(EventOpenRegistration::IMAGE)) {
            $img        = (string) Image::make($request->file(EventOpenRegistration::IMAGE))->encode('webp');
            $imgSmall   = (string) Image::make($request->file(EventOpenRegistration::IMAGE))->resize(200, null, function ($constraint) {$constraint->aspectRatio();})->encode('webp');

            $profilePhotoPath       =  EventOpenRegistration::DIR_IMAGE . pathinfo($request->file(EventOpenRegistration::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            $profilePhotoSmallPath  =  EventOpenRegistration::DIR_IMAGE . StorageAttributes::SMALL_IMG_PREFIX . pathinfo($request->file(EventOpenRegistration::IMAGE)->hashName(), PATHINFO_FILENAME) . '.webp';
            
            Storage::put($profilePhotoPath, $img);
            Storage::put($profilePhotoSmallPath, $imgSmall);
            
            $request->merge(['image_url' => $profilePhotoPath, 'image_thumbnail_url' => $profilePhotoSmallPath]);
        }

        EventOpenRegistration::updateOrCreate(['uuid' => $uuid], $request->except(['image']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EventOpenRegistration $event
     * @return \Illuminate\Http\Response
     */
    public function show(EventOpenRegistration $event_open_registration)
    {
        $attendee = AttendanceOpenRegistration::selectRaw("COALESCE(SUM(CASE WHEN event_open_seats.check_in_at IS NOT NULL THEN 1 ELSE 0 END), 0) AS total_checkin, COALESCE(COUNT(event_open_seats)) AS total_attendance, COALESCE(SUM(pax_ikhwan)) as pax_ikhwan, COALESCE(SUM(pax_akhwat)) as pax_akhwat")
                    ->where('event_open_registration_id', $event_open_registration->uuid)
                    ->leftJoin('event_open_seats', 'attendance_open_registrations.id', 'event_open_seats.parent_account_id')
                    ->groupBy('event_open_registration_id')
                    ->first();
        $event = $event_open_registration->toArray();
        if($attendee){
            return response()->json(array_merge($attendee->toArray(), $event));
        }
        return response()->json($event);
        
    }

    public function destroy(EventOpenRegistration $event_open_registration)
    {
        $event_open_registration->delete();
    }

    public function generateOpenRegistrationLink($eventId)
    {
        $event = EventOpenRegistration::find($eventId);
        $text = "https://www.jejakimani.com/event-registration/" . $event->uuid;

        return (new BarcodeText($text))->download();
    }

    public function chartAttendance(Request $request)
    {
        $query = AttendanceOpenRegistration::selectRaw("COALESCE(SUM(CASE WHEN event_open_seats.check_in_at IS NOT NULL THEN 1 ELSE 0 END), 0) AS total_checkin, COALESCE(SUM(CASE WHEN event_open_seats.check_in_at IS NULL THEN pax ELSE 0 END), 0) AS total_uncheckin")
        ->where('event_open_registration_id', $request->eventId)
        ->leftJoin('event_open_seats', 'attendance_open_registrations.id', 'event_open_seats.parent_account_id')
        ->groupBy('event_open_registration_id');
        $attendance = $query->first();

        $participant = AttendanceOpenRegistration::selectRaw("attendance_open_registrations.id, event_open_registration_id, name, no_hp, pax, SUM(CASE WHEN event_open_seats.check_in_at IS NOT NULL THEN 1 ELSE 0 END) as actual_pax")
        ->where('event_open_registration_id', $request->eventId)
        ->leftJoin('event_open_seats', 'attendance_open_registrations.id', 'event_open_seats.parent_account_id')
        ->groupBy('attendance_open_registrations.id')
        ->get();

        $data = array();
        $data['categories'] = array('Total Checkin', 'Total Belum Checkin');
        $data['data'] = array($attendance->total_checkin, $attendance->total_uncheckin);
        $data['participant'] = $participant;
           
        return response()->json($data);
    }

    public function chartParticipant(Request $request)
    {
        $query = AttendanceOpenRegistration::selectRaw("
        COALESCE(SUM(CASE WHEN is_alumni = TRUE THEN pax ELSE 0 END), 0) AS total_alumni,
        COALESCE(SUM(CASE WHEN is_alumni = FALSE THEN pax ELSE 0 END), 0) AS total_non_alumni
        ")
        ->where('event_open_registration_id', $request->eventId)
        ->groupBy('event_open_registration_id');
        $attendance = $query->first();

        $data = array();
        $data['categories'] = array('Total Participant', 'Total Non Participant');
        $data['data'] = array($attendance->total_alumni, $attendance->total_non_alumni);
        
        return response()->json($data);
    }

}
