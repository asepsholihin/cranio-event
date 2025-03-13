<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\ParticipantUmrohTrip;
use App\Models\PackageUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\ParticipantCRM;
use App\Models\OrderUmrohTrip;
use App\Models\ParticipantCRMTransactionBackdateHistories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use DB;
use Carbon\Carbon;

class CRMReportSPAController extends Controller
{
    const SPA_PATH = '/crm-report';

    public function __construct()
    {
        $this->middleware('permission:crm-report-view')->only(['chartParticipantGrowth','chartTotalTrip','chartTotalTransaction','chartPackage','chartTopTrip','chartGender','chartAge','chartJob','chartEducation','chartTopCity', 'chartRepetisi']);
    }

    public function chartParticipantGrowth(Request $request)
    {
        $date = Carbon::now();
        $start = $date->startOfMonth()->format('Y-m-d H:i:s');
        $end = $date->endOfMonth()->format('Y-m-d H:i:s');
        if($request->date) {
            $dateXplode = explode('to', $request->date);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
        }

        $query = OrderUmrohTrip::select([
            'umroh_trips.title',
            'departure_at',
            DB::raw("sum(total_pax_trip) AS pax"),
        ])
        ->join('umroh_trips', 'umroh_trips.id', 'order_umroh_trips.umroh_trip_id')
        ->where('umroh_trips.id', '<>', 1);
        $query->whereBetween('umroh_trips.departure_at', [$start, $end]);

        $sales = $query->orderBy('departure_at', 'ASC')->groupBy('umroh_trips.id')->get();

        foreach ($sales as $sale) {
            $sale->title = Carbon::parse($sale->departure_at)->format('d F Y');
        }

        return response()->json($sales);
    }

