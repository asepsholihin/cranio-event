<?php

namespace App\Http\Controllers;

use App\Models\TempBooking;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingExport;
use App\File\PDF\ReceiptGeneralPDF;

class BookingTemporarySPAController extends Controller
{
    const SPA_PATH = '/booking-temporary';

    public function __construct()
    {
        $this->middleware('permission:temp-booking-view')->only(['index','show']);
        $this->middleware('permission:temp-booking-add-or-edit')->only(['store']);
        $this->middleware('permission:temp-booking-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orderBy = request()->query('sortBy', 'uuid');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage', 10);
        return response()->json(
            TempBooking::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);

        $uid = auth()->user()->id;
        $request->merge([
            'created_by' => $uid,
            'updated_by' => $uid
        ]);

        TempBooking::updateOrCreate(['id' => $request->get('id')], $request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TempBooking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(TempBooking $booking)
    {
        return response()->json($booking->toArray());
    }

    public function destroy($uuid)
    {
        TempBooking::where('uuid', $uuid)->delete();
    }

    public function exportData(Request $request)
    {
        $storageKey = "Export-Booking-Hotel-Event-" . hrtime(true) . ".xlsx";
        return Excel::download(new BookingExport($request->all()), $storageKey);
    }
}
