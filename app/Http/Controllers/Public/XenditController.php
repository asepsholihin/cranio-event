<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterEvent\SubmitRegister;
use App\Mail\EventAttandance\OpenRegistration;
use App\Models\Participant;
use App\Models\AttendanceOpenRegistration;
use App\Models\EventOpenRegistration as ModelsEventOpenRegistration;
use App\Models\EventTicketTransaction;
use App\Models\OrderUmrohTrip;
use App\Models\InvoiceUmrohTrip;
use App\Models\JiosSales;
use App\Models\UmrohTrip;
use App\Models\CampaignTransaction;
use App\Models\CampaignTransactionHistory;
use App\Models\LogCsoClosing;
use App\Models\BookingHotelEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use SnappyImage;
use Carbon\Carbon;
use App\Jobs\SendWhatsappQREventOpenRegistration;
use App\Jobs\SendWhatsappTicketEvent;
use App\Jobs\SendWhatsappBookingPaid;
use App\Jobs\SendWhatsappBookingPaidInvoice;
use App\Jobs\SendWhatsappLinkSelfInvoice;
use App\Jobs\SendWhatsappBookingPaidInvoiceAndReceipt;
use App\Jobs\SendWhatsappGeneralReceipt;
use App\Actions\Xendit\Xendit;
use DB;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use App\File\PDF\ReceiptPDF;
use App\File\PDF\InvoiceBookingSeatPDF;
use App\File\PDF\ReceiptAndInvoicePDF;
use App\File\PDF\ReceiptGeneralPDF;
use App\Actions\Accurate\Accurate;

class XenditController extends Controller
{
    public function callback(Request $request)
    {
        if(Str::contains($request->external_id, "JIBB_INV_DONATION")) {
            return $this->donationOrderCallback($request);
        } else if(Str::contains($request->external_id, "JIBB_INV_MANASIK")) {
            return $this->hotelBookingCallback($request);
        } else if(Str::contains($request->external_id, "JIBB_INV")) {
            return $this->bookingOrderCallback($request);
        } else if(Str::contains($request->external_id, "INV-JIOS")) {
            return $this->jiosOrderCallback($request);
        } else {
            return $this->bookingEventCallback($request);
        }
    }

