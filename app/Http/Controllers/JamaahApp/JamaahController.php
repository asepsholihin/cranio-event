<?php

namespace App\Http\Controllers\ParticipantApp;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\ParticipantFile;
use App\Models\UmrohTrip;
use App\Models\PackageUmrohTrip;
use App\Models\ParticipantUmrohTrip;
use App\Models\OrderUmrohTrip;
use App\Models\OrderItemUmrohTrip;
use App\Models\EventAttendance;
use App\Models\EquipmentDelivery;
use App\Models\ParticipantLetterInformation;
use App\Models\InvoiceUmrohTrip;
use App\Models\CreditImageReceipt;
use App\Models\Attendance;
use App\Models\Equipment;
use App\Models\Baggage;
use App\Models\DetailBaggage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Image;
use App\File\Image\BarcodeParticipant;
use App\File\PDF\LetterParticipant as PDFLetterParticipant;
use App\File\Word\LetterParticipant as WordLetterParticipant;
use App\File\PDF\ReceiptPDF;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CrewApp\Participant\StoreBaggageRequest;

class ParticipantController extends Controller
{
    public function __construct()
    {
        App::setLocale('id');
    }

    public function profile(Request $request)
    {
        return $request->user();
    }

    public function trip(Request $request)
    {
        $participant = $request->user();
        $response = ParticipantUmrohTrip::
        select(['umroh_trips.id as umroh_trip_id','umroh_trips.title','participant_umroh_trips.booking_order_no','participant_umroh_trips.ticket_number','order_umroh_trips.id as order_umroh_trip_id','order_umroh_trips.sales_name','order_umroh_trips.total_pax_trip'])
        ->join('order_umroh_trips', 'order_umroh_trips.order_no', 'participant_umroh_trips.booking_order_no')
        ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
        ->where('participant_id', $participant->id)
        // ->where('departure_at', '>=', Carbon::now())
        ->first();

        if($response){
            $response['pax_infants'] = OrderItemUmrohTrip::firstWhere('order_umroh_trip_id', $response->order_umroh_trip_id)->pax_infants ?? 0;
            $event = EventAttendance::firstWhere('umroh_trip_id', $response->umroh_trip_id);
            $equipment =  EquipmentDelivery::where('umroh_trip_id', $response->umroh_trip_id)->where('participant_id', $participant->id)->first();
            
            $deliveryStatus = "Belum di Proses";
            if($equipment) {
                if ($equipment->delivery_status == 1)
                    $deliveryStatus = 'Belum di Proses';
                if ($equipment->delivery_status == 2)
                    $deliveryStatus = 'Sedang di Proses';
                if ($equipment->delivery_status == 3)
                    $deliveryStatus = 'Dalam Pengiriman';
                if ($equipment->delivery_status == 4)
                    $deliveryStatus = 'Telah Diterima';
                if ($equipment->delivery_status == 5)
                    $deliveryStatus = 'Dalam Proses Ulang';
            }

            $ticketStatus = "Belum Tersedia";
            if ($response->ticket_number)
                $ticketStatus = 'Sudah Tersedia';

            $response['event_date'] = $event->event_date ?? null;
            $response['event_location'] = $event->location ?? null;
            $response['event_at'] = $event->event_at ?? null;
            $response['equipment_status'] = $deliveryStatus;
            $response['ticket_status'] = $ticketStatus;
        }
        
        return response()->json($response);
    }

