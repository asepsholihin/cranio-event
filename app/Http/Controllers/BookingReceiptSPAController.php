<?php

namespace App\Http\Controllers;

use App\Models\BookingReceipt;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingReceiptExport;
use App\File\PDF\ReceiptGeneralPDF;
use DB;

class BookingReceiptSPAController extends Controller
{
    const SPA_PATH = '/booking-receipt';

    public function __construct()
    {
        $this->middleware('permission:booking-receipt-view')->only(['index','show']);
        $this->middleware('permission:booking-receipt-add-or-edit')->only(['store','action']);
        $this->middleware('permission:booking-receipt-delete')->only(['destroy']);
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
            BookingReceipt::tableSearch()
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

        BookingReceipt::updateOrCreate(['id' => $request->get('id')], $request->all());
    }

    public function action(Request $request)
    {
        if ($request->has('update_status')) {
            DB::transaction(function() use($request) {
                $bookingReceipt = BookingReceipt::find($request->get('id'));
                $bookingReceipt->status = $request->get('status');
                $bookingReceipt->save();
                $bookingReceipt->setOrderNumber();
                
                $booking = Booking::find($bookingReceipt->booking_id);
                $booking->total_unpaid = $booking->total_unpaid - $bookingReceipt->payment_amount;
                $booking->save();
            });
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BookingReceipt  $booking_receipt
     * @return \Illuminate\Http\Response
     */
    public function show(BookingReceipt $booking_receipt)
    {
        return response()->json($booking_receipt->toArray());
    }

    public function destroy(BookingReceipt $booking_receipt)
    {
        $booking_receipt->deleted_by = auth()->user()->id;
        $booking_receipt->save();
        $booking_receipt->delete();
    }

}
