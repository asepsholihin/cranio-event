<?php

namespace App\Http\Controllers\CrewApp;

use App\Http\Controllers\Controller;
use App\Models\SummaryAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class AttendeeController extends Controller
{
    public function __construct()
    {
        App::setLocale('id');
    }

    public function summaryAttendee(Request $request)
    {
        $request->validate([
            'umroh_trip_id' => 'required',
            'category_id' => 'required',
            'attendance_name' => 'required',
        ]);
        
        SummaryAttendance::updateOrCreate(['umroh_trip_id' => $request->umroh_trip_id, 'category_id' => $request->category_id], $request->except('photo'));
        return response()->json(['success' => 'ok']);
    }
}