    public function chartTotalTrip(Request $request)
    {
        $date = Carbon::now();
        $start = $date->startOfMonth()->format('Y-m-d H:i:s');
        $end = $date->endOfMonth()->format('Y-m-d H:i:s');
        $startYear = $date->startOfMonth()->format('Y');
        $endYear = $date->endOfMonth()->format('Y');
        if($request->date) {
            $dateXplode = explode('to', $request->date);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
            $startYear = date('Y', strtotime($dateXplode[0]));
            $endYear = date('Y', strtotime($dateXplode[1]??$dateXplode[0]));
        }

        $crm = ParticipantUmrohTrip::select([
            DB::raw("(SELECT COUNT(*) FROM (SELECT count(id)
                FROM participant_umroh_trips as participant_umroh
                WHERE participant_umroh.created_at BETWEEN '".$start."' and '".$end."'
                GROUP BY participant_umroh.participant_id
                HAVING count(participant_umroh.participant_id) = 1) as trip) as trip1"),
            DB::raw("(SELECT COUNT(*) FROM (SELECT count(id)
                FROM participant_umroh_trips as participant_umroh
                WHERE participant_umroh.created_at BETWEEN '".$start."' and '".$end."'
                GROUP BY participant_umroh.participant_id
                HAVING count(participant_umroh.participant_id) = 2) as trip) as trip2"),
            DB::raw("(SELECT COUNT(*) FROM (SELECT count(id)
                FROM participant_umroh_trips as participant_umroh
                WHERE participant_umroh.created_at BETWEEN '".$start."' and '".$end."'
                GROUP BY participant_umroh.participant_id
                HAVING count(participant_umroh.participant_id) = 3) as trip) as trip3"),
            DB::raw("(SELECT COUNT(*) FROM (SELECT count(id)
                FROM participant_umroh_trips as participant_umroh
                WHERE participant_umroh.created_at BETWEEN '".$start."' and '".$end."'
                GROUP BY participant_umroh.participant_id
                HAVING count(participant_umroh.participant_id) = 4) as trip) as trip4"),
            DB::raw("(SELECT COUNT(*) FROM (SELECT count(id)
                FROM participant_umroh_trips as participant_umroh
                WHERE participant_umroh.created_at BETWEEN '".$start."' and '".$end."'
                GROUP BY participant_umroh.participant_id
                HAVING count(participant_umroh.participant_id) = 5) as trip) as trip5"),
            DB::raw("(SELECT COUNT(*) FROM (SELECT count(id)
                FROM participant_umroh_trips as participant_umroh
                WHERE participant_umroh.created_at BETWEEN '".$start."' and '".$end."'
                GROUP BY participant_umroh.participant_id
                HAVING count(participant_umroh.participant_id) > 5) as trip) as trip6")
        ])
        ->whereBetween('participant_umroh_trips.created_at', [$start, $end])
        ->first();

        if($request->type){
            if($request->type == 'Data Backdate'){
                $crm = ParticipantCRM::join('participant_crm_transaction_backdate_histories', 'participant_crm_transaction_backdate_histories.participant_crm_id', 'participant_crm.id')
                       ->select([
                            DB::raw("SUM(CASE WHEN participant_crm.total_trip = 1 THEN 1 ELSE 0 END) AS trip1"),
                            DB::raw("SUM(CASE WHEN participant_crm.total_trip = 2 THEN 1 ELSE 0 END) AS trip2"),
                            DB::raw("SUM(CASE WHEN participant_crm.total_trip = 3 THEN 1 ELSE 0 END) AS trip3"),
                            DB::raw("SUM(CASE WHEN participant_crm.total_trip = 4 THEN 1 ELSE 0 END) AS trip4"),
                            DB::raw("SUM(CASE WHEN participant_crm.total_trip = 5 THEN 1 ELSE 0 END) AS trip5"),
                            DB::raw("SUM(CASE WHEN participant_crm.total_trip > 5 THEN 1 ELSE 0 END) AS trip6")
                       ])
                       ->whereBetween('participant_crm_transaction_backdate_histories.year', [$startYear, $endYear])
                       ->first();
            }
        }

        $data = array();

        $data['categories'] = array('1 Trip', '2 Trip', '3 Trip', '4 Trip', '5 Trip', 'Lebih dari 5 Trip');
        $data['data'] = array($crm->trip1, $crm->trip2, $crm->trip3, $crm->trip4, $crm->trip5, $crm->trip6);

        return response()->json($data);
    }

    public function chartTotalTransaction(Request $request)
    {
        $date = Carbon::now();
        $start = $date->startOfMonth()->format('Y-m-d H:i:s');
        $end = $date->endOfMonth()->format('Y-m-d H:i:s');
        $startYear = $date->startOfMonth()->format('Y');
        $endYear = $date->endOfMonth()->format('Y');
        if($request->date) {
            $dateXplode = explode('to', $request->date);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
            $startYear = date('Y', strtotime($dateXplode[0]));
            $endYear = date('Y', strtotime($dateXplode[1]??$dateXplode[0]));
        }

        $crm = ParticipantUmrohTrip::select([
            DB::raw("SUM(CASE WHEN price_after_discount <= 100000000 THEN 1 ELSE 0 END) AS trip1"),
            DB::raw("SUM(CASE WHEN price_after_discount > 100000000 AND price_after_discount <= 200000000 THEN 1 ELSE 0 END) AS trip2"),
            DB::raw("SUM(CASE WHEN price_after_discount > 200000000 AND price_after_discount <= 300000000 THEN 1 ELSE 0 END) AS trip3"),
            DB::raw("SUM(CASE WHEN price_after_discount > 300000000 AND price_after_discount <= 400000000 THEN 1 ELSE 0 END) AS trip4"),
            DB::raw("SUM(CASE WHEN price_after_discount > 400000000 AND price_after_discount <= 500000000 THEN 1 ELSE 0 END) AS trip5"),
            DB::raw("SUM(CASE WHEN price_after_discount > 500000000 THEN 1 ELSE 0 END) AS trip6"),
        ])
        ->whereBetween('participant_umroh_trips.created_at', [$start, $end])
        ->first();

        if($request->type){
            if($request->type == 'Data Backdate'){
                $crm = ParticipantCRM::join('participant_crm_transaction_backdate_histories', 'participant_crm_transaction_backdate_histories.participant_crm_id', 'participant_crm.id')
                       ->select([
                            DB::raw("SUM(CASE WHEN participant_crm_transaction_backdate_histories.total_transaction::INTEGER <= 100000000 THEN 1 ELSE 0 END) AS trip1"),
                            DB::raw("SUM(CASE WHEN participant_crm_transaction_backdate_histories.total_transaction::INTEGER > 100000000 AND participant_crm_transaction_backdate_histories.total_transaction::INTEGER <= 200000000 THEN 1 ELSE 0 END) AS trip2"),
                            DB::raw("SUM(CASE WHEN participant_crm_transaction_backdate_histories.total_transaction::INTEGER > 200000000 AND participant_crm_transaction_backdate_histories.total_transaction::INTEGER <= 300000000 THEN 1 ELSE 0 END) AS trip3"),
                            DB::raw("SUM(CASE WHEN participant_crm_transaction_backdate_histories.total_transaction::INTEGER > 300000000 AND participant_crm_transaction_backdate_histories.total_transaction::INTEGER <= 400000000 THEN 1 ELSE 0 END) AS trip4"),
                            DB::raw("SUM(CASE WHEN participant_crm_transaction_backdate_histories.total_transaction::INTEGER > 400000000 AND participant_crm_transaction_backdate_histories.total_transaction::INTEGER <= 500000000 THEN 1 ELSE 0 END) AS trip5"),
                            DB::raw("SUM(CASE WHEN participant_crm_transaction_backdate_histories.total_transaction::INTEGER > 500000000 THEN 1 ELSE 0 END) AS trip6"),
                        ])
                       ->whereBetween('participant_crm_transaction_backdate_histories.year', [$startYear, $endYear])
                       ->first();
            }
        }

        $data = array();

        $data['categories'] = array('Dibawah 100 Juta', '100 Juta - 200 Juta', '200 Juta - 300 Juta', '300 Juta - 400 Juta', '400 Juta - 500 Juta', 'Lebih dari 500 Juta');
        $data['data'] = array($crm->trip1, $crm->trip2, $crm->trip3, $crm->trip4, $crm->trip5, $crm->trip6);

        return response()->json($data);
    }

    public function chartPackage(Request $request)
    {
        $date = Carbon::now();
        $start = $date->startOfMonth()->format('Y-m-d H:i:s');
        $end = $date->endOfMonth()->format('Y-m-d H:i:s');
        $startYear = $date->startOfMonth()->format('Y');
        $endYear = $date->endOfMonth()->format('Y');
        if($request->date) {
            $dateXplode = explode('to', $request->date);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
            $startYear = date('Y', strtotime($dateXplode[0]));
            $endYear = date('Y', strtotime($dateXplode[1]??$dateXplode[0]));
        }

        $crm = ParticipantUmrohTrip::select([
            'package_umroh_trips.name as package_name',
            DB::raw("count(participant_umroh_trips.package_umroh_trip_id) AS pax"),
        ])
        ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
        ->whereBetween('participant_umroh_trips.created_at', [$start, $end])
        ->groupBy('package_umroh_trips.name')->get();

        if($request->type){
            if($request->type == 'Data Backdate'){
                $crm = ParticipantCRM::join('participant_crm_transaction_backdate_histories', 'participant_crm_transaction_backdate_histories.participant_crm_id', 'participant_crm.id')
                       ->select(['participant_crm_transaction_backdate_histories.package_name as latest_trip_package', DB::raw("count(*) AS pax")])
                       ->whereNotNull('participant_crm_transaction_backdate_histories.package_name')
                       ->whereBetween('participant_crm_transaction_backdate_histories.year', [$startYear, $endYear])
                       ->groupBy('participant_crm_transaction_backdate_histories.package_name')->get();
            }
        }

        $data = array();
        $pax = array();
        foreach ($crm as $value) {
            $data['categories'][] = $value->package_name;
            $pax[] = $value->pax;
        }

        $data['data'][]['data'] = $pax;

        return response()->json($data);
    }

    public function chartTopTrip(Request $request)
    {
        $date = Carbon::now();
        $start = $date->startOfMonth()->format('Y-m-d H:i:s');
        $end = $date->endOfMonth()->format('Y-m-d H:i:s');
        $startYear = $date->startOfMonth()->format('Y');
        $endYear = $date->endOfMonth()->format('Y');
        if($request->date) {
            $dateXplode = explode('to', $request->date);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
            $startYear = date('Y', strtotime($dateXplode[0]));
            $endYear = date('Y', strtotime($dateXplode[1]??$dateXplode[0]));
        }

        $crm = ParticipantUmrohTrip::select([
            'umroh_trips.title',
            DB::raw("count(participant_umroh_trips.umroh_trip_id) AS pax"),
        ])
        ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
        ->whereBetween('participant_umroh_trips.created_at', [$start, $end])
        ->groupBy('umroh_trips.title')
        ->orderBy('pax', 'DESC')->limit(10)->get();

        if($request->type){
            if($request->type == 'Data Backdate'){
                $crm = ParticipantCRM::join('participant_crm_transaction_backdate_histories', 'participant_crm_transaction_backdate_histories.participant_crm_id', 'participant_crm.id')
                       ->select(['participant_crm_transaction_backdate_histories.umroh_trip_name as latest_trip_name', DB::raw("count(*) AS pax")])
                       ->whereNotNull('participant_crm_transaction_backdate_histories.umroh_trip_name')
                       ->whereBetween('participant_crm_transaction_backdate_histories.year', [$startYear, $endYear])
                       ->groupBy('participant_crm_transaction_backdate_histories.umroh_trip_name')->orderBy('pax', 'DESC')->limit(10)->get();
            }
        }

        $data = array();
        $pax = array();
        foreach ($crm as $value) {
            $data['categories'][] = $value->title;
            $pax[] = $value->pax;
        }

        $data['data'][]['data'] = $pax;

        return response()->json($data);
    }

    public function chartGender(Request $request)
    {
        $query = ParticipantCRM::select([
            DB::raw("SUM(CASE WHEN gender = 1 THEN 1 ELSE 0 END) AS men"),
            DB::raw("SUM(CASE WHEN gender = 2 THEN 1 ELSE 0 END) AS women"),
        ]);
        if($request->year) {
            $query->where('latest_trip_year', $request->year);
        }
        $crm = $query->first();

        $data = array();

        $data['categories'] = array('Pria', 'Wanita');
        $data['data'] = array($crm->men, $crm->women);

        return response()->json($data);
    }

    public function chartAge(Request $request)
    {
        $query = ParticipantCRM::select([
            DB::raw("SUM(CASE WHEN EXTRACT(year FROM age(current_date,birth_date)) < 18 THEN 1 ELSE 0 END) AS data1"),
            DB::raw("SUM(CASE WHEN EXTRACT(year FROM age(current_date,birth_date)) >= 18 AND EXTRACT(year FROM age(current_date,birth_date)) <= 25 THEN 1 ELSE 0 END) AS data2"),
            DB::raw("SUM(CASE WHEN EXTRACT(year FROM age(current_date,birth_date)) > 25 AND EXTRACT(year FROM age(current_date,birth_date)) <= 35 THEN 1 ELSE 0 END) AS data3"),
            DB::raw("SUM(CASE WHEN EXTRACT(year FROM age(current_date,birth_date)) > 35 AND EXTRACT(year FROM age(current_date,birth_date)) <= 60 THEN 1 ELSE 0 END) AS data4"),
            DB::raw("SUM(CASE WHEN EXTRACT(year FROM age(current_date,birth_date)) > 60 THEN 1 ELSE 0 END) AS data5"),
        ]);
        if($request->year) {
            $query->where('latest_trip_year', $request->year);
        }
        $crm = $query->first();

        $data = array();
        $data['categories'] = array('Dibawah 18', '18 - 25 tahun', '25 - 35 tahun', '35 - 60 tahun', 'Lebih dari 60');
        $data['data'] = array($crm->data1, $crm->data2, $crm->data3, $crm->data4, $crm->data5);

        return response()->json($data);
    }

    public function chartJob(Request $request)
    {
        $query = ParticipantCRM::select([
            'job',
            DB::raw("count(*) AS pax"),
        ])->whereNotNull('job');
        if($request->year) {
            $query->where('latest_trip_year', $request->year);
        }
        $crm = $query->orderBy('pax', 'DESC')->groupBy('job')->get();

        $data = array();
        $pax = array();
        foreach ($crm as $value) {
            $data['categories'][] = $value->job;
            $data['data'][] = $value->pax;
        }

        return response()->json($data);
    }

    public function chartEducation(Request $request)
    {
        $query = ParticipantCRM::select([
            'education',
            DB::raw("count(*) AS pax"),
        ])->whereNotNull('education');
        if($request->year) {
            $query->where('latest_trip_year', $request->year);
        }
        $crm = $query->orderBy('pax', 'DESC')->groupBy('education')->get();

        $data = array();
        $pax = array();
        foreach ($crm as $value) {
            $data['categories'][] = $value->education;
            $data['data'][] = $value->pax;
        }

        return response()->json($data);
    }

    public function chartTopCity(Request $request)
    {
        $crm = ParticipantCRM::select([
            'city',
            DB::raw("count(*) AS pax"),
        ])->whereNotNull('city')->orderBy('pax', 'DESC')->limit(10)->groupBy('city')->get();

        $data = array();
        $pax = array();
        foreach ($crm as $value) {
            $data['categories'][] = $value->city;
            $pax[] = $value->pax;
        }

        $data['data'][]['data'] = $pax;

        return response()->json($data);
    }

    public function chartTopProvince(Request $request)
    {
        $crm = ParticipantCRM::select([
            'province',
            DB::raw("count(*) AS pax"),
        ])->whereNotNull('province')->orderBy('pax', 'DESC')->limit(10)->groupBy('province')->get();

        $data = array();
        $pax = array();
        foreach ($crm as $value) {
            $data['categories'][] = $value->province;
            $pax[] = $value->pax;
        }

        $data['data'][]['data'] = $pax;

        return response()->json($data);
    }

    public function chartOther(Request $request)
    {
        $crm = ParticipantCRM::select([
            DB::raw("SUM(CASE WHEN parent_account >= 1 THEN 1 ELSE 0 END) AS data1"),
            DB::raw("SUM(CASE WHEN no_hp IS NOT NULL OR no_hp != '' THEN 1 ELSE 0 END) AS data2"),
            DB::raw("SUM(CASE WHEN instagram IS NOT NULL OR instagram != '' THEN 1 ELSE 0 END) AS data3"),
            DB::raw("SUM(CASE WHEN linkedin IS NOT NULL OR linkedin != '' THEN 1 ELSE 0 END) AS data4"),
        ])
        ->first();

        $data = array();
        $data['categories'] = array('Pemilik Akun','Memiliki No HP','Memiliki Instagram','Memiliki LinkedIn');
        $data['data'] = array($crm->data1, $crm->data2, $crm->data3, $crm->data4);

        return response()->json($data);
    }

    public function chartHasAccount(Request $request)
    {
        $crm = ParticipantCRM::select([
            DB::raw("SUM(CASE WHEN parent_account >= 1 THEN 1 ELSE 0 END) AS parent_account"),
            DB::raw("SUM(CASE WHEN parent_account < 1 THEN 1 ELSE 0 END) AS not_parent_account"),
        ])
        ->first();

        $data = array();
        $data['categories'] = array('Pemilik Akun','Bukan');
        $data['data'] = array($crm->parent_account, $crm->not_parent_account);

        return response()->json($data);
    }

    public function chartHasPhone(Request $request)
    {
        $crm = ParticipantCRM::select([
            DB::raw("SUM(CASE WHEN no_hp IS NOT NULL OR no_hp != '' THEN 1 ELSE 0 END) AS has_phone"),
            DB::raw("SUM(CASE WHEN no_hp IS NULL THEN 1 ELSE 0 END) AS no_phone"),
        ])
        ->first();

        $data = array();
        $data['categories'] = array('Memiliki No HP','Tidak');
        $data['data'] = array($crm->has_phone, $crm->no_phone);

        return response()->json($data);
    }

    public function chartHasInstagram(Request $request)
    {
        $crm = ParticipantCRM::select([
            DB::raw("SUM(CASE WHEN instagram IS NOT NULL OR instagram != '' THEN 1 ELSE 0 END) AS has_instagram"),
            DB::raw("SUM(CASE WHEN instagram IS NULL THEN 1 ELSE 0 END) AS no_instagram"),
        ])
        ->first();

        $data = array();
        $data['categories'] = array('Memiliki Instagram','Tidak');
        $data['data'] = array($crm->has_instagram, $crm->no_instagram);

        return response()->json($data);
    }

    public function chartHasLinkedin(Request $request)
    {
        $crm = ParticipantCRM::select([
            DB::raw("SUM(CASE WHEN linkedin IS NOT NULL OR linkedin != '' THEN 1 ELSE 0 END) AS has_linkedin"),
            DB::raw("SUM(CASE WHEN linkedin IS NULL THEN 1 ELSE 0 END) AS no_linkedin"),
        ])
        ->first();

        $data = array();
        $data['categories'] = array('Memiliki LinkedIn','Tidak');
        $data['data'] = array($crm->has_linkedin, $crm->no_linkedin);

        return response()->json($data);
    }

    public function chartRepetisi(Request $request){
        $date = Carbon::now();
        $startYear = "2019";
        $endYear =  (string) $date->startOfMonth()->format('Y');

        $replaceEndYear = (int) $endYear;
        $replaceStartYear = (int) $startYear;

        $data = [];

        $totalTrendParticipants = array();
        $totalRepeatParticipants = array();
        $umrohTrips = array();
        for($index=$replaceStartYear; $index <= $replaceEndYear; $index++){
            $data['categories'][] = $index;

            // total trend participant
            $trendParticipant = DB::table('participant_umroh_trips')->select([DB::raw('count(participant_umroh_trips.id) as total')])
            ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
            ->whereNull('umroh_trips.deleted_at')
            ->whereYear('umroh_trips.departure_at', $index)->first();
            $totalParticipant = $trendParticipant->total ?? 0;

            $repeatParticipant = DB::table('participant_umroh_trips')->select('participant_id')
            ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
            ->whereNull('umroh_trips.deleted_at')
            ->whereYear('umroh_trips.departure_at', $index)
            ->groupBy('participant_id')
            ->having(DB::raw('count(participant_id)'), '>', 1)
            ->count();

            $totalRepeatParticipants[] = $repeatParticipant;

            $categories = DB::table('web_categories')->select(['name', 'id'])->get();
            $umrohTrip = [];
            $umrohTrip['years'] = $index;
            foreach($categories as $category){
                $otherTrips = DB::table('participant_umroh_trips')
                    ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
                    ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
                    ->whereNull('umroh_trips.deleted_at')
                    ->whereRaw('EXTRACT(YEAR FROM umroh_trips.departure_at) = ?', [$index])
                    ->where('umroh_trips.category_id', $category->id)->count();
                $category->count = $otherTrips ?? 0;
                $umrohTrip['item'][] = $category;
            }
            $umrohTrips[] = $umrohTrip;

            $totalTrendParticipants[] = $totalParticipant;
        }

        $paxed = [];
        array_push($paxed, array('name' => 'Total Participant', 'data' => $totalTrendParticipants));
        array_push($paxed, array('name' => 'Participant Repeat', 'data' => $totalRepeatParticipants));

        $data['data'][]['data'] = $paxed;
        $data['summary'] = $umrohTrips;
        return response()->json($data);
    }

    public function chartTrendParticipant(Request $request){
        $date = Carbon::now();
        $startYear = "2019";
        $endYear =  (string) $date->startOfMonth()->format('Y');

        $replaceEndYear = (int) $endYear;
        $replaceStartYear = (int) $startYear;

        $data = [];

        $totalTrendParticipants = array();
        $totalRepeatParticipants = array();
        $umrohTrips = array();
        $filterProduct = $request->product;
        $filterPackage = $request->package;
        $filterYear = $request->year;
        $filterSource= $request->source;
        $productName = "";
        if($filterProduct){
           $productName = DB::table('web_categories')->select(['name', 'id'])->where('id', $filterProduct)->first();
        }
        $sourceName = '';
        if($filterSource){
            if($filterSource != 1){
                $sourceName = $filterSource;
            }
        }
        for($index=$replaceStartYear; $index <= $replaceEndYear; $index++){
            $data['categories'][] = $index;

            $backDate = DB::table('participant_crm_transaction_backdate_histories')
                        ->join('participant_crm', 'participant_crm.id', 'participant_crm_transaction_backdate_histories.participant_crm_id')
                        ->where('year', $index);
            if($productName){
                if($productName->name == "Umroh"){
                    $backDate = $backDate->where('umroh_trip_name', 'LIKE', '%'.strtoupper(str_replace('Umroh', 'Umrah', $productName->name)).'%')
                                ->whereNot('umroh_trip_name', 'LIKE', '%'. 'UMRAH PLUS' .'%')->count();
                }else{
                    $backDate = $backDate->where('umroh_trip_name', 'LIKE', '%'.strtoupper(str_replace('Umroh', 'Umrah', $productName->name)).'%')->count();
                }
            }else{
                $backDate = $backDate->count();
            }

            // total trend participant
            $trendParticipant = DB::table('participant_umroh_trips')->select([DB::raw('count(participant_umroh_trips.id) as total')])
            ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
            ->whereNull('umroh_trips.deleted_at');
            if($productName){
                $trendParticipant = $trendParticipant->where('umroh_trips.category_id', $productName->id);
            }
            $trendParticipant = $trendParticipant->whereYear('umroh_trips.departure_at', $index)->first();
            $totalParticipant = ($trendParticipant->total ?? 0) + $backDate;
            if($sourceName){
                if($sourceName == 2){
                    $totalParticipant = ($trendParticipant->total ?? 0);
                }else if($sourceName == 3){
                    $totalParticipant = $backDate;
                }
            }

            $repeatParticipant = DB::table('participant_umroh_trips')->select('participant_id')
            ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id');
            if($productName){
                $repeatParticipant->where('umroh_trips.category_id', $productName->id);
            }
            $repeatParticipant = $repeatParticipant->whereNull('umroh_trips.deleted_at')
            ->whereYear('umroh_trips.departure_at', $index)
            ->groupBy('participant_id')
            ->having(DB::raw('count(participant_id)'), '>', 1)
            ->count();

            $totalRepeatParticipants[] = $repeatParticipant;

            $categories = DB::table('web_categories')->select(['name', 'id'])->get();
            $umrohTrip = [];
            $umrohTrip['years'] = $index;
            foreach($categories as $category){
                $otherTrips = DB::table('participant_umroh_trips')
                    ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
                    ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
                    ->whereNull('umroh_trips.deleted_at')
                    ->whereRaw('EXTRACT(YEAR FROM umroh_trips.departure_at) = ?', [$index])
                    ->where('umroh_trips.category_id', $category->id)->count();

                $countRepeeat = DB::table('participant_umroh_trips')->select('participant_id')
                    ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
                    ->where('umroh_trips.category_id', $category->id)
                    ->whereNull('umroh_trips.deleted_at')
                    ->whereYear('umroh_trips.departure_at', $index)
                    ->groupBy('participant_id')
                    ->having(DB::raw('count(participant_id)'), '>', 1)
                    ->count();

                $category->count = $otherTrips;
                $category->count_repeat = $countRepeeat;
                $umrohTrip['item'][] = $category;
            }
            $umrohTrips[] = $umrohTrip;

            $totalTrendParticipants[] = $totalParticipant;
        }

        $paxed = [];
        array_push($paxed, array('name' => 'Total Participant', 'data' => $totalTrendParticipants));
        array_push($paxed, array('name' => 'Participant Repeat', 'data' => $totalRepeatParticipants));

        $data['data'][]['data'] = $paxed;
        $data['summary'] = $umrohTrips;
        return response()->json($data);
    }

    public function chartTrendPackage(Request $request){
        $date = Carbon::now();
        $startYear = "2019";
        $endYear =  (string) $date->startOfMonth()->format('Y');

        $replaceEndYear = (int) $endYear;
        $replaceStartYear = (int) $startYear;

        $data = [];

        $umrohTrips = array();
        $package1 = [];
        $package2 = [];
        $package3 = [];
        $package4 = [];
        $filterSource = $request->source;
        $sourceName = '';
        if($filterSource){
            if($filterSource != 1){
                $sourceName = $filterSource;
            }
        }
        for($index=$replaceStartYear; $index <= $replaceEndYear; $index++){
            $data['categories'][] = $index;

            $participantTrips = DB::table('participant_umroh_trips')->select('package_umroh_trips.name as package_name')
            ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
            ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
            ->whereNull('umroh_trips.deleted_at')
            ->whereYear('umroh_trips.departure_at', $index)->get();

            $array = [];
            foreach($participantTrips as $participant){
                $array[] = $participant->package_name;
            }

            $nilai1 = 0;
            $nilai2 = 0;
            $nilai3 = 0;
            $nilai4 = 0;
            $nilai5 = 0;
            $nilai6 = 0;
            // Summary
            $umrohTrip = [];
            $nilaiRuby = 0;
            $nilaiE = 0;
            $nilaiS = 0;

            $nilaiOnyx = 0;
            $nilaiL = 0;
            $nilaiK = 0;
            $umrohTrip['years'] = $index;

            for($yindex = 0; $yindex < count($array); $yindex++){
                // package 1
                if(trim($array[$yindex]) == 'Sapphire Plus'){
                    $nilai1 += 1;
                }
                // package 2
                if(trim($array[$yindex]) == 'Sapphire'){
                    $nilai2 += 1;
                }
                // package 3
                if(in_array(trim($array[$yindex]), ['Ruby','Emerald','Silver'])){
                    $nilai3 += 1;
                }
                if(trim($array[$yindex]) == 'Ruby'){
                    $nilaiRuby += 1;
                }
                if(trim($array[$yindex]) == 'Emerald'){
                    $nilaiE += 1;
                }
                if(trim($array[$yindex]) == 'Silver'){
                    $nilaiS += 1;
                }
                // package 4
                if(in_array(trim($array[$yindex]), ['Onyx','Lebih Hemat','Konsorsium'])){
                    $nilai4 += 1;
                }
                if(trim($array[$yindex]) == 'Onyx'){
                    $nilaiOnyx += 1;
                }
                if(trim($array[$yindex]) == 'Lebih Hemat'){
                    $nilaiL += 1;
                }
                if(trim($array[$yindex]) == 'Konsorsium'){
                    $nilaiK += 1;
                }
                // package 5
                if(str_contains(trim($array[$yindex]), 'Yaqin')){
                    $nilai5 += 1;
                }
                // package 6
                if(!str_contains(trim($array[$yindex]), 'Yaqin') && !in_array(trim($array[$yindex]), ['Sapphire Plus','Sapphire','Onyx','Lebih Hemat','Konsorsium','Ruby','Emerald','Silver'])){
                    $nilai6 += 1;
                }
            }

            $saphhire_plus_backdate = DB::table('participant_crm_transaction_backdate_histories')
                        ->join('participant_crm', 'participant_crm.id', 'participant_crm_transaction_backdate_histories.participant_crm_id')
                        ->where('package_name', 'Sapphire Plus')
                        ->where('year', $index)->count();

            $saphhire_backdate = DB::table('participant_crm_transaction_backdate_histories')
                        ->join('participant_crm', 'participant_crm.id', 'participant_crm_transaction_backdate_histories.participant_crm_id')
                        ->where('package_name', 'Sapphire')
                        ->where('year', $index)->count();

            $ruby_backdate = DB::table('participant_crm_transaction_backdate_histories')
                        ->join('participant_crm', 'participant_crm.id', 'participant_crm_transaction_backdate_histories.participant_crm_id')
                        ->where('package_name', 'Ruby')
                        ->where('year', $index)->count();

            $emerald_backdate = DB::table('participant_crm_transaction_backdate_histories')
                        ->join('participant_crm', 'participant_crm.id', 'participant_crm_transaction_backdate_histories.participant_crm_id')
                        ->where('package_name', 'Emerald')
                        ->where('year', $index)->count();

            $silver_backdate = DB::table('participant_crm_transaction_backdate_histories')
                        ->join('participant_crm', 'participant_crm.id', 'participant_crm_transaction_backdate_histories.participant_crm_id')
                        ->where('package_name', 'Silver')
                        ->where('year', $index)->count();

            $onyx_backdate = DB::table('participant_crm_transaction_backdate_histories')
                        ->join('participant_crm', 'participant_crm.id', 'participant_crm_transaction_backdate_histories.participant_crm_id')
                        ->where('package_name', 'Onyx')
                        ->where('year', $index)->count();

            $hemat_backdate = DB::table('participant_crm_transaction_backdate_histories')
                        ->join('participant_crm', 'participant_crm.id', 'participant_crm_transaction_backdate_histories.participant_crm_id')
                        ->where('package_name', 'Lebih Hemat')
                        ->where('year', $index)->count();

            $konsorsium_backdate = DB::table('participant_crm_transaction_backdate_histories')
                        ->join('participant_crm', 'participant_crm.id', 'participant_crm_transaction_backdate_histories.participant_crm_id')
                        ->where('package_name', 'Konsorsium')
                        ->where('year', $index)->count();

            $yaqin_backdate = DB::table('participant_crm_transaction_backdate_histories')
                        ->join('participant_crm', 'participant_crm.id', 'participant_crm_transaction_backdate_histories.participant_crm_id')
                        ->where('package_name', 'Yaqin Umroh')
                        ->where('year', $index)->count();

            $other_backdate = DB::table('participant_crm_transaction_backdate_histories')
                        ->join('participant_crm', 'participant_crm.id', 'participant_crm_transaction_backdate_histories.participant_crm_id')
                        ->whereNotIn('package_name', ['Yaqin Umroh', "Sapphire Plus", "Sapphire", "Ruby", "Emerald", "Silver", "Onyx", "Lebih Hemat",
                        "Konsorsium"])
                        ->where('year', $index)->count();

            if($sourceName){
                if($sourceName == 3){
                    $nilai1 = 0;
                    $nilai2 = 0;
                    $nilai3 = 0;
                    $nilai4 = 0;
                    $nilai5 = 0;
                    $nilai6 = 0;
                    $nilaiE = 0;
                    $nilaiK = 0;
                    $nilaiL = 0;
                    $nilaiOnyx = 0;
                    $nilaiRuby = 0;
                    $nilaiS = 0;
                    $saphhire_plus_backdate = $saphhire_plus_backdate;
                    $saphhire_backdate = $saphhire_backdate;
                    $ruby_backdate = $ruby_backdate;
                    $emerald_backdate = $emerald_backdate;
                    $silver_backdate = $silver_backdate;
                    $onyx_backdate = $onyx_backdate;
                    $hemat_backdate = $hemat_backdate;
                    $konsorsium_backdate = $konsorsium_backdate;
                    $yaqin_backdate = $yaqin_backdate;
                    $other_backdate = $other_backdate;
                }else if($sourceName == 2){
                    $saphhire_plus_backdate = 0;
                    $saphhire_backdate = 0;
                    $ruby_backdate = 0;
                    $emerald_backdate = 0;
                    $silver_backdate = 0;
                    $onyx_backdate = 0;
                    $hemat_backdate = 0;
                    $konsorsium_backdate = 0;
                    $yaqin_backdate = 0;
                    $other_backdate = 0;
                }else{
                    $saphhire_plus_backdate = $saphhire_plus_backdate;
                    $saphhire_backdate = $saphhire_backdate;
                    $ruby_backdate = $ruby_backdate;
                    $emerald_backdate = $emerald_backdate;
                    $silver_backdate = $silver_backdate;
                    $onyx_backdate = $onyx_backdate;
                    $hemat_backdate = $hemat_backdate;
                    $konsorsium_backdate = $konsorsium_backdate;
                    $yaqin_backdate = $yaqin_backdate;
                    $other_backdate = $other_backdate;
                }
            }else{
                $saphhire_plus_backdate = $saphhire_plus_backdate;
                $saphhire_backdate = $saphhire_backdate;
                $ruby_backdate = $ruby_backdate;
                $emerald_backdate = $emerald_backdate;
                $silver_backdate = $silver_backdate;
                $onyx_backdate = $onyx_backdate;
                $hemat_backdate = $hemat_backdate;
                $konsorsium_backdate = $konsorsium_backdate;
                $yaqin_backdate = $yaqin_backdate;
                $other_backdate = $other_backdate;
            }
            $package1[] = $nilai1 + $saphhire_plus_backdate;
            $package2[] = $nilai2 + $saphhire_backdate;
            $package3[] = $nilai3 + $ruby_backdate + $emerald_backdate + $silver_backdate;
            $package4[] = $nilai4 + $onyx_backdate + $hemat_backdate + $konsorsium_backdate;
            $package5[] = $nilai5 + $yaqin_backdate;
            $package6[] = $nilai6 + $other_backdate;

            $umrohTrip['item'] = [
                'part_1'=> [
                    [
                        'name' => 'Sapphire Plus',
                        'no' => 1,
                        'count' => $nilai1 + $saphhire_plus_backdate
                    ]
                ],
                'part_2' => [
                    [
                        'name' => 'Sapphire',
                        'no' => 2,
                        'count' => $nilai2 +  $saphhire_backdate
                    ]
                ],
                'part_3' => [
                    [
                        'name' => 'Ruby',
                        'no' => 3,
                        'count' => $nilaiRuby + $ruby_backdate
                    ],[
                        'name' => 'Emerald',
                        'no' => 4,
                        'count' => $nilaiE + $emerald_backdate
                    ],[
                        'name' => 'Silver',
                        'no' => 5,
                        'count' => $nilaiS + $silver_backdate
                    ]
                ],
                'part_4' => [
                    [
                        'name' => 'Onyx',
                        'no' => 6,
                        'count' => $nilaiOnyx + $onyx_backdate
                    ],[
                        'name' => 'Lebih Hemat',
                        'no' => 7,
                        'count' => $nilaiL + $hemat_backdate
                    ],[
                        'name' => 'Konsorsium',
                        'no' => 8,
                        'count' => $nilaiK + $konsorsium_backdate
                    ]
                ],
                'part_5'=> [
                    [
                        'name' => 'Yaqin Umroh',
                        'no' => 9,
                        'count' => $nilai5 + $yaqin_backdate
                    ]
                ],
                'part_6'=> [
                    [
                        'name' => 'Paket Lainnya',
                        'no' => 10,
                        'count' => $nilai6 + $other_backdate
                    ]
                ],
            ];

            $umrohTrips[] = $umrohTrip;
        }

        $paxed = array();
        array_push($paxed, array('name' => 'Sapphire Plus', 'data' => $package1));
        array_push($paxed, array('name' => 'Sapphire', 'data' => $package2));
        array_push($paxed, array('name' => 'Ruby, Emerald, Silver', 'data' => $package3));
        array_push($paxed, array('name' => 'Onyx, Lebih Hemat, Konsorsium', 'data' => $package4));
        array_push($paxed, array('name' => 'Yaqin Umroh', 'data' => $package5));
        array_push($paxed, array('name' => 'Paket Lainnya', 'data' => $package6));

        $data['data'][]['data'] = $paxed;
        $data['summary'] = $umrohTrips;
        return response()->json($data);
    }

    public function chartTrendGrowth(Request $request){
        $date = Carbon::now();
        $startYear = "2019";
        $endYear =  (string) $date->startOfMonth()->format('Y');

        $replaceEndYear = (int) $endYear;
        $replaceStartYear = (int) $startYear;

        $data = [];

        $totalTrendParticipants = array();
        $totalRepeatParticipants = array();
        $umrohTrips = array();
        for($index=$replaceStartYear; $index <= $replaceEndYear; $index++){
            $data['categories'][] = $index;

            // total trend participant
            $trendParticipant = DB::table('participant_umroh_trips')->select([DB::raw('count(participant_umroh_trips.id) as total')])
            ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
            ->whereNull('umroh_trips.deleted_at')
            ->whereYear('umroh_trips.departure_at', $index)->first();
            $totalParticipant = $trendParticipant->total ?? 0;

            $backDate = DB::table('participant_crm_transaction_backdate_histories')->select('participant_crm_transaction_backdate_histories.participant_crm_id')
                        ->join('participant_crm', 'participant_crm.id', 'participant_crm_transaction_backdate_histories.participant_crm_id')
                        ->where('participant_crm_transaction_backdate_histories.year', $index)
                        ->count();

            $totalTrendParticipants[] = $totalParticipant + $backDate;

            $categories = DB::table('web_categories')->select(['name', 'id'])->get();
            $umrohTrip = [];
            $umrohTrip['years'] = $index;
            foreach($categories as $category){
                $otherTrips = DB::table('participant_umroh_trips')
                    ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
                    ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
                    ->whereNull('umroh_trips.deleted_at')
                    ->whereRaw('EXTRACT(YEAR FROM umroh_trips.departure_at) = ?', [$index])
                    ->where('umroh_trips.category_id', $category->id)->count();

                $countCategory = DB::table('participant_crm_transaction_backdate_histories')
                    ->join('participant_crm', 'participant_crm.id', 'participant_crm_transaction_backdate_histories.participant_crm_id')
                    ->where('year', $index);

                if($category->name == "Umroh"){
                    $countCategory = $countCategory->where('umroh_trip_name', 'LIKE', '%'.strtoupper(str_replace('Umroh', 'Umrah', $category->name)).'%')
                                ->whereNot('umroh_trip_name', 'LIKE', '%'. 'UMRAH PLUS' .'%')->count();
                }else{
                    $countCategory = $countCategory->where('umroh_trip_name', 'LIKE', '%'.strtoupper(str_replace('Umroh', 'Umrah', $category->name)).'%')->count();
                }

                $category->count = ($otherTrips ?? 0) + $countCategory;
                $umrohTrip['item'][] = $category;
            }
            $umrohTrips[] = $umrohTrip;
        }

        $paxed = [];
        array_push($paxed, array('name' => 'Total Participant', 'data' => $totalTrendParticipants));

        $data['data'][]['data'] = $paxed;
        $data['summary'] = $umrohTrips;
        return response()->json($data);
    }

    public function chartTrendProduct(Request $request){
        $date = Carbon::now();
        $startYear = "2019";
        $endYear =  (string) $date->startOfMonth()->format('Y');

        $replaceEndYear = (int) $endYear;
        $replaceStartYear = (int) $startYear;

        $data = [];
        $umrohTrips = [];
        for($index=$replaceStartYear; $index <= $replaceEndYear; $index++){
            $data['categories'][] = $index;

            $categories = DB::table('web_categories')->select(['name', 'id'])->get();
            $umrohTrip = [];
            $umrohTrip['years'] = $index;
            foreach($categories as $category){
                $otherTrips = DB::table('participant_umroh_trips')
                    ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
                    ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
                    ->whereNull('umroh_trips.deleted_at')
                    ->whereRaw('EXTRACT(YEAR FROM umroh_trips.departure_at) = ?', [$index])
                    ->where('umroh_trips.category_id', $category->id)->count();

                $countCategory = DB::table('participant_crm_transaction_backdate_histories')
                    ->join('participant_crm', 'participant_crm.id', 'participant_crm_transaction_backdate_histories.participant_crm_id')
                    ->where('year', $index);
                if($category->name == "Umroh"){
                    $countCategory = $countCategory->where('umroh_trip_name', 'LIKE', '%'.strtoupper(str_replace('Umroh', 'Umrah', $category->name)).'%')
                                ->whereNot('umroh_trip_name', 'LIKE', '%'. 'UMRAH PLUS' .'%')->count();
                }else{
                    $countCategory = $countCategory->where('umroh_trip_name', 'LIKE', '%'.strtoupper(str_replace('Umroh', 'Umrah', $category->name)).'%')->count();
                }
                $category->count = ($otherTrips ?? 0) + $countCategory;
                $umrohTrip['item'][] = $category;
            }
            $umrohTrips[] = $umrohTrip;
        }


        $paxed = [];
        $categories = DB::table('web_categories')->select(['name', 'id'])->get();
        foreach($categories as $key => $category){
            $totalPerCategory = [];
            for($index=$replaceStartYear; $index <= $replaceEndYear; $index++){
                $participantTrips = DB::table('participant_umroh_trips')->select(['umroh_trips.category_id'])
                ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
                ->whereNull('umroh_trips.deleted_at')
                ->where('umroh_trips.category_id', $category->id)
                ->whereYear('umroh_trips.departure_at', $index)->count();

                $countCategory = DB::table('participant_crm_transaction_backdate_histories')
                    ->join('participant_crm', 'participant_crm.id', 'participant_crm_transaction_backdate_histories.participant_crm_id')
                    ->where('year', $index);
                if($category->name == "Umroh"){
                    $countCategory = $countCategory->where('umroh_trip_name', 'LIKE', '%'.strtoupper(str_replace('Umroh', 'Umrah', $category->name)).'%')
                                ->whereNot('umroh_trip_name', 'LIKE', '%'. 'UMRAH PLUS' .'%')->count();
                }else{
                    $countCategory = $countCategory->where('umroh_trip_name', 'LIKE', '%'.strtoupper(str_replace('Umroh', 'Umrah', $category->name)).'%')->count();
                }

                $totalPerCategory[] = $participantTrips + $countCategory;
            }
            array_push($paxed, array('name' => $category->name, 'data' => $totalPerCategory));
        }

        $data['data'][]['data'] = $paxed;
        $data['summary'] = $umrohTrips;
        return response()->json($data);
    }
}
