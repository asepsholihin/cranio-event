<?php

namespace App\Http\Controllers;

use App\Exports\ParticipantUmrohManifestExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\EventManasikOnline;
use App\Models\UmrohTrip;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use Carbon\Carbon;
use Auth;
use DB;

class EventManasikOnlineSPAController extends Controller
{
    const SPA_PATH = '/event-manasik-online';

    public function __construct()
    {
        $this->middleware('permission:event-timeline-view')->only(['index','show']);
        $this->middleware('permission:event-timeline-add-or-edit')->only(['updateData']);
        $this->middleware('permission:event-timeline-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orderBy = request()->query('sortBy', 'umroh_trip_id');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage', 10);
        return response()->json(
            EventManasikOnline::tableSearch()->with(['packages','ticketing','event'])
            ->paginate($perPage)
            ->withQueryString()
            ->withPath(self::SPA_PATH)
        );
    }

    public function store(Request $request)
    {
        $request->merge([
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        if($request->get('id')) {
            $event = EventManasikOnline::find($request->get('id'));
            $event->update($request->all());
        } else {
            DB::transaction(function() {
                $event = EventManasikOnline::create(request()->all());
                EventManasikOnline::generateConfirmationLink($event->id);
            });
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EventManasikOnline  $eventManasikOnline
     * @return \Illuminate\Http\Response
     */
    public function show(EventManasikOnline $eventManasikOnline)
    {
        return response()->json($eventManasikOnline->toArray());
    }

    public function destroy(EventManasikOnline $eventManasikOnline)
    {
        $eventManasikOnline->delete();
    }
}
