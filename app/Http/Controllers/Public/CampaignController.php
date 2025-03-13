<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignTransaction;
use App\Models\CampaignTransactionHistory;
use App\Models\Participant;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Actions\Xendit\Xendit;
use DB;

class CampaignController extends Controller
{
    public function __construct()
    {

    }

    public function show($slug)
    {
        $campaign = Campaign::select(['*'])
        ->where('slug', $slug)->first();
        if(!$campaign) {
            return response()->json([
                'success' => false,
                'message'  => 'Campaign tidak tersedia',
            ], 422);
        }

        $campaign->due_date_formated = Carbon::parse($campaign->target_date)->diffForHumans();
        $campaign->donations = CampaignTransaction::select('donatur','amount','description')->where('campaign_id', $campaign->id)->where('status', 2)->orderBy('id', 'desc')->get();

        return response()->json($campaign->toArray());
    }

    public function donate(Request $request)
    {
        $phoneNumber = $request->no_hp;

        if(!isset(request()->nominal)) {
            return response()->json([
                'success' => false,
                'message'  => 'Harap masukkan donasi anda',
            ], 422);
        }
        
        if(request()->nominal < 10000) {
            return response()->json([
                'success' => false,
                'message'  => 'Mohon maaf, donasi harus lebih dari Rp 10.000',
            ], 422);
        }

        if(!is_numeric($phoneNumber)) {
            return response()->json([
                'success' => false,
                'message'  => 'Nomor HP anda tidak valid',
            ], 422);
        }
        
        if (Str::startsWith($phoneNumber, '0')) {
            $phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $phoneNumber);
        } else {
            if(!request()->update) {
                return response()->json([
                    'success' => false,
                    'message'  => 'Nomor HP anda tidak valid',
                ], 422);
            }
        }

        $campaign = Campaign::find($request->id);
        
        $transaction = DB::transaction(function() use ($campaign, $phoneNumber) {
            $donaturName = request()->name;
            if(request()->hamba_Allah) {
                $donaturName = "Hamba Allah";
            }
            
            $transaction = CampaignTransaction::create([
                'campaign_id' => $campaign->id,
                'invoice_no' => Str::uuid()->toString(),
                'description' => request()->message,
                'donatur' => $donaturName,
                'no_hp' => $phoneNumber,
                'email' => request()->email ?? null,
                'amount' => request()->nominal,
                'status' => 1
            ]);
            $transaction->setOrderNumber();

            // Xendit
            $externalId = strtoupper(str_replace("/","_",$transaction->invoice_no));
            $body = [
                'external_id' => $externalId,
                'description' => $campaign->title .' - Invoice ' . $transaction->invoice_no,
                'amount' => $transaction->amount,
                'invoice_duration' => 86400,
                'currency' => 'IDR',
                'reminder_time' => 1,
                "customer" => [
                    "given_names" => $transaction->donatur,
                    "surname" => $transaction->donatur,
                    "mobile_number" => $transaction->no_hp,
                ],
                "success_redirect_url" => "https://www.jejakimani.com/success-checkout-booking/" . $externalId,
                "failure_redirect_url" => "https://www.jejakimani.com",
                "items" => [
                    [
                        "name" => "DONASI - " . $campaign->title,
                        "quantity" => 1,
                        "price" => $transaction->amount,
                    ]
                ],
            ];
            try {
                $result = Xendit::createInvoice($body);
                if($result['invoice_url']){
                    $transaction->update(['invoice_url' => $result['invoice_url']]);
                
                    CampaignTransactionHistory::create([
                        'campaign_id' => $campaign->id,
                        'campaign_transaction_id' => $transaction->id,
                        'status' => "WAITING PAYMENT",
                        'information' => $result
                    ]);

                } else {
                    throw new ErrorMessageException(json_encode($result));
                }
            } catch (\Throwable $th) {
                throw new ErrorMessageException(json_encode($result));
            }

            return $transaction;
        });

        return response()->json([
            'success' => true,
            'invoice_url' => $transaction->invoice_url,
            'message'  => 'Jazakumullah khairan, berhasil dikonfirmasi'
        ], 200);
    }
}
