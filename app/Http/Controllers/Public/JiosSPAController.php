<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use App\Models\Participant;
use App\Models\JiosSales;
use App\Models\UmrohTrip;
use App\Models\ParticipantUmrohTrip;
use App\Models\JiosSalesDetail;
use Illuminate\Http\Request;
use App\Http\Requests\StoreItemRequest;
use App\Exceptions\ErrorMessageException;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DB;
use App\Jobs\SendWhatsappOrderJios;
use App\Actions\Xendit\Xendit;

class JiosSPAController extends Controller
{
    const SPA_PATH = '/jios';

    public function __construct()
    {
        
    }
    
    public function products(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = Item::select(['id','category_id','name','unit_id','unit_price','unit_price_riyal','photo'])
            ->where('name', 'like', $search)
            ->orWhere('code', 'like', $search)
            ->paginate(10);
        return response()->json($result);
    }
    
    public function categories(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = Category::select(['id','name'])
            ->where('name', 'like', $search)
            ->get();
        return response()->json($result);
    }

    public function checkJios(Request $request)
    {
        $umrohTrip = UmrohTrip::where('slug', $request->trip)->first();
        return response()->json($umrohTrip);
    }

    public function checkout(Request $request)
    {
        if (empty($request->no_hp)) {
            return response()->json([
                'success' => false,
                'message'  => 'Harap memasukkan Nomor HP yang terdaftar saat booking',
            ], 422);
        }
        if (empty($request->payment_method)) {
            return response()->json([
                'success' => false,
                'message'  => 'Harap pilih metode pembayaran',
            ], 422);
        }

        $phoneNumber = $request->no_hp;
        if (Str::startsWith($phoneNumber, '0')) {
            $phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $phoneNumber);
        }
        $originPhone = substr($phoneNumber, 2);

        $participant = Participant::select('participant.id', 'participant.name', 'participant.no_hp', 'participant_umroh_trips.umroh_trip_id', 'participant_umroh_trips.group_bus')->join('participant_umroh_trips', 'participant.id', 'participant_umroh_trips.participant_id')->where('no_hp', 'like', '%' . $originPhone . '')->orderBy('participant_umroh_trips.id', 'desc')->first();
        if (empty($participant)) {
            return response()->json([
                'success' => false,
                'message'  => 'Nomor HP yang tidak terdaftar',
            ], 422);
        }

        $jiosSales = DB::transaction(function () use($request,$participant) {
            $umrohTrip = UmrohTrip::find($participant->umroh_trip_id);
            $mutawwif = ParticipantUmrohTrip::select('participant.id','participant.name')->join('participants', 'participant.id', 'participant_umroh_trips.participant_id')->where('role_type', ParticipantUmrohTrip::ROLE_TYPE_MUTAWWIF)->where('group_bus', $participant->group_bus)->where('umroh_trip_id', $umrohTrip->id)->first();
            if($mutawwif) {
                $mutawwifId = $mutawwif->id;
            } else {
                $mutawwifId = Participant::where('id', $umrohTrip->mutawwif)->first()->id ?? 0;
            }

            $invoiceNumber = JiosSales::generateCode();

            $params = [
                'umroh_trip_id' => $umrohTrip->id,
                'invoice_number' => $invoiceNumber,
                'participant_id' => $participant->id,
                'mutawwif_id' => $mutawwifId ?? 0,
                'total_item' => 0,
                'total_qty' => 0,
                'total_amount' => 0,
                'paid_amount' => 0,
                'payment_method' => request()->payment_method,
                'sales_status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'notes' => 'Checkout from website',
                'given_amount' => 0,
                'change_amount' => 0
            ];

            $jiosSales = JiosSales::create($params);

            $totalAmount = 0;
            $totalAmountRiyal = 0;
            $totalItem = 0;
            $totalQty = 0;
            $xenditItems = array();
            foreach (json_decode($request->carts) as $key => $row) {
                $itemDetail = Item::select('id','unit_price','unit_price_riyal')->where('id', $row->id)->first();
                if ($row->qty == "0" || $row->qty == 0) {
                    throw new ErrorMessageException('Qty tidak boleh kosong');
                }

                $totalItem += 1;
                $totalQty += $row->qty;
                $totalAmount += ($row->qty * $itemDetail->unit_price);
                $totalAmountRiyal += ($row->qty * $itemDetail->unit_price_riyal);
                
                $discountPerItem = 0;
                $totalPrice = ($row->qty * $itemDetail->unit_price);
                DB::table('jios_sales_details')->insert([
                    'jios_sale_id' => $jiosSales->id,
                    'item_id' => $row->id,
                    'qty' => $row->qty,
                    'price_per_item' => $itemDetail->unit_price,
                    'total_price' => $totalPrice,
                    'discount_per_item' => $discountPerItem,
                    'price_after_discount' => ($itemDetail->unit_price - $discountPerItem),
                    'total_price_after_discount' => ($totalPrice - $discountPerItem)
                ]);

                $xenditItems[] = array(
                    "name" => $row->name,
                    "quantity" => $row->qty,
                    "price" => $itemDetail->unit_price,
                    "category" => "JIOS"
                );
            }

            $externalId = strtoupper(str_replace("/","_",$invoiceNumber));
            if(request()->payment_method == "NON CASH") {
                $xenditBody = [
                    'external_id' => $externalId,
                    'description' => 'Oleh-oleh',
                    'amount' => $totalAmount,
                    'invoice_duration' => 86400,
                    'currency' => 'IDR',
                    'reminder_time' => 1,
                    "customer" => [
                        "given_names" => $participant->name,
                        "surname" => $participant->name,
                        "mobile_number" => $participant->no_hp,
                    ],
                    "success_redirect_url" => "https://www.jejakimani.com/success-checkout/" . $externalId,
                    "failure_redirect_url" => "https://www.jejakimani.com",
                    "items" => $xenditItems,
                ];
                $result = Xendit::createInvoice($xenditBody);

                if($result['invoice_url']){
                    $jiosSales->update(['invoice_url' => $result['invoice_url']]);
                
                    DB::table('log_jios_sales_transactions')->insert([
                        'jios_sale_id' => $jiosSales->id,
                        'transaction_id' => $externalId,
                        'transaction_status' => "WAITING PAYMENT",
                        'payment_information' => $result,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

                    // SendWhatsappOrderJios::dispatch($orderUmrohTrip, $invoiceUmrohTrip);
                } else {
                    throw new ErrorMessageException(json_encode($result));
                }
            } 
            
            if(request()->payment_method == "CASH") {
                $jiosSales->update([
                    'sales_status' => 2,
                    'given_amount' => $totalAmountRiyal,
                    'paid_amount' => $totalAmount,
                    'paid_amount_riyal' => $totalAmountRiyal
                ]);
            }

            $jiosSales->update([
                'total_item' => $totalItem,
                'total_qty' => $totalQty,
                'total_amount' => $totalAmount,
                'total_amount_riyal' => $totalAmountRiyal
            ]);

            return $externalId;
        });

        return response()->json([
            'success' => true,
            'message'  => 'Berhasil checkout',
            'transactionId' => $jiosSales,
        ]);
    }

    public function invoiceDetail($transactionId)
    {
        $externalId = strtoupper(str_replace("_","/",$transactionId));
        $transaction = JiosSales::where('invoice_number', $externalId)->orderBy('id', 'DESC')->first();
        if(!$transaction) {
            return response()->json([
                'success' => false,
                'message'  => 'Transaksi tidak tersedia',
            ], 422);
        }

        return response()->json($transaction);
    }
}
