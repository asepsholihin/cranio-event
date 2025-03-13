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
    <div class="book">
        <div class="page">
            <div class="letter-bg">
                <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_receipt_alt.jpg')))}}" alt="">
            </div>
            <div class="subpage">
                <br/>
                <div style="margin-bottom:12px;margin-left:150px;font-weight:bold">Bpk/Ibu {{$order->name}}</div>
                <div style="margin-bottom:12px;margin-left:150px;">{{str_replace('INV', 'KWT', $order->invoice_no)}}</div>
                <div style="margin-bottom:6px;margin-left:150px;">{{\Carbon\Carbon::parse($order->paid_at)->isoFormat('D MMMM Y')}}</div><br/><br/><br/><br/><br/>
                <table width="100%">
                    <tr>
                        <td width="350" class="justify">
                        Tanggal {{\Carbon\Carbon::parse($order->paid_at)->isoFormat('D MMMM Y')}} telah diterima <b>Pembayaran {{ $order->invoice_description }}</b> sebesar {{$currency}} {{number_format($order->total_amount)}},-
                        </td>
                        <td width="65"></td>
                        <td width="185" style="vertical-align: top; text-align: center">1</td>
                        <td class="text-nowrap" style="vertical-align: top; text-align: center">
                            {{$currency}} {{number_format($order->total_amount)}},-
                        </td>
                    </tr>
                </table>
                <br/><br/><br/>
                <div style="padding-left: 5px" >
                    <b><i>Terbilang: "{{NumberFormat::terbilang($order->total_amount)}} {{NumberFormat::currencyToString($currency)}}"</i></b>
                    <br/><br/><br/><br/>
                    <br/>
                    <i><p>Semoga Bapak/Ibu dan keluarga selalu diberikan Kesehatan,</p>
                    <p>keberkahan dalam kesehariannya, dan dilancarkan rizkinya,</p>
                    <p>Aamiin.</p></i><br/>
                </div>
                <span class="text-nowrap" style="font-weight:bold; position:absolute; top:738px; right:20px; font-size:12pt;">
                    {{$currency}} {{number_format($order->total_amount)}},-
                </span>
            </div>
        </div>
    </div>
</body>
</html>
