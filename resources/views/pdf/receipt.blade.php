<?php
use App\Support\NumberFormat;
use Illuminate\Support\Facades\DB;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">
        @font-face {
            font-family: 'Mulish';
            src: url({{ storage_path('private_assets/fonts/Mulish-Regular.ttf') }}) format("truetype");
        }
         body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            font: 9pt "Mulish";
        }
        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }
        .page {
            position: relative;
            background-color: #ffffff;
        }
        .letter-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }
        .letter-bg img {
            width: 210mm;
            min-height: 297mm;
        }
        .subpage {
            position: relative;
            padding-top: 6.2cm;
            padding-left: 10px;
            height: 235mm;
            z-index: 999;
        }

        .right {
            text-align: right;
        }
        .left {
            text-align: left;
        }
        .center {
            text-align: center;
        }
        .justify {
            text-align: justify;
        }
        .mt {
            margin-top: 12px;
        }
        .mt-5 {
            margin-top: 2.4em;
        }
        .mt-3 {
            margin-top: 1.2em;
        }
        .mb-0 {
            margin-bottom: 0 !important;
        }
        h1,h2,h3,h4,h5,p {
            margin-top: 0;
            margin-bottom: 8px;
        }
        .underline {
            text-decoration: underline;
        }
        .fw-bold {
            font-weight: bold;
        }
        .uppercase {
            text-transform: uppercase;
        }
        .text-nowrap {
            text-wrap: nowrap;
            white-space: nowrap;
        }

        @page {
            size: A4;
            margin: 0;
        }
        @media print {
            html, body {
                width: 210mm;
                height: 297mm;
            }
            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
        }
    </style>
    <title>Receipt</title>
</head>
<body>
    @php
    $packages = "";
    if($order->is_badal) {
        $category = 1;
        $title = "Badal";
        $badal = DB::table('badal_umroh_trips')->where('order_umroh_trip_id', $order->id)->first();
        $packages = $badal->badal_package;
        $participant_badal = json_decode($badal->participant_badal);
        $totalPax = $badal->pax;
    } else {
        $countPackages = count($orderItems);
        foreach($orderItems as $key => $item) {
            $packages .= $item->name;
            if($key == $countPackages - 2) {
                $packages .= " and ";    
            } else {
                $packages .= ", ";    
            }
        }
        $packages = rtrim($packages, ', ');
        $category = $umrohTrip->category_id;
        $title = $umrohTrip->title;
    }
    @endphp
    <div class="book">
        <div class="page">
            <div class="letter-bg">
                @if($invoice->invoice_url != null)
                    <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_receipt_alt.jpg')))}}" alt="">
                @else
                    @if ($category == 2)
                        <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_receipt_usd_.jpg')))}}" alt="">
                    @else
                        @if($currency == 'USD')
                        <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_receipt_usd.jpg')))}}" alt="">
                        @else
                        <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_receipt.jpg')))}}" alt="">
                        @endif
                    @endif
                @endif
            </div>
            <div class="subpage">
                <br/>
                <div style="margin-bottom:12px;margin-left:150px;font-weight:bold">Bpk/Ibu {{$order->name}}</div>
                <div style="margin-bottom:12px;margin-left:150px;">{{str_replace('INV', 'KWT', $invoice->invoice_no)}}</div>
                <div style="margin-bottom:6px;margin-left:150px;">{{\Carbon\Carbon::parse($invoice->created_at)->isoFormat('D MMMM Y')}}</div>
                <div style="margin-bottom:6px;margin-left:150px;">@if($sales) {{ $sales->whatsapp_number }} ({{ $sales->sales_name }}) @endif</div><br/><br/><br/><br/>
                <table width="100%">
                    <tr>
                        <td width="350" class="justify">
                            @if($order->is_badal)
                                Tanggal {{\Carbon\Carbon::parse($invoice->payment_date)->isoFormat('D MMMM Y')}} telah diterima <b>Pembayaran {{$invoice->description}}</b> {{$packages}} sebesar {{$currency}} {{number_format($invoice->payment_amount)}},-
                                <br>
                                @foreach($participant_badal as $row)
                                - {{ $row->name_in_badal }}<br>
                                @endforeach
                            @else
                                Tanggal {{\Carbon\Carbon::parse($invoice->payment_date)->isoFormat('D MMMM Y')}} telah diterima <b>Pembayaran {{$invoice->description}}</b> Paket {{$packages}} {{$title}} sebesar 
                                @if($invoice->usd_convertion)
                                    IDR {{number_format($invoice->payment_amount)}},-
                                @else
                                    {{$currency}} {{number_format($invoice->payment_amount)}},-
                                @endif
                            @endif
                        </td>
                        <td width="65"></td>
                        <td width="185" style="vertical-align: top; text-align: center">{{$totalPax}} pax</td>
                        <td class="text-nowrap" style="vertical-align: top; text-align: center">
                            @if($invoice->usd_convertion)
                                IDR {{number_format($invoice->payment_amount)}},-
                            @else
                                {{$currency}} {{number_format($invoice->payment_amount)}},-
                            @endif
                        </td>
                    </tr>
                </table>
                <br/><br/><br/>
                <div style="padding-left: 5px" >
                    @if($invoice->usd_convertion)
                        <b><i>Terbilang: "{{NumberFormat::terbilang($invoice->payment_amount)}} Rupiah"</i></b>
                    @else
                        <b><i>Terbilang: "{{NumberFormat::terbilang($invoice->payment_amount)}} {{NumberFormat::currencyToString($currency)}}"</i></b>
                    @endif
                    <br/><br/><br/><br/>
                    <br/>
                    <i><p>Semoga Bapak/Ibu dan keluarga selalu diberikan Kesehatan,</p>
                    <p>keberkahan dalam kesehariannya, dan dilancarkan rizkinya,</p>
                    <p>Aamiin.</p></i><br/>
                    <b>*DP Non-Refundable (down payment tidak dapat dikembalikan)</b>
                </div>
                <span class="text-nowrap" style="font-weight:bold; position:absolute; top:738px; right:20px; font-size:12pt;">
                    @if($invoice->usd_convertion)
                        IDR {{number_format($invoice->payment_amount)}},-
                    @else
                        {{$currency}} {{number_format($invoice->payment_amount)}},-
                    @endif
                </span>
                <!-- <img width="100" style="position:absolute; top:848px; right:100px;" src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/signature_invoice.jpg'))) }}"> -->
                <!-- <span style="font-weight:bold; position:absolute; top:950px; right:65px; font-size:9pt; background-color:#fff">Firda Ayuningtyas, S.E.</span> -->
            </div>
        </div>
    </div>
</body>
</html>
