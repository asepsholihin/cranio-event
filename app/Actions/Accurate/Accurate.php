<?php

namespace App\Actions\Accurate;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use App\Models\AccurateIntegration;
use App\Models\InvoiceUmrohTrip;
use App\Models\LogAccurateIntegration;
use Carbon\Carbon;

class Accurate
{
    const URL = "https://account.accurate.id/";
    const HOST = "https://zeus.accurate.id";
    
    private function __construct()
    {
        App::setLocale('id');
    }

    // private static function _getToken($credential)
    // {
    //     $timestamp = time();
	//     $signature = hash_hmac('sha256', $timestamp, $credential->signature_secret);

    //     $endpoint = self::URL . "api/api-token.do";

    //     // Header
    //     $header = [
    //         'Authorization' => \sprintf('Bearer %s', $credential->token ?? ''),
    //         'X-Api-Timestamp' => $timestamp,
    //         'X-Api-Signature' => $signature
    //     ];
    //     $response = Http::withHeaders($header)->post($endpoint);
    //     $response = $response->json();

    //     return $response['d']['database'];
    // }

    public static function checkCustomer($credential, $name)
    {
        $content = array(
            'fields' => 'id,name,bbmPin,customerNo',
            'filter.keywords.val' => $name
        );

        $timestamp = time();
	    $signature = hash_hmac('sha256', $timestamp, $credential->signature_secret);

        $endpoint = self::HOST . "/accurate/api/customer/list.do";

        // Header
        $header = [
            'Authorization' => \sprintf('Bearer %s', $credential->token ?? ''),
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature,
            'Content-Type' => 'application/json'
        ];
        $response = Http::withHeaders($header)->get($endpoint, $content);
        $response = $response->json();

        return $response;
    }

    public static function createBookingJournal($invoice_id)
    {
        $credential = AccurateIntegration::find(1);

        $invoice = InvoiceUmrohTrip::find($invoice_id);
        
        $admBank = 0;
        $accountNoDP = "211201";
        $accountNoCredit = "1149"; 

        if($invoice->invoice_url) $accountNoCredit = "1149"; 
        if($invoice->payment_method == "Cash") $accountNoCredit = "1131";
        if($invoice->payment_method == "BSI-03") $accountNoCredit = "1114";
        if($invoice->payment_method == "BSI-97") $accountNoCredit = "1120";
        if($invoice->payment_method == "Mandiri") $accountNoCredit = "1113";
        if($invoice->payment_method == "Muamalat") $accountNoCredit = "1111";

        if($invoice->invoice_url) {
            // Pengurangan Adm. Bank
            $admBank = 4440;
        }

        $itemJournal = array(
            array(
                "accountNo" => $accountNoCredit,
                "amount" => ($invoice->payment_amount - $admBank),
                "amountType" => "DEBIT",
            )
        );
        
        if($invoice->invoice_url) {
            array_push($itemJournal, array(
                "accountNo" => "8001",
                "amount" => $admBank,
                "amountType" => "DEBIT",
            ));
        }

        array_push($itemJournal, array(
            "accountNo" => $accountNoDP,
            "amount" => $invoice->payment_amount,
            "amountType" => "CREDIT",
            "subsidiaryType" => "VENDOR",
            "vendorNo" => "V.00001"
            // "customerNo" => ID PELANGGAN
        ));

        // TEMBAK API LIST CUSTOMER
        // $customers = self::checkCustomer($credential, $invoice->name);

        // TEMBAK API BUAT BIKIN JURNAL UMUM
        $content = array(
            "transDate" => date('d/m/Y'),
            "description" => $invoice->description . " - " . $invoice->invoice_no ." a/n ". $invoice->name,
            "detailJournalVoucher" => $itemJournal
        );

        $timestamp = time();
	    $signature = hash_hmac('sha256', $timestamp, $credential->signature_secret);

        $endpoint = self::HOST . "/accurate/api/journal-voucher/save.do";

        // Header
        $header = [
            'Authorization' => \sprintf('Bearer %s', $credential->token ?? ''),
            'X-Api-Timestamp' => $timestamp,
            'X-Api-Signature' => $signature,
            'Content-Type' => 'application/json'
        ];
        $response = Http::withHeaders($header)->post($endpoint, $content);
        $response = $response->json();

        $status = "";
        if($response['s']) {
            $status = "SUCCESS";
        } else {
            $status = "ERROR";
        }
        
        LogAccurateIntegration::create([
            'type' => 'Booking',
            'invoice_id' => $invoice->id,
            'invoice_no' => $invoice->invoice_no,
            'url' => $endpoint,
            'request_body' => json_encode($content),
            'response_body' => json_encode($response),
            'status' => $status,
            'message' => json_encode($response['d'])
        ]);

        return response()->json($response);
    }
}
