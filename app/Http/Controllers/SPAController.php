<?php

namespace App\Http\Controllers;

use App\Actions\Dashboard\Statistic;
use App\Actions\Dashboard\Booking;
use App\Models\InvoiceUmrohTrip;
use App\Models\OrderItemUmrohTripChangeRequest;
use App\Models\PaymentApprovalHeader;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SPAController extends Controller
{
    public function index()
    {
        return view('app');
    }

    public function dashboard()
    {
        return response()->json(
            [
                'statistics' => (new Statistic)(),
                'bookings' => (new Booking)()
            ]
        );
    }

    public function countBadgeNav()
    {
        $start = Carbon::now()->subDays(7);
        $end = Carbon::now();

        return response()->json(
            [
                'creditReceipt' => 0,
                'orderChangeRequest' => 0,
                'paymentApproval' => 0
            ]
        );
    }

    public function userAuth()
    {
        return response()->json(['authUser' => Auth::user()->toArray()]);
    }

    public function directorDashboard(){
        return response()->json(
            [
                'statistics' => (new Statistic)(),
                'bookings' => (new Booking)()
            ]
        );
    }
}
