<?php

namespace App\Http\Controllers;

use App\Models\BookingHotelEvent;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingHotelEventExport;
use App\File\PDF\ReceiptGeneralPDF;

class BookingHotelEventSPAController extends Controller
{
    const SPA_PATH = '/booking-hotel-event';

    public function __construct()
    {
        $this->middleware('permission:booking-hotel-event-view')->only(['index','show']);
        $this->middleware('permission:booking-hotel-event-add-or-edit')->only(['store']);
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
            BookingHotelEvent::tableSearch()
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

        BookingHotelEvent::updateOrCreate(['id' => $request->get('id')], $request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BookingHotelEvent  $booking_hotel_event
     * @return \Illuminate\Http\Response
     */
    public function show(BookingHotelEvent $booking_hotel_event)
    {
        return response()->json($booking_hotel_event->toArray());
    }

    public function destroy(BookingHotelEvent $booking_hotel_event)
    {
        $booking_hotel_event->deleted_by = auth()->user()->id;
        $booking_hotel_event->save();
        $booking_hotel_event->delete();
    }

    public function exportData(Request $request)
    {
        $storageKey = "Export-Booking-Hotel-Event-" . hrtime(true) . ".xlsx";
        return Excel::download(new BookingHotelEventExport($request->all()), $storageKey);
    }

    public function downloadReceiptPDF(Request $request)
    {   
        $bookingHotelEvent = BookingHotelEvent::find($request->id);
        return (new ReceiptGeneralPDF($bookingHotelEvent))->stream();
    }

    public function hotelSearch(Request $request)
    {
        $search = $request->get('q');

        $search = '%' . $search . '%';
        $result = BookingHotelEvent::select(['hotel_name'])->where('hotel_name', 'ilike', $search)->groupBy('hotel_name')->get();
        return response()->json($result);
    }

    public function roomTypeSearch(Request $request)
    {
        $search = $request->get('q');

        $search = '%' . $search . '%';
        $result = BookingHotelEvent::select(['room_type'])->where('room_type', 'ilike', $search)->groupBy('room_type')->get();
        return response()->json($result);
    }

    public function reCreateInvoice(Request $request)
    {
        $formFill = [
            'id' => request()->id
        ];
        BookingHotelEvent::reCreateInvoice($formFill);
    }
}
