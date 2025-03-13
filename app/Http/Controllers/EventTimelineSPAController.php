<?php

namespace App\Http\Controllers;

use App\Exports\ParticipantUmrohManifestExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\EventTimeline;
use App\Models\UmrohTrip;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use Carbon\Carbon;
use DB;

class EventTimelineSPAController extends Controller
{
    const SPA_PATH = '/event-timeline';

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
        $counted = EventTimeline::tableSearch()->count();
        $perPage = request()->query('perPage', $counted);
        return response()->json(
            EventTimeline::tableSearch()->with(['packages','ticketing','event','manasikOnlines'])
            ->paginate($perPage)
            ->withQueryString()
            ->withPath(self::SPA_PATH)
        );
    }

    public function updateData(Request $request)
    {
        $eventTimeline = EventTimeline::find($request->id);
        if($request->certificate_due_date == null) {
            $request->request->remove('certificate_due_date');
        }
        $eventTimeline->update($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EventTimeline  $eventTimeline
     * @return \Illuminate\Http\Response
     */
    public function show(EventTimeline $eventTimeline)
    {
        return response()->json($eventTimeline->toArray());
    }

    public function destroy(EventTimeline $eventTimeline)
    {
        $eventTimeline->delete();
    }

    public function refineEventTimelineSeeder(Request $request)
    {
        $umrohTrips = UmrohTrip::where('departure_at', '>=', Carbon::now()->subMonthsNoOverflow(4))->whereNotIn('id', [1,2,3,4])->get();
        foreach ($umrohTrips as $umrohTrip) {
            EventTimeline::createOrUpdateEventTimeline($umrohTrip->id);
        }
    }
}
