<?php

namespace App\Http\Controllers;

use App\Models\Ticketing;
use App\Models\Transaction;
use App\Models\Journals;
use App\Models\Currency;
use App\Models\OperationalUmrohTripItem;
use App\Http\Requests\StoreSocialMediaRequest;
use App\Http\Requests\UpdateSocialMediaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class TransactionSPAController extends Controller
{
    const SPA_PATH = '/transaction';

    public function __construct()
    {
        $this->middleware('permission:payment-approval-add-payment|ticketing-payment-history-add-or-edit|operational-umroh-trip-add-or-edit')->only(['store']);
    }

    public function store(Request $request){
        if($request->currency) {
            $request->merge([
                'currency_id' => Currency::where('symbol', $request->currency)->first()->id ?? 0
            ]);
        }
        $transaction = Transaction::createTransaction($request, $request->type, $request->page);
        // Journals::createJournals($transaction);

        // TICKETING
        if($request->page == 1){
            $ticket = Ticketing::find($request->ticket_id);
            $ticket->paid_amount = $request->amount;
            $ticket->due_amount = $ticket->due_amount - $request->amount;
            // DEPO DONE
            if($ticket->due_amount != $ticket->paid_amount){
                if($ticket->due_amount != 0){
                    $ticket->status = 2;
                }
            }
            // FULLY PAID
            if($ticket->due_amount == 0){
                $ticket->status = 3;
            }
            $ticket->save();
        }
        return response()->json($transaction);
    }

    public function destroy(Transaction $transaction)
    {
        DB::transaction(function() use($transaction) {
            $transaction->delete();
            OperationalUmrohTripItem::updateTransaction($transaction->reference);
        });
    }

    public function getListTransaction(){
        $query = Transaction::select([
            'transactions.*', 'master_currencies.symbol as currency_symbol', 'origin_currencies.symbol as origin_currency_symbol', 'umroh_trips.title as umroh_trip_title', 
            'users.name as created_by_name', 'budgeting_categories.name as budget_category_name'
        ])
        ->join('master_currencies', 'master_currencies.id', 'transactions.currency_id')
        ->leftjoin('master_currencies as origin_currencies', 'origin_currencies.id', 'transactions.origin_currency_id')
        ->join('users', 'users.id', 'transactions.created_by')
        ->leftjoin('umroh_trips', 'umroh_trips.id', 'transactions.umroh_trip_id')
        ->leftjoin('budgeting_categories', 'budgeting_categories.id', 'transactions.budget_category');
        if(!empty(request()->query('reference'))){
            $query->where('reference', request()->query('reference'));
        }
        if(!empty(request()->query('page'))){
            $query->where('transaction_page', request()->query('page'));
        }
        if(!empty(request()->query('umroh_trip_id'))){
            $query->where('umroh_trip_id', request()->query('umroh_trip_id'));
        }
        $data = $query->get();
        return response()->json($data);
    }
}
