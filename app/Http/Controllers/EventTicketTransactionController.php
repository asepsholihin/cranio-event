<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\EventTicketTransaction;
use App\Models\EventOpenRegistration;
use App\Models\AttendanceOpenRegistration;
use App\Models\ParticipantUmrohTrip;
use Illuminate\Http\Request;
use App\File\Image\BarcodeText;
use App\Jobs\SendWhatsappTicketEvent;
use DB;

class EventTicketTransactionController extends Controller
{
    const SPA_PATH = '/event-ticket-transaction';
    public function __construct()
    {
        $this->middleware('permission:event-ticket-transaction-view')->only(['index','show']);
        $this->middleware('permission:event-ticket-transaction-add-or-edit')->only(['destroy']);
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
            EventTicketTransaction::tableSearch()
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
            'event_id' => 'required',
            'name' => 'required',
            'no_hp' => 'required|numeric',
            'email' => 'required|email'
        ]);

        EventTicketTransaction::updateOrCreate(['id' => $request->get('id')], $request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EventTicketTransaction $event
     * @return \Illuminate\Http\Response
     */
    public function show(EventTicketTransaction $event_ticket_transaction)
    {
        $event = EventOpenRegistration::find($event_ticket_transaction->event_id);
        $logs = DB::table('log_event_ticket_transactions')->where('transaction_id', $event_ticket_transaction->id)->get();
        
        $event_ticket_transaction->event_name = $event->name;
        $event_ticket_transaction->event_date = $event->event_date;
        $event_ticket_transaction->logs = $logs;
        $transaction = $event_ticket_transaction->toArray();
        return response()->json($transaction);
    }

    public function destroy(EventTicketTransaction $event_ticket_transaction)
    {
        $event_ticket_transaction->delete();
    }

    public function resendTicket(Request $request)
    {
        $participant = AttendanceOpenRegistration::where('no_hp', $request->no_hp)->where('event_open_registration_id', $request->event_id)->first();
        $event = EventOpenRegistration::find($request->event_id);

        SendWhatsappTicketEvent::dispatch($event, $request->pax, $participant->no_hp, $participant->name, $participant->barcode_thumbnail);
    }

}
