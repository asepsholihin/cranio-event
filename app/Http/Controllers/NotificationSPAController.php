<?php

namespace App\Http\Controllers;

use App\Exceptions\ErrorMessageException;
use App\Models\Notification;
use App\Models\OrderUmrohTrip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Carbon\Carbon;

class NotificationSPAController extends Controller
{
    const SPA_PATH = 'notifications';
    public function __construct()
    {
        // $this->middleware('permission:booking-order-view')->only(['index']);
    }

    public function index()
    {
        $user = auth()->user();
        $query = Notification::tableSearch();
        if($user->id != 1) {
            $query->whereIn('notifications.department_id', $user->department_ids);
        }
        $notifications = $query->get();
        return response()->json(
            $notifications
        );
    }

    public function readNotification(Request $request)
    {
        $exist = DB::table('notification_read_log')->where('notification_id', $request->id)->where('read_by', auth()->user()->id)->first();
        
        if($exist === null) {
            DB::table('notification_read_log')->insert([
                'notification_id' => $request->id,
                'read_by' => auth()->user()->id,
                'read_at' => Carbon::now(),
                'created_at' => Carbon::now()
            ]);
        }

        $notif = Notification::find($request->id);

        if($notif->notification_type == 1) {
            $data = OrderUmrohTrip::select('order_umroh_trips.*','order_item_umroh_trips.package_umroh_trip_id')
            ->join('order_item_umroh_trips', 'order_umroh_trips.id', '=', 'order_item_umroh_trips.order_umroh_trip_id')->where('order_umroh_trips.id', $notif->order_umroh_trip_id)->first();
            return response()->json([
                'redirect' => 'participant',
                'umroh_trip_id' => $data->umroh_trip_id,
                'package_umroh_trip_id' => $data->package_umroh_trip_id,
                'booking_id' => $data->id,
                'order_no' => $data->order_no
            ]);
        }
    }

    public function readAllNotification(Request $request)
    {
        $notifications = Notification::select('notifications.id')->leftJoin('notification_read_log', 'notification_read_log.notification_id', '=', 'notifications.id')
        ->whereNull('notification_read_log.notification_id')->get();
        foreach ($notifications as $notification) {
            $exist = DB::table('notification_read_log')->where('notification_id', $notification->id)->where('read_by', auth()->user()->id)->first();
            
            if($exist === null) {
                DB::table('notification_read_log')->insert([
                    'notification_id' => $notification->id,
                    'read_by' => auth()->user()->id,
                    'read_at' => Carbon::now(),
                    'created_at' => Carbon::now()
                ]);
            }
        }
    }
}
