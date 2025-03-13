<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use App\Actions\Xendit\Xendit;
use App\Jobs\SendWhatsappBookingHotelEvent;

class BookingHotelEvent extends Model
{
    use SoftDeletes;

    const DIR_RECEIPT = 'web/receipts';
    const PREFIX_ORDER_NUMBER = 'JIBB/INV/MANASIK/';
    const MONTH_ROMAWI = [1=>"I","II","III", "IV", "V","VI","VII","VIII","IX","X", "XI","XII"];

    const STATUS_PENDING = "pending";
    const STATUS_PAID = "paid";
    const STATUS_BOOKED = "booked";
    const STATUS_ACCESS_GIVEN = "access_given";
    const STATUS_CANCEL = "cancelled";
    const STATUS_PAYMENT_EXPIRED = "payment_expired";

    protected $fillable = [
        'event_id',
        'hotel_id',
        'hotel_name',
        'participant_id',
        'umroh_trip_id',
        'order_umroh_trip_id',
        'package_umroh_trip_id',
        'phone_number',
        'checkin_date',
        'checkout_date',
        'room_type',
        'room_price_pax',
        'total_room',
        'total_pax',
        'additional_item',
        'additional_pax',
        'additional_cost',
        'total_amount',
        'payment_method',
        'status', //'pending', 'paid', 'booked', 'accessgiven', 'cancelled'
        'room_number',
        'access_evidence',
        'created_by',
        'updated_by',
        'deleted_by',
        'invoice_no',
        'invoice_description',
        'invoice_xendit_url',
        'receipt_url',
        'assigned_participant',
        'paid_at',
        'paid_amount',
    ];

    public function receiptUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => 
                "https://www.jejakimani.com/".$attributes['receipt_url'],
        );
    }

    public function setOrderNumber($date = null)
    {
        $additional_code = "";
        if($date) {
            if(date('Y', strtotime($date)) != date('Y')) {
                $additional_code = "-" . date('y', strtotime($date));
            }
        }
        $orderNumber = self::PREFIX_ORDER_NUMBER . date('y/') . self::MONTH_ROMAWI[date('n')] ."/". str_pad($this->getIdInThisMonth(), 5, 0, STR_PAD_LEFT);
        $this->invoice_no = $orderNumber . $additional_code ."/". strtoupper(Str::uuid()->toString());
        $this->save();
    }

    private function getIdInThisMonth()
    {
        $recordNumber = self::whereYear('created_at', date('Y'))
            ->withTrashed()
            ->count();

        return $recordNumber + 1;
    }

    public function scopeTableSearch($query)
    {
        $query->select('booking_hotel_events.*', 'participant.name', 'participant.no_hp', 'package_umroh_trips.name as package_name', 'umroh_trips.title as umroh_trip_title');
        $query->join('participant', 'participant.id', 'booking_hotel_events.participant_id');
        $query->leftjoin('package_umroh_trips', 'package_umroh_trips.id', 'booking_hotel_events.package_umroh_trip_id');
        $query->leftjoin('umroh_trips', 'umroh_trips.id', 'booking_hotel_events.umroh_trip_id');
        $search = '%' . request()->query('q') .'%';
        $query->where(function($q) use($search) {
            $q->where('hotel_name', 'like', $search)
            ->orWhere('participant.name', 'like', $search)
            ->orWhere('participant.no_hp', 'like', $search)
            ->orWhere('umroh_trips.title', 'like', $search);
        });
        if (!empty(request()->checkinDate)) {
            $dateXplode = explode('to', request()->checkinDate);
            $start = date('Y-m-d 00:00:00', strtotime($dateXplode[0]));
            $end = date('Y-m-d 24:00:00', strtotime($dateXplode[1]??$dateXplode[0]));
            $query->whereBetween('booking_hotel_events.checkin_date', [$start, $end]);
        }
        if (!empty(request()->status)) {
            $query->where('booking_hotel_events.status', request()->status);
        }
        if (!empty(request()->umrohTripId)) {
            $query->where('booking_hotel_events.umroh_trip_id', request()->umrohTripId);
        }
        if (!empty(request()->packageName)) {
            $query->where('package_umroh_trips.name', request()->packageName);
        }
        if (!empty(request()->hotel)) {
            $query->where('booking_hotel_events.hotel_name', request()->hotel);
        }
        if (!empty(request()->roomType)) {
            $query->where('booking_hotel_events.room_type', request()->roomType);
        }

        return $query;
    }

    public static function createBookingHotelEvent($request)
    {
        $event = EventAttendance::find($request['event_id']);
        $umrohTrip = UmrohTrip::find($event->umroh_trip_id);

        $request['invoice_no'] = Str::uuid();
        $request['invoice_description'] = "Pemesanan Hotel Manasik " . $umrohTrip->title;
        
        self::create($request);

        $bookingHotel = self::select('booking_hotel_events.*', 'participant.name', 'participant.no_hp')
        ->join('participant', 'participant.id', 'booking_hotel_events.participant_id')
        ->where('participant_id', $request['participant_id'])->where('event_id', $request['event_id'])->first();
        $bookingHotel->setOrderNumber();

        return self::createInvoiceXendit($bookingHotel);
    }

    public static function reCreateInvoice($request)
    {
        $bookingHotel = self::select('booking_hotel_events.*', 'participant.name', 'participant.no_hp')
        ->join('participant', 'participant.id', 'booking_hotel_events.participant_id')
        ->where('booking_hotel_events.id', $request['id'])->first();
        $bookingHotel->update([
            'invoice_no' => $bookingHotel->invoice_no . "/UPDATE" . time()
        ]);

        return self::createInvoiceXendit($bookingHotel);
    }

    private static function createInvoiceXendit($bookingHotel)
    {
        $externalId = strtoupper(str_replace("/", "_", $bookingHotel->invoice_no));
        $body = [
            'external_id' => $externalId,
            'description' => $bookingHotel->invoice_description,
            'amount' => $bookingHotel->total_amount,
            'invoice_duration' => 86400,
            'currency' => 'IDR',
            'reminder_time' => 1,
            "customer" => [
                "given_names" => $bookingHotel->name,
                "surname" => $bookingHotel->name,
                "mobile_number" => $bookingHotel->no_hp,
            ],
            "success_redirect_url" => "https://www.jejakimani.com/success-checkout-booking/" . $externalId,
            "failure_redirect_url" => "https://www.jejakimani.com",
            "items" => [
                [
                    "name" => "Booking Hotel",
                    "quantity" => 1,
                    "price" => $bookingHotel->total_amount,
                ]
            ],
        ];
        
        $result = Xendit::createInvoice($body);

        if ($result['invoice_url']) {
            $bookingHotel->update(['invoice_xendit_url' => $result['invoice_url'], 'status' => self::STATUS_PENDING]);
            SendWhatsappBookingHotelEvent::dispatch($bookingHotel);
        } else {
            throw new ErrorMessageException(json_encode($result));
        }

        return $bookingHotel;
    }
}
