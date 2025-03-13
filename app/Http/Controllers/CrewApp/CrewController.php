<?php

namespace App\Http\Controllers\CrewApp;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UmrohTrip;
use App\Models\EventAttendance;
use App\Models\EventOpenRegistration;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Image;
use DB;

class CrewController extends Controller
{
    public function __construct()
    {
        App::setLocale('id');
    }

    public function profile(Request $request)
    {
        return $request->user();
    }

    public function appInfo(Request $request)
    {
        $y = date('Y');
        $m = date('m');
        $d = date('d');
        $jd = GregoriantoJD($m, $d, $y);
        $l = $jd - 1948440 + 10632;
        $n = (int) (( $l - 1 ) / 10631);
        $l = $l - 10631 * $n + 354;
        $j = ( (int) (( 10985 - $l ) / 5316)) * ( (int) (( 50 * $l) / 17719)) + (
        (int) ( $l / 5670 )) * ( (int) (( 43 * $l ) / 15238 ));
        $l = $l - ( (int) (( 30 - $j ) / 15 )) * ( (int) (( 17719 * $j ) / 50)) - (
        (int) ( $j / 16 )) * ( (int) (( 15238 * $j ) / 43 )) + 29;
        $m = (int) (( 24 * $l ) / 709 );
        $d = $l - (int) (( 709 * $m ) / 24);
        $y = 30 * $n + $j - 30;
        
        $bulanHijriah = array(1 => "Muharram", "Shofar", "Robi'ul Awwal", "Robi'uts Tsani",
        "Jumadil Ula", "Jumadil Akhiroh", "Rojab", "Sya'ban",
        "Romadhon", "Syawwal", "Dzulqo'dah", "Dzulhijjah");

        $hijriah = $d .' '. $bulanHijriah[$m] .' '. $y . 'H';
        
        return [
            'indonesia_date' => Carbon::now()->isoFormat('dddd, D MMMM Y'),
            'hijri_date' => $hijriah,
        ];
    }

    public function updateProfile(Request $request)
    {
        try {
            User::where('id', $request->user()->id)->update($request->except(['photo']));
            $response = [
                'success' => true,
                'message' => 'Data saved'
            ];
        } catch(\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e
            ];
        }
        
        return response()->json($response);
    }

    public function updateProfilePhoto(Request $request)
    {
        try {
            if ($request->hasFile(User::PHOTO)) {
                $profilePhotoPath = $request->file(User::PHOTO)->store(User::DIR_PHOTO);
                $img = Image::make($request->file(User::PHOTO))
                        ->resize(200, null, function ($constraint) {$constraint->aspectRatio();})
                        ->encode('webp');
                Storage::put(User::DIR_THUMBNAIL . $profilePhotoPath, $img);
                $request->merge(['profile_photo_path' => $profilePhotoPath]);
            }
            User::where('id', $request->user()->id)->update($request->except(['photo']));
            $response = [
                'success' => true,
                'message' => 'Data saved'
            ];
        } catch(\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e
            ];
        }
        
        return response()->json($response);
    }

    public function nearestDeparture(Request $request) {
        $umrohTrips = UmrohTrip::where('departure_at', '>=', Carbon::now())->orderBy('departure_at', 'asc')->limit(5)->get();
        
        $response = array();
        foreach ($umrohTrips as $item) {
            $mutawwif = Participant::select('name')->find($item->mutawwif);
            $item['mutawwif'] =  $mutawwif->name ?? "-";
            $response[] = $item;
        }
        
        return response()->json($response);
    }

    public function nearestEvent(Request $request) {
        $closedEvent = EventAttendance::select(['*', DB::raw("true as close_registration")])->where('event_date', '>=', Carbon::now())->orderBy('event_date', 'asc')->limit(5)->get()->toArray();
        $closedEventArr = array();
        foreach ($closedEvent as $value) {
            $value['id'] = strval($value['id']);
            $closedEventArr[] = $value;
        }
        $openedEvent = EventOpenRegistration::select('*', 'uuid as id')->where('event_date', '>=', Carbon::now())->orderBy('event_date', 'asc')->limit(5)->get()->toArray();

        $events = array_merge($closedEventArr, $openedEvent);
        return response()->json($events);
    }
}
