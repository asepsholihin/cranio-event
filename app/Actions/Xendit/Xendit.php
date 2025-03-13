<?php

namespace App\Actions\Xendit;

use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\XenditSdkException;
use Illuminate\Http\Request;

class Xendit
{
    const URL_ENDPOINT = "https://api.xendit.co/v2/";
    
    public static function createInvoice($body)
    {
        Configuration::setXenditKey(\config('xendit.api_key'));
        $apiInstance = new InvoiceApi();

        $create_invoice_request = new CreateInvoiceRequest($body);
        $for_user_id = null;
        
        try {
            $result = $apiInstance->createInvoice($create_invoice_request, $for_user_id);
            return $result;
        } catch (XenditSdkException $e) {
            return $e->getFullError();
            // echo 'Exception when calling InvoiceApi->createInvoice: ', $e->getMessage(), PHP_EOL;
            // echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
        }
    }
}