    public function tripDetail(Request $request, $tripId, $orderUmrohTripId)
    {
        $participant = $request->user();
        $response = ParticipantUmrohTrip::
        select(['umroh_trips.id as umroh_trip_id',
        'umroh_trips.title',
        'participant_umroh_trips.booking_order_no',
        'participant_umroh_trips.ticket_number',
        'order_umroh_trips.id as order_umroh_trip_id',
        'order_umroh_trips.sales_name',
        'order_umroh_trips.total_pax_trip',
        'order_umroh_trips.name',
        'order_umroh_trips.due_payment',
        'package_umroh_trips.name as package_name',
        'package_umroh_trips.hotel_makkah',
        'package_umroh_trips.hotel_madinah',
        'participant_umroh_trips.room_type',
        'umroh_trips.departure_at',
        'umroh_trips.return_at',
        'crew.name as tour_leader',
        'mutawwif.name as mutawwif',
        ])
        ->join('order_umroh_trips', 'order_umroh_trips.order_no', 'participant_umroh_trips.booking_order_no')
        ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
        ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
        ->leftJoin('participant as crew', 'umroh_trips.tour_leader', '=', 'crew.id')
        ->leftJoin('participant as mutawwif', 'umroh_trips.mutawwif', '=', 'mutawwif.id')
        ->where('umroh_trips.id', $tripId)
        ->where('order_umroh_trips.id', $orderUmrohTripId)->first();

        $response['pax_infants'] = OrderItemUmrohTrip::firstWhere('order_umroh_trip_id', $response->order_umroh_trip_id)->pax_infants ?? 0;
        $response['pax_without_bed'] = OrderItemUmrohTrip::firstWhere('order_umroh_trip_id', $response->order_umroh_trip_id)->without_bed ?? 0;
        $event = EventAttendance::firstWhere('umroh_trip_id', $response->umroh_trip_id);
        $equipment =  EquipmentDelivery::where('umroh_trip_id', $response->umroh_trip_id)->where('participant_id', $participant->id)->first();
        
        $deliveryStatus = "Belum di Proses";
        if($equipment) {
            if ($equipment->delivery_status == 1)
                $deliveryStatus = 'Belum di Proses';
            if ($equipment->delivery_status == 2)
                $deliveryStatus = 'Sedang di Proses';
            if ($equipment->delivery_status == 3)
                $deliveryStatus = 'Dalam Pengiriman';
            if ($equipment->delivery_status == 4)
                $deliveryStatus = 'Telah Diterima';
            if ($equipment->delivery_status == 5)
                $deliveryStatus = 'Dalam Proses Ulang';
        }

        $paymentStatus = "Belum Lunas";
        if ($response->due_payment == 0)
            $paymentStatus = 'Lunas';

        $ticketStatus = "Belum Tersedia";
        if ($response->ticket_number)
            $ticketStatus = 'Sudah Tersedia';

        $response['event_id'] = $event->id ?? 0;
        $response['event_date'] = $event->event_date ?? null;
        $response['event_location'] = $event->location ?? null;
        $response['event_at'] = $event->event_at ?? null;
        $response['equipment_status'] = $deliveryStatus;
        $response['payment_status'] = $paymentStatus;
        $response['ticket_status'] = $ticketStatus;
        
        return response()->json($response);
    }

    public function invoice(Request $request)
    {
        $participant = $request->user();
        $response = InvoiceUmrohTrip::query()
        ->join('order_umroh_trips', 'order_umroh_trips.id', 'invoice_umroh_trips.order_umroh_trip_id')
        ->where('order_umroh_trips.order_no', $participant->suggest_booking_order)
        ->where('invoice_umroh_trips.status', 1)
        ->select([
            'invoice_umroh_trips.id',
            'order_umroh_trips.id as order_umroh_trip_id',
            'order_umroh_trips.total_payment',
            'order_umroh_trips.due_payment',
            'invoice_umroh_trips.invoice_no',
            'invoice_umroh_trips.created_at',
            'invoice_umroh_trips.due_date',
            'invoice_umroh_trips.description',
            'invoice_umroh_trips.payment_date',
            'invoice_umroh_trips.payment_method',
            'invoice_umroh_trips.other_bank',
            'invoice_umroh_trips.payment',
            'invoice_umroh_trips.payment_note',
            'invoice_umroh_trips.status',
        ])
        ->first();
        
        return response()->json($response);
    }

