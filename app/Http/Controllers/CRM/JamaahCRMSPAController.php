<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Imports\ParticipantCRMImport;
use App\Models\ParticipantUmrohTrip;
use App\Models\PackageUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\ParticipantCRM;
use App\Models\OrderUmrohTrip;
use App\Models\DiscountOrderUmrohTrip;
use App\Models\OrderItemUmrohTrip;
use App\Models\ParticipantCRMTransactionBackdateHistories;
use App\Models\MasterAddress;
use Illuminate\Http\Request;
use App\Http\Requests\StoreParticipantRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Image;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\File\Image\CertificateImage;
use App\File\PDF\CertificateParticipant;
use App\Exports\ParticipantCRMExport;
use App\Models\ParticipantFile;
use App\Jobs\GenerateDocumentCRMPDF;
use App\Jobs\RefineParticipantCRM;
use DB;
use Carbon\Carbon;

class ParticipantCRMSPAController extends Controller
{
    const SPA_PATH = '/participant-crm';

    public function __construct()
    {
        $this->middleware('permission:participant-crm-view')->only(['index', 'show', 'queryParticipant', 'barcode']);
        $this->middleware('permission:participant-crm-edit')->only(['store']);
        $this->middleware('permission:participant-crm-import')->only(['import']);
        $this->middleware('permission:participant-crm-download')->only(['exportParticipantCRM']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = request()->query('perPage', 10);
        $this->checkYears();
        return response()->json(
            ParticipantCRM::tableSearch()
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreParticipantRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $phoneNumber = $request->no_hp;
        if (Str::startsWith($phoneNumber, '0')) {
            $phoneNumber = Str::replaceFirst('0', $request->country_code, $phoneNumber);
        }

        $request->merge([
            'no_hp' => $phoneNumber,
            'home_province' => $request->province,
            'home_city' => $request->city,
            'home_kecamatan' => $request->home_kecamatan,
            'home_kelurahan' => $request->home_kelurahan,
            'home_postalcode' => $request->home_postalcode,
        ]);

        $participantCRM = ParticipantCRM::updateOrCreate(['id' => $request->get('id')], $request->except(['photo']));
        Participant::updateOrCreate(['id' => $participantCRM->participant_id], $request->except(['photo']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Participant  $participant
     * @return \Illuminate\Http\Response
     */
    public function show(Participant $participant)
    {
        return response()->json($participant->toArray());
    }

    public function participantCRMdetail($participantId)
    {
        $participantCrm = ParticipantCRM::
        select(['participant.*', 'participant_crm.*', 'participant.education as j_education'])
        ->join('participants', 'participant_crm.participant_id', 'participant.id')->where('participant_crm.participant_id', $participantId)->first();

        return response()->json($participantCrm);
    }

    public function destroy(ParticipantCRM $participant)
    {
        $participant->delete();
    }

    public function tripSearch(Request $request)
    {
        $request->validate(['q' => 'nullable']);
        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = ParticipantCRM::select(['latest_trip_name'])
        ->where('latest_trip_name', 'like', $search)
        ->groupBy('latest_trip_name')->limit(10)->get();
        return response()->json($result);
    }

    public function filterCities(Request $request)
    {
        $result = ParticipantCRM::select(['city'])->whereNotNull('city')->orderBy('city', 'ASC')->groupBy('city')->get();
        return response()->json($result);
    }

    public function filterProvinces(Request $request)
    {
        $result = ParticipantCRM::select(['province'])->whereNotNull('province')->orderBy('province', 'ASC')->groupBy('province')->get();
        return response()->json($result);
    }

    public function filterTotalAccount(Request $request)
    {
        $result = ParticipantCRM::select(['parent_account'])->where('parent_account', '>', 0)->groupBy('parent_account')->get();
        return response()->json($result);
    }

    public function filterPackages(Request $request)
    {
        $result = ParticipantCRM::select(['latest_trip_package'])->whereNotNull('latest_trip_package')->orderBy('latest_trip_package', 'ASC')->groupBy('latest_trip_package')->get();
        return response()->json($result);
    }

    public function filterJobs(Request $request)
    {
        $result = ParticipantCRM::select(['job'])->whereNotNull('job')->orderBy('job', 'ASC')->groupBy('job')->get();
        return response()->json($result);
    }

    public function uploadMiladPhoto(Request $request)
    {
        $request->validate([
            'participant_id' => 'required',
            'title' => 'required',
            'file_upload' => 'required|file|mimes:jpg,png,pdf',
        ]);
        $file = $request->file('file_upload');
        $filePath = $file->store(ParticipantFile::DIR_FILE);
        $request->merge(['file_path' => $filePath, 'file_type' => $file->getClientMimeType()]);
        ParticipantFile::updateOrCreate(['participant_id' => $request->participant_id, 'title' => 'Foto Milad'], $request->except(['file_upload']));
    }

    public function getCertificateImage($id)
    {
        $participant = Participant::join('participant_umroh_trips', 'participant_umroh_trips.participant_id', 'participant.id')
            ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
            ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
            ->select(
                'participant.title',
                'participant.front_title',
                'participant.back_title',
                'participant.name',
                'participant.name_in_passport',
                'participant.name_in_certificate',
                'umroh_trips.id as umroh_trip_id',
                'package_umroh_trips.name as package_name',
            )->where('participant.id', $id)->first();
        $photo = (new CertificateImage($participant, $participant->umroh_trip_id))->stream();

        return response()->json(['photo' => $photo]);
    }

    public function downloadCertificate(Request $request)
    {
        GenerateDocumentCRMPDF::dispatch("certificate_participant_crm", $request->all());
        return response()->json(['success' => true]);
    }

    public function certificate($participantId)
    {
        return (new CertificateParticipant($participantId))->download();
    }

    public function previewCertificate($participantId)
    {
        return (new CertificateParticipant($participantId))->stream();
    }

    public function files($id)
    {
        $participantFiles = Participant::select(['id', 'name', 'profile_photo_path'])->with('files')->findOrFail($id);
        return response()->json($participantFiles);
    }

    public function deleteMiladPhoto(Request $request)
    {
        $request->validate([
            'participant_id' => 'required',
            'title' => 'required',
        ]);

        ParticipantFile::where('participant_id', $request->participant_id)->where('title', $request->title)->delete();
    }

    public function downloadExampleImport()
    {
        $path = storage_path('example/format-import-data-participant-crm.xlsx');
        $headers = ['Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        return response()->download($path, 'format-import-data-participant-crm.xlsx', $headers);
    }

    public function importParticipantCRM(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|mimes:xlsx|file|max:1024'
        ]);

        $file = $request->file('file');

        $import = new ParticipantCRMImport();
        $import->import($file);

        if (count($import->failures()) > 0) {
            return response()->json(['message' => $this->importErrorParse($import->failures())], 422);
        }

        $result = [
            'error' => false,
            'message' => 'Import Participant Berhasil'
        ];
        return response()->json($result);
    }

    private function importErrorParse($errors)
    {
        $messages = [];
        foreach($errors as $error) {
            $messageErr = implode(', ', $error->errors());
            $no = $error->values()[0];
            $val = $error->values()[$error->attribute()];
            $messages[] = "#{$no}: ({$val}) {$messageErr}";
        }

        return implode('<br/><br/>', $messages);
    }

    public function exportParticipantCRM(Request $request) {
        $storageKey = "CRM-EXPORT-". date('d-m-Y') . ".xlsx";
        return Excel::download(new ParticipantCRMExport($request->all()), $storageKey);
    }

    public function refineParticipantCRM(Request $request)
    {
        RefineParticipantCRM::dispatch();

        return response()->json(['status'=>'processing in background']);
    }

    public function refineManualParticipantCRM(Request $request)
    {
        $type = "update";
        if($request->haji) {
            $type = "haji";
        }
        $data = ParticipantCRM::generateParticipantCRMDaily($type);
        echo json_encode($data);
    }

    public function listParticipantMerge(Request $request)
    {
        $participants = ParticipantCRM::where('no_hp', 'like', '%'.$request->reference.'%')->orderBy('created_at', 'ASC')->get();
        return response()->json($participants);
    }

    public function mergeJmaah(Request $request)
    {
        $parent = ParticipantCRM::find($request->participant_crm_id);
        DB::beginTransaction();
        try {
            $totalTransaction = 0;
            $totalTrip = 0;
            $participants = ParticipantCRM::where('id', '!=', $parent->id)->where('no_hp', 'like', '%'.$parent->reference.'%')->get();
            foreach ($participants as $participant) {
                $totalTransaction = $participant->total_transaction;
                $totalTrip = $participant->total_trip;
                $participant->update(['need_merge' => 0, 'participant_crm_id' => $parent->id]);
                $participant->delete();
            }
            $parent->update([
                'need_merge' => 0,
                'total_transaction' => $parent->total_transaction + $totalTransaction,
                'total_trip' => $parent->total_trip + $totalTrip,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }

        return response()->json($parent);
    }

    public function removeMergeJmaah(Request $request)
    {
        $parent = ParticipantCRM::find($request->participant_crm_id);
        DB::beginTransaction();
        try {
            $parent->update([
                'need_merge' => 0,
                'reference' => null
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }

        return response()->json($parent);
    }

    public function setParentAccount(Request $request)
    {
        $parent = ParticipantCRM::find($request->participant_crm_id);
        DB::beginTransaction();
        try {
            $parent->update([
                'parent_account' => $parent->parent_account + 1,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }

        return response()->json($parent);
    }

    public function transactions(Request $request)
    {
        $participantCrm = ParticipantCRM::where('participant_id', $request->participantId)->first();
        $historyTrip = ($participantCrm->history_participant_umroh_trips) ? explode(',',$participantCrm->history_participant_umroh_trips) : [];
        $transactions = ParticipantUmrohTrip::select(['participant_umroh_trips.id','participant_id', 'booking_order_no',
            'umroh_trips.title','umroh_trips.departure_at','umroh_trips.return_at','package_umroh_trips.name', 'umroh_trips.currency',
            'participant_umroh_trips.price_per_pax', 'participant_umroh_trips.discount', 'order_umroh_trip_id', 'order_umroh_trips.sales_name','participant_umroh_trips.role_type'])
        ->join('participants', 'participant.id' , 'participant_umroh_trips.participant_id')
        ->join('umroh_trips', 'umroh_trips.id' , 'participant_umroh_trips.umroh_trip_id')
        ->leftJoin('order_umroh_trips', 'order_umroh_trips.id' , 'participant_umroh_trips.order_umroh_trip_id')
        ->join('package_umroh_trips', 'package_umroh_trips.id' , 'participant_umroh_trips.package_umroh_trip_id')
        ->whereIn('participant_umroh_trips.id', $historyTrip)->orderBy('umroh_trips.departure_at', 'DESC')->get();

        foreach ($transactions as $key => $value) {
            // $totalDiscount = DiscountOrderUmrohTrip::where('order_umroh_trip_id', $value->order_umroh_trip_id)->where('assigned_participant', 'like', '%'.$value->participant_id.'%')->sum('discount_per_pax') ?? 0;
            $totalTransaction = OrderItemUmrohTrip::where('order_umroh_trip_id', $value->order_umroh_trip_id)->where('assigned_participant', 'like', '%'.$value->participant_id.'%')->whereNull('room_type')->count('price') ?? 0;

            $value->discount = $value->discount ?? 0;

            $total_transaction = ($value->price_per_pax - $value->discount) + $totalTransaction;

            $convertion = 1;
            if($value->currency == 'USD') {
                $convertion = 15000;
            }
            $salesName = $value->sales_name;
            if($value->booking_order_no == null) {
                $salesName = '';
            }
            if($value->role_type == 2) {
                $salesName = 'TOUR LEADER';
            }
            if($value->role_type == 4) {
                $salesName = 'FOC';
            }
            if($value->role_type == 5) {
                $salesName = 'RUNNER';
            }
            $value->sales_name = $salesName;
            $value->total_transaction = $total_transaction;
            $value->total_transaction_conv = $total_transaction * $convertion;
        }


        $backDateHistories = ParticipantCRMTransactionBackdateHistories::where('participant_crm_id', $participantCrm->id)->get();
        $dataHistory = array();
        foreach ($backDateHistories as $value) {
            $convertion = 1;
            if($value->currency == 'USD') {
                $convertion = 15000;
            }

            $total_transaction = $value->total_transaction;
            $total_transaction_conv = $total_transaction * $convertion;
            $dataHistory[] = [
                'id' => $value->id,
                'title' => $value->umroh_trip_name,
                'name' => $value->package_name,
                'currency' => "IDR",
                'discount' => 0,
                'total_transaction' => $total_transaction,
                'total_transaction_conv' => $total_transaction_conv,
                'sales_name' => 'Data Back Date'
            ];
        }

        $transactions = $transactions->toArray();
        $transactions = array_merge($transactions, $dataHistory);


        return response()->json($transactions);
    }

    public function viewParentAccount($participantCrmId, Request $request) {
        $participantCrm = ParticipantCRM::find($participantCrmId);
        $participantHistory = explode(',',$participantCrm->history_participant_umroh_trips);

        $participantUmrohTrips = ParticipantUmrohTrip::whereIn('id', $participantHistory)->get();

        $umrohTrips = array();
        foreach ($participantUmrohTrips as $participant) {
            $order = OrderUmrohTrip::where('order_no', $participant->booking_order_no)->first();
            if($order) {
                if($participantCrm->no_hp == $order->no_hp) {
                    $umrohTrip = UmrohTrip::find($participant->umroh_trip_id);
                    $umrohTrips[] = $umrohTrip->title;
                }
            }
        }

        return response()->json($umrohTrips);
    }

    public function refineParticipantCRMTotalTransaction(Request $request)
    {
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 1000;
        $participantId = $request->get('participantId');

        $query = DB::table('participant_crm')->select(['participant_crm.*']);
        if(!empty($participantId)) {
            $query->where('participant_id', $participantId);
        }

        $participants = $query->orderBy('participant_crm.created_at', 'ASC')->limit($limit)->offset(($page - 1) * $limit)->get();
        $data = array();
        foreach ($participants as $participantcrm) {
            $grandTotalTransaction = 0;
            $participantUmrohTrips = DB::table('participant_umroh_trips')->where('participant_id', $participantcrm->participant_id)->get();
            foreach ($participantUmrohTrips as $participantUmrohTrip) {
                $umrohTrip = DB::table('umroh_trips')->where('id', $participantUmrohTrip->umroh_trip_id)->first();

                $price_per_pax = $participantUmrohTrip->price_per_pax ?? 0;
                $convertion = 1;
                if($umrohTrip->currency == 'USD') {
                    $convertion = 15000;
                }
                $price_per_pax = $price_per_pax;
                $total_discount = ($participantUmrohTrip->discount > 0) ? $participantUmrohTrip->discount : 0;
                $total_transaction = ($price_per_pax - $total_discount);
                $total_transaction_conv = ($total_transaction * $convertion);

                $grandTotalTransaction+= $total_transaction_conv;
            }
            $params = [
                'total_transaction' => $grandTotalTransaction,
            ];

            $data[] = $params;

            ParticipantCRM::where('participant_id', $participantcrm->participant_id)->update(
                $params
            );
        }

        return response()->json($data);
    }

    public function checkYears(){
        $data = ParticipantCRMTransactionBackdateHistories::select('year')->whereNull('year')->first();
        if($data){
            $this->updateYears();
        }
    }

    public function updateYears(){
        $years = [2018, 2019, 2020, 2022, 2023, 2024, 2025, 2026, 2028, 2029];
        $data = ParticipantCRMTransactionBackdateHistories::updateYear($years);
    }
}