    public function bookingOrderCallback($request)
    {
        $externalId = strtoupper(str_replace("_","/",$request->external_id));
        $invoice = InvoiceUmrohTrip::where('invoice_no', $externalId)->first();
        $order = OrderUmrohTrip::find($invoice->order_umroh_trip_id);
        if(!$invoice) {
            return response()->json([
                'success' => false,
                'message'  => 'Transaksi tidak tersedia',
            ], 422);
        }

        DB::transaction(function() use($order, $request, $invoice) {

            $log = DB::table('log_booking_umroh_trip_transactions')->where('order_umroh_trip_id', $order->id)->where('invoice_umroh_trip_id', $invoice->id)->first();

            DB::table('log_booking_umroh_trip_transactions')->insert([
                'order_umroh_trip_id' => $log->order_umroh_trip_id,
                'invoice_umroh_trip_id' => $log->invoice_umroh_trip_id,
                'category' => $log->category,
                'transaction_id' => $log->transaction_id,
                'transaction_status' => $request->status,
                'payment_information' => json_encode($request->all()),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            if($request->status == "PAID") {
                $invoice->update([
                    'status' => InvoiceUmrohTrip::STATUS_PAID,
                    'credit_status' => 0,
                    'due_payment' => 0,
                    'payment_method' => $request->payment_method,
                    'payment_amount' => $request->paid_amount,
                    'payment_date' => Carbon::parse($request->paid_at)->timezone('Asia/Jakarta'),
                    'payment_created_at' => Carbon::parse($request->paid_at)->timezone('Asia/Jakarta'),
                    'payment_note' => 'Payment with Xendit'
                ]);
    
                $order->calculateOrderPayment();
                UmrohTrip::updateTakenSeat($order->umroh_trip_id);
                
                $fileName = "kwitansi_dan_invoice_".str_replace("/", "_", $invoice->invoice_no)."_".Carbon::now()->timestamp.".pdf";
                $receiptInvoicePDF = (new ReceiptAndInvoicePDF($order, $invoice));
                $storageKey = InvoiceUmrohTrip::DIR_RECEIPT . "/{$fileName}";
                Storage::put($storageKey, $receiptInvoicePDF->output(), 'r');
                $invoice->invoice_and_receipt_url = $storageKey;

                $invoice->save();

                SendWhatsappBookingPaidInvoiceAndReceipt::dispatch($order, $invoice)->delay(Carbon::now()->addSeconds(300));
                if(!$order->is_badal && $order->due_payment > 0) {
                    SendWhatsappLinkSelfInvoice::dispatch($order)->delay(Carbon::now()->addSeconds(600));    
                }

                LogCsoClosing::insertLog($order, $invoice);
                try {
                    Accurate::createBookingJournal($invoice->id);
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                }
            }
            if($request->status == "EXPIRED") {
                $invoice->update([
                    'status' => InvoiceUmrohTrip::STATUS_EXPIRED,
                    'payment_note' => 'Payment with Xendit'
                ]);

                $firstInvoice = InvoiceUmrohTrip::select('id')->where('order_umroh_trip_id', $order->id)->orderBy('id', 'ASC')->first();
                if($firstInvoice) {
                    if($firstInvoice->id == $invoice->id) {
                        if($order->paid == 0) {
                            OrderUmrohTrip::destroy($order);
                        }
                    }
                }
            }
        });

        return response()->json([
            'success' => true,
            'message'  => 'Jazakumullah khairan'
        ], 200);
    }

    public function bookingEventCallback($request)
    {
        $transaction = EventTicketTransaction::where('transaction_id', $request->external_id)->first();
        if(!$transaction) {
            return response()->json([
                'success' => false,
                'message'  => 'Transaksi tidak tersedia',
            ], 422);
        }

        DB::transaction(function() use($transaction, $request) {
            DB::table('log_event_ticket_transactions')->insert([
                'transaction_id' => $transaction->id,
                'transaction_status' => $request->status,
                'payment_information' => json_encode($request->all()),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
    
            $transaction->update([
                'send_email_invoice' => 1,
                'transaction_status' => $request->status,
                'payment_method' => $request->payment_method
            ]);

            if($request->status == "PAID") {
                $barcode = Str::uuid()->toString();
                $checkBarcodeExist = AttendanceOpenRegistration::where('barcode', $barcode)->first();
                if($checkBarcodeExist) {
                    $barcode = Str::uuid()->toString();
                }

                $participant = AttendanceOpenRegistration::updateOrCreate(
                [
                    'event_open_registration_id' => $transaction->event_id,
                    'no_hp' => $transaction->no_hp,
                ],
                [
                    'event_open_registration_id' => $transaction->event_id,
                    'barcode' => $barcode,
                    'name' => $transaction->name,
                    'no_hp' => $transaction->no_hp,
                    'is_alumni' => $transaction->is_alumni,
                    'pax' => $transaction->pax,
                    'last_umroh_trip' => $transaction->last_umroh_trip,
                    'notes' => $transaction->notes,
                    'pax_ikhwan' => $transaction->pax_ikhwan,
                    'pax_akhwat' => $transaction->pax_akhwat
                ]);
        
                $fileName = Str::slug($participant->name, '_') . "_barcode_" .Carbon::now()->timestamp. ".jpg";
        
                $img = SnappyImage::setOption('width', 100)->loadView('barcode.participant', compact('participants'));
                $img->save(storage_path('app/'.$fileName));
                $storageKey = AttendanceOpenRegistration::S3_PATH_BARCODE . "/{$fileName}";
                Storage::put($storageKey, fopen(storage_path('app/'.$fileName), 'r'));
                
                $participant->barcode_thumbnail = $storageKey;
                $participant->save();

                // $event = ModelsEventOpenRegistration::find($transaction->event_id);
                // SendWhatsappTicketEvent::dispatch($event, $transaction->pax, $transaction->no_hp, $transaction->name, $participant->barcode_thumbnail);

                $transaction->update([
                    'send_ticket' => 1,
                ]);
            }
    
        });

        return response()->json([
            'success' => true,
            'message'  => 'Jazakumullah khairan'
        ], 200);
    }

    public function jiosOrderCallback($request)
    {
        $externalId = strtoupper(str_replace("_","/",$request->external_id));
        $jios = JiosSales::where('invoice_number', $externalId)->first();
        if(!$jios) {
            return response()->json([
                'success' => false,
                'message'  => 'Transaksi tidak tersedia',
            ], 422);
        }

        DB::transaction(function() use($jios, $request) {

            $log = DB::table('log_jios_sales_transactions')->where('jios_sale_id', $jios->id)->first();

            DB::table('log_jios_sales_transactions')->insert([
                'jios_sale_id' => $log->jios_sale_id,
                'transaction_id' => $log->transaction_id,
                'transaction_status' => $request->status,
                'payment_information' => json_encode($request->all()),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            if($request->status == "PAID") {
                $jios->update([
                    'sales_status' => JiosSales::SALES_STATUS_PAID,
                    'paid_amount' => $request->paid_amount,
                    'given_amount' => $request->paid_amount,
                    'payment_method' => $request->payment_method,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message'  => 'Jazakumullah khairan'
        ], 200);
    }

    public function donationOrderCallback($request)
    {
        $externalId = strtoupper(str_replace("_","/",$request->external_id));
        $transaction = CampaignTransaction::where('invoice_no', $externalId)->first();
        if(!$transaction) {
            return response()->json([
                'success' => false,
                'message'  => 'Transaksi tidak tersedia',
            ], 422);
        }

        DB::transaction(function() use($transaction, $request) {

            CampaignTransactionHistory::create([
                'campaign_id' => $transaction->campaign_id,
                'campaign_transaction_id' => $transaction->id,
                'status' => $request->status,
                'information' => json_encode($request->all())
            ]);

            if($request->status == "PAID") {
                $transaction->update([
                    'status' => CampaignTransaction::STATUS_PAID
                ]);

                CampaignTransaction::calculateDonation($transaction->campaign_id);
            }
            if($request->status == "EXPIRED") {
                $invoice->update([
                    'status' => CampaignTransaction::STATUS_EXPIRED
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message'  => 'Jazakumullah khairan'
        ], 200);
    }

    public function hotelBookingCallback($request)
    {
        $externalId = strtoupper(str_replace("_","/",$request->external_id));
        $bookingHotel = BookingHotelEvent::select('booking_hotel_events.*','participant.name','participant.no_hp')->join('participants','participant.id', 'booking_hotel_events.participant_id')->where('invoice_no', $externalId)->first();
        if(!$bookingHotel) {
            return response()->json([
                'success' => false,
                'message'  => 'Transaksi tidak tersedia',
            ], 422);
        }

        DB::transaction(function() use($bookingHotel, $request) {
            if($request->status == "PAID") {
                $bookingHotel->update([
                    'status' => BookingHotelEvent::STATUS_PAID,
                    'paid_at' => Carbon::parse($request->paid_at)->timezone('Asia/Jakarta'),
                    'paid_amount' => $request->paid_amount
                ]);

                $fileName = "kwitansi_".str_replace("/", "_", $bookingHotel->invoice_no)."_".Carbon::now()->timestamp.".pdf";
                $receiptInvoicePDF = (new ReceiptGeneralPDF($bookingHotel));
                $storageKey = BookingHotelEvent::DIR_RECEIPT . "/{$fileName}";
                Storage::put($storageKey, $receiptInvoicePDF->output(), 'r');
                $bookingHotel->receipt_url = $storageKey;

                $bookingHotel->save();

                SendWhatsappGeneralReceipt::dispatch($bookingHotel)->delay(Carbon::now()->addSeconds(300));
            }
            if($request->status == "EXPIRED") {
                $bookingHotel->update([
                    'status' => BookingHotelEvent::STATUS_PAYMENT_EXPIRED
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message'  => 'Jazakumullah khairan'
        ], 200);
    }
}