    public function invoiceDetail(Request $request, $invoiceId)
    {
        $participant = $request->user();
        $response = InvoiceUmrohTrip::
        select(['umroh_trips.id as umroh_trip_id',
        'umroh_trips.title',
        'order_umroh_trips.order_no',
        'order_umroh_trips.id as order_umroh_trip_id',
        'order_umroh_trips.sales_name',
        'order_umroh_trips.total_pax_trip',
        'order_umroh_trips.name',
        'order_umroh_trips.due_payment',
        'order_umroh_trips.total_payment',
        'package_umroh_trips.name as package_name',
        'participant_umroh_trips.room_type',
        'invoice_umroh_trips.invoice_no',
        'invoice_umroh_trips.payment_amount',
        'order_umroh_trips.status',
        ])
        ->join('order_umroh_trips', 'order_umroh_trips.id', 'invoice_umroh_trips.order_umroh_trip_id')
        ->join('participant_umroh_trips', 'order_umroh_trips.order_no', 'participant_umroh_trips.booking_order_no')
        ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
        ->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
        ->where('invoice_umroh_trips.id', $invoiceId)->first();

        $response['pax_infants'] = OrderItemUmrohTrip::firstWhere('order_umroh_trip_id', $response->order_umroh_trip_id)->pax_infants ?? 0;
        $response['pax_without_bed'] = OrderItemUmrohTrip::firstWhere('order_umroh_trip_id', $response->order_umroh_trip_id)->without_bed ?? 0;
        
        return response()->json($response);
    }

    public function invoiceList(Request $request)
    {
        $participant = $request->user();
        $response = InvoiceUmrohTrip::query()
        ->join('order_umroh_trips', 'order_umroh_trips.id', 'invoice_umroh_trips.order_umroh_trip_id')
        ->where('order_umroh_trips.order_no', $participant->suggest_booking_order)
        ->select([
            'invoice_umroh_trips.id',
            'invoice_umroh_trips.invoice_no',
            'invoice_umroh_trips.created_at',
            'invoice_umroh_trips.due_date',
            'invoice_umroh_trips.description',
            'invoice_umroh_trips.payment_date',
            'invoice_umroh_trips.payment_method',
            'invoice_umroh_trips.other_bank',
            'invoice_umroh_trips.payment_amount',
            'invoice_umroh_trips.payment_note',
            'invoice_umroh_trips.credit_image_receipt',
            'invoice_umroh_trips.status',
        ])
        ->get();
        
        return response()->json($response);
    }

    public function barcode(Request $request)
    {
        $participant = $request->user();
        return (new BarcodeParticipant($participant))->stream();
    }

    public function barcodeParticipant($id, Request $request)
    {
        $participant = Participant::find($id);
        return (new BarcodeParticipant($participant))->stream();
    }

    public function barcodeParticipantByBooking($orderUmrohTripId, Request $request)
    {
        $orderUmrohTrip = OrderUmrohTrip::firstWhere('id', $orderUmrohTripId);
        $participant = Participant::where('suggest_booking_order', $orderUmrohTrip->order_no)->get();

        $barcodes = array();
        $event = EventAttendance::firstWhere('umroh_trip_id', $orderUmrohTrip->umroh_trip_id);
        foreach($participant as $row) {
            $attendee = Attendance::where('event_id', $event->id)->where('participant_id', $row->id)->first();
            $data = array();
            $data['check_in_at'] = $attendee->check_in_at??null;
            $data['photo_at'] = $attendee->photo_at??null;
            $data['barcode'] = (new BarcodeParticipant($row))->stream();
            $barcodes[] = $data;
        }
        return $barcodes;
    }

    public function updateProfile(Request $request)
    {
        try {
            Participant::where('id', $request->user()->id)->update($request->except(['photo']));
            $response = [
                'success' => true,
                'message' => 'Data saved'
            ];
        } catch(\Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Error'
            ];
        }
        
