<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingExport;
use App\File\PDF\ReceiptGeneralPDF;
use Carbon\Carbon;

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
        $booking = Booking::updateOrCreate(['id' => $request->get('id')], $request->all());
    }

    public function action(Request $request)
    {
        $request->validate(['id' => 'required']);
        $booking = Booking::find($request->get('id'));

        if ($request->hasFile('file_evidence')) {
            $profilePhotoPath = $request->file('file_evidence')->store(Booking::DIR_EVIDENCE);
            $request->merge(['room_key_evidence' => $profilePhotoPath]);
        }

        if($request->set_room) {
            $request->merge([
                'received_at' => Carbon::now(),
                'given_by' => auth()->user()->id
            ]);
        }

        if($request->update_booking) {
            if($request->total_pax < $booking->pax_assign) {
                throw new ErrorMessageException("Total Pax tidak boleh kurang dari peserta yang terdaftar");
            } 
        }

        $booking->update($request->except('file_evidence'));
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
