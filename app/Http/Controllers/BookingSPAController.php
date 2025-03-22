<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingExport;
use App\File\PDF\ReceiptGeneralPDF;

class BookingSPAController extends Controller
{
    const SPA_PATH = '/booking';

    public function __construct()
    {
        $this->middleware('permission:booking-view')->only(['index','show']);
        $this->middleware('permission:booking-add-or-edit')->only(['store']);
        $this->middleware('permission:booking-delete')->only(['destroy']);
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
            Booking::tableSearch()
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

        Booking::updateOrCreate(['id' => $request->get('id')], $request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(Booking $booking)
    {
        return response()->json($booking->toArray());
    }

    public function destroy(Booking $booking)
    {
        $booking->deleted_by = auth()->user()->id;
        $booking->save();
        $booking->delete();
    }

    public function exportData(Request $request)
    {
        $storageKey = "Export-Booking-Hotel-Event-" . hrtime(true) . ".xlsx";
        return Excel::download(new BookingExport($request->all()), $storageKey);
    }
}