        return response()->json($response);
    }

    public function addMemberParticipant(Request $request)
    {
        try {
            $request['refer_by'] = $request->user()->id;
            $participant = Participant::create($request->except(['photo']));
            $response = [
                'success' => true,
                'message' => 'Data saved',
                'created_participant' => $participant
            ];
        } catch(\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e
            ];
        }
        
        return response()->json($response);
    }

    public function updateMemberParticipant(Request $request)
    {
        try {
            Participant::where('id', $request->id)->update($request->except(['photo']));
            $response = [
                'success' => true,
                'message' => 'Data saved'
            ];
        } catch(\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e
            ];
        }
        
        return response()->json($response);
    }

    public function listMemberParticipant(Request $request)
    {
        try {
            $participant = Participant::where('refer_by', $request->user()->id)->where('name', 'like', '%' . request('keyword') . '%')->get();

            $rows = [];
            foreach ($participant as $row) {
                $row['check_ktp'] = Participant::checkFile($row->id,'KTP');
                $row['check_kk'] = Participant::checkFile($row->id,'Kartu Keluarga');
                $row['check_akta'] = Participant::checkFile($row->id,'Akta Kelahiran');
                $row['check_buku_nikah'] = Participant::checkFile($row->id,'Buku Nikah');
                $row['check_buku_kuning'] = Participant::checkFile($row->id,'Buku Kuning');
                $row['check_passport'] = Participant::checkFile($row->id,'Passport');
                $row['check_kartu_vaksin'] = Participant::checkFile($row->id,'Kartu Vaksin');
                $row['check_photo'] = ($row->profile_photo_path != null) ? true : false;
                $rows[] = $row;
            }
            $response = $rows;
        } catch(\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e
            ];
        }
        
        return response()->json($response);
    }

    public function detailMemberParticipant(Request $request, $id)
    {
        return Participant::find($id);
    }

    public function uploadFileParticipant(StoreParticipantFileRequest $request)
    {
        try {
            foreach($request->file_path as $item) {
                if($request->title == "Pas Photo") {
                    Participant::select(['id','profile_photo_path'])->where('id', $request->participant_id)->update(['profile_photo_path' => $item]);
                } else {
                    ParticipantFile::create(
                        [
                            'participant_id' => $request->participant_id, 
                            'title' => $request->title,
                            'file_type' => 'image',
                            'file_path' => $item
                        ]
                    );
                }
            }
            
            $response = [
                'success' => true,
                'message' => 'Data saved'
            ];
        } catch(\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        
        return response()->json($response);
    }

    public function listParticipantByBooking($orderUmrohTripId, Request $request)
    {
        try {
            $orderUmrohTrip = OrderUmrohTrip::firstWhere('id', $orderUmrohTripId);
            $participant = Participant::
            select([
                'participant.id','participant.name','participant.email','participant.no_hp','participant.gender','participant.nik','participant.birth_place','participant.birth_date','participant.no_passport','participant.passport_expired_date','participant.ktp_address','participant.body_size','participant.chest_size','participant.body_height',
                'participant_umroh_trips.id as participant_umroh_trip_id','participant_umroh_trips.room_type', 'package_umroh_trips.name as package_name', 'participant_umroh_trips.group_bus', 'participant_umroh_trips.group_hotel_room', 'participant_umroh_trips.ticket_number', 'participant_umroh_trips.siskopatuh_id'
            ])
            ->join('participant_umroh_trips', 'participant.id', 'participant_umroh_trips.participant_id')
            ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', 'package_umroh_trips.id')
            ->where('suggest_booking_order', $orderUmrohTrip->order_no)
            ->where('participant.name', 'like', '%' . request('keyword') . '%')->get();
            
            $event = EventAttendance::firstWhere('umroh_trip_id', $orderUmrohTrip->umroh_trip_id);
            foreach($participant as $row) {
                if($event) {
                    $attendee = Attendance::where('event_id', $event->id)->where('participant_id', $row->id)->first();
                }
                $row['check_in_at'] = $attendee->check_in_at??null;
                $row['photo_at'] = $attendee->photo_at??null;
                $row['siskopatuh_id'] = str_replace('https://siskopatuh.kemenag.go.id/web/npu/?id=', '', $row->siskopatuh_id);
            }

            $response = $participant;
        } catch(\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        
        return response()->json($response);
    }

    public function letterAddressParticipant($participantId, Request $request)
    {
        try {
            $letterAddress = ParticipantLetterInformation::where('participant_id', $participantId)->first();
            
            $response = $letterAddress;
        } catch(\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e
            ];
        }
        
        return response()->json($response);
    }

    public function addLetterAddressParticipant(Request $request)
    {
        try {
            $participant = Participant::find($request->get('participant_id'));
            $request->merge([
                'city' => $participant ->home_city??null,
                'province' => $participant ->home_province??null,
            ]);
            ParticipantLetterInformation::updateOrCreate(['participant_id' => $request->get('participant_id')], $request->all());
            $response = [
                'success' => true,
                'message' => 'Data saved'
            ];
        } catch(\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e
            ];
        }
        
        return response()->json($response);
    }

    public function generateLetter(ParticipantUmrohTrip $participantUmrohTrip, Request $request)
    {
        if (request()->type == "word") {
            return (new WordLetterParticipant(date('Y'), $participantUmrohTrip, request()->get('umrohTripId'), request()->get('category')))->download();
        } else {
            return (new PDFLetterParticipant(date('Y'), $participantUmrohTrip, request()->get('umrohTripId'), request()->get('category')))->download();
        }
    }

    public function groupRoomByBooking($orderUmrohTripId, Request $request)
    {
        try {
            $orderUmrohTrip = OrderUmrohTrip::firstWhere('id', $orderUmrohTripId);
            $groupRooms = ParticipantUmrohTrip::
            select('participant_umroh_trips.group_hotel_room')
            ->join('participant', 'participant.id', 'participant_umroh_trips.participant_id')
            ->where('booking_order_no', $orderUmrohTrip->order_no)
            ->where('participant.name', 'like', '%' . request('keyword') . '%')
            ->groupBy('participant_umroh_trips.group_hotel_room')
            ->get();

            foreach($groupRooms as $row) {
                $row['participant'] = ParticipantUmrohTrip::
                select(['participant.id','participant.name','participant.gender','participant.no_passport', 'participant_umroh_trips.room_type','package_umroh_trips.name as package_name'])
                ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', 'package_umroh_trips.id')
                ->join('participant', 'participant.id', 'participant_umroh_trips.participant_id')
                ->where('booking_order_no', $orderUmrohTrip->order_no)->get();
            }

            $response = $groupRooms;
        } catch(\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e
            ];
        }
        
        return response()->json($response);
    }

    public function uploadCreditImageReceipt(Request $request)
    {
        try {
            $invoice = InvoiceUmrohTrip::find($request->invoice_id);
            $primaryImage = "";
            if ($request->hasfile('file')) { 
                $files = [];
                foreach ($request->file('file') as $file) {
                    $filePath = $file->store(InvoiceUmrohTrip::DIR_CREDIT_RECEIPT);
                    $request->merge(['invoice_umroh_trip_id'=>$invoice->id, 'image' => $filePath]);

                    $primaryImage = $filePath;
                    CreditImageReceipt::create($request->except(['file']));
                }
            }

            $invoice->update([
                'credit_status' => 1,
                'payment_note' => request()->payment_note,
                'credit_image_receipt' => $primaryImage
            ]);
            
            $response = [
                'success' => true,
                'message' => 'Data saved'
            ];
        } catch(\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        
        return response()->json($response);
    }

    public function downloadInvoiceReceipt(InvoiceUmrohTrip $invoice)
    {
        if ($invoice->status != InvoiceUmrohTrip::STATUS_PAID) {
            throw new NotFoundHttpException();
        }

        $order = OrderUmrohTrip::find($invoice->order_umroh_trip_id);
        return (new ReceiptPDF($order, $invoice))->download();
    }

    public function postAttendee(Request $request)
    {
        $request->validate([
            'event_id' => 'required_if:act,add|required_if:act,checkinBarcode,photoBoothBarcode',
            'participant_id' => 'required_if:act,add',
            'barcode' => 'required_if:act,checkinBarcode|uuid',
            'id' => 'required_if:act,checkin',
            'act' => 'required|in:add,remove,checkin,checkinBarcode,photoBoothBarcode',
        ],['barcode.required' => 'Barcode is invalid','barcode.uuid' => 'Barcode is invalid']);
        
        $attendee = Attendance::updateOrCreate(['event_id' => $request->event_id, 'participant_id' => $request->participant_id], $request->all());
        if ($attendee->check_in_at == null) {
            $attendee->check_in_at = now();
        } else {
            $attendee->check_in_at = null;
        }
        $attendee->save();

        return response()->json(['success' => true, 'message' => 'Success']);
    }

    public function equipmentDeliveryDetail(Request $request, $umrohTripId, $participantId)
    {
        $equipment =  EquipmentDelivery::join('participant', 'participant.id', 'equipment_deliveries.participant_id')
        ->join('package_umroh_trips', 'package_umroh_trips.id', 'equipment_deliveries.package_umroh_trip_id')
        ->where('equipment_deliveries.umroh_trip_id', $umrohTripId)->where('equipment_deliveries.participant_id', $participantId)
        ->select('participant.name','participant.gender','package_umroh_trips.name as package_name','participant.body_size','participant.chest_size','participant.body_height','equipment_deliveries.*')
        ->first();

        return $equipment;
    }

    public function equipments(Request $request, $packageUmrohTripId, $gender)
    {
        $packageUmrohTrip = PackageUmrohTrip::find($packageUmrohTripId);
        $packageType = 1;
        if($packageUmrohTrip->name == "Ruby") {
            $packageType = 1;
        }
        if($packageUmrohTrip->name == "Emerald") {
            $packageType = 2;
        }
        if($packageUmrohTrip->name == "Sapphire") {
            $packageType = 3;
        }
        if($packageUmrohTrip->name == "Plus") {
            $packageType = 4;
        }

        $equipments =  Equipment::join('equipment_details', 'equipment_details.equipment_id', 'equipments.id')->where('package_type', $packageType)->where('gender', $gender)->get();
        return $equipments;
    }

    public function receivedEquipmentParticipant(Request $request)
    {
        $equipmentDetail = DB::table('equipment_details')
        ->where('equipment_delivery_id', $request->equipment_delivery_id)
        ->where('participant_id', $request->participant_id)->get();
        if(count($equipmentDetail) > 0) {
            try {
                foreach ($request->equipments as $id) {
                    $equipment =  Equipment::find($id);
                    DB::table('equipment_details')
                    ->where('equipment_delivery_id', $request->equipment_delivery_id)
                    ->where('participant_id', $request->participant_id)
                    ->update([
                        'equipment_delivery_id' => $request->equipment_delivery_id,
                        'participant_id' => $request->participant_id,
                        'equipment_id' => $id,
                        'qty' => $equipment->qty,
                        'status' => 2,
                        'received_by' => $request->participant_id,
                        'received_date' => Carbon::now(),
                    ]);
                }

                $image = "";
                if ($request->hasfile('file')) { 
                    $files = [];
                    foreach ($request->file('file') as $file) {
                        $filePath = $file->store(EquipmentDelivery::DIR_RECEIVED_EVIDENCE);
                        $image = $filePath;
                    }
                }
                
                EquipmentDelivery::where('id',$request->equipment_delivery_id)->update([
                    'received_notes' => $request->received_notes,
                    'delivery_status' => 4,
                    'received_evidence' => $image
                ]);
                $response = [
                    'success' => true,
                    'message' => 'Data saved'
                ];
            } catch(\Exception $e) {
                $response = [
                    'success' => false,
                    'message' => 'Error'
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'Belum ada pengiriman'
            ];
        }

        return response()->json($response);
    }

    public function listBaggageParticipantByBooking($orderUmrohTripId, Request $request)
    {
        try {
            $orderUmrohTrip = OrderUmrohTrip::firstWhere('id', $orderUmrohTripId);
            $participant = Participant::
            select([
                'participant.id','participant.email','participant.name','participant.no_hp','participant_umroh_trips.umroh_trip_id','participant_umroh_trips.package_umroh_trip_id',
                'participant_umroh_trips.room_type', 'package_umroh_trips.name as package_name'
            ])
            ->join('participant_umroh_trips', 'participant.id', 'participant_umroh_trips.participant_id')
            ->join('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', 'package_umroh_trips.id')
            ->where('suggest_booking_order', $orderUmrohTrip->order_no)
            ->where('participant.name', 'like', '%' . request('keyword') . '%')->get();
            
            foreach($participant as $row) {
                $baggageByParticipant = Baggage::where('location_id',5)
                ->where('city_id',7)->where('umroh_trip_id',$row->umroh_trip_id)->where('participant_id',$row->id)->first();
                $row->participant_total_bags = $baggageByParticipant->total_bags??0;
                $row->participant_total_cabin = $baggageByParticipant->total_cabin??0;

                $baggageByCrew = Baggage::where('location_id',1)
                ->where('city_id',1)->where('umroh_trip_id',$row->umroh_trip_id)->where('participant_id',$row->id)->first();
                $row->crew_total_bags = $baggageByCrew->total_bags??0;
                $row->crew_total_cabin = $baggageByCrew->total_cabin??0;

                $jumlahMutawwif = ParticipantUmrohTrip::select('participant.name','participant.no_hp')->join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')->where('role_type', 3)->where('umroh_trip_id', $row->umroh_trip_id)->get();
                if(count($jumlahMutawwif) == 1) {
                    $mutawwif = $jumlahMutawwif[0];
                    $row->mutawwif = $mutawwif->name ?? '';
                } else {
                    $mutawwif = ParticipantUmrohTrip::select('participant.name','participant.no_hp')->join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')->where('role_type', 3)->where('umroh_trip_id', $row->umroh_trip_id)->where('group_bus', $row->group_bus)->first();
                    $row->mutawwif = $mutawwif->name ?? '';
                }
            }

            $response = $participant;
        } catch(\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        
        return response()->json($response);
    }

    public function photoBaggage(StoreBaggageRequest $request)
    {
        Baggage::updateOrCreate(
            [
                'location_id' => $request->location_id,
                'city_id' => $request->city_id,
                'umroh_trip_id' => $request->umroh_trip_id,
                'package_umroh_trip_id' => $request->package_umroh_trip_id,
                'participant_id' => $request->participant_id,
            ],
            $request->except(['photo'])
        );
        $baggageId = Baggage::where('location_id',$request->location_id)
        ->where('city_id',$request->city_id)->where('umroh_trip_id',$request->umroh_trip_id)
        ->where('package_umroh_trip_id',$request->package_umroh_trip_id)->where('participant_id',$request->participant_id)->first();

        // DetailBaggage::where('baggage_id', $baggageId->id)->delete();
        if ($request->image) { 
            foreach($request->image as $item) {
                DetailBaggage::create(
                    [
                        'baggage_id' => $baggageId->id,
                        'image' => $item
                    ]
                );
            }
        }
        return response()->json(['success' => 'ok']);
    }

    public function deleteAccount(Request $request)
    {
        $participant = $request->user();
        try {
            Participant::where('id', $request->user()->id)->update(['access_status' => Participant::ACCESS_STATUS_DISABLED]);
            $response = [
                'success' => true,
                'message' => 'Data saved'
            ];
        } catch(\Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Error'
            ];
        }
        
        return response()->json($response);
    }
    
}
