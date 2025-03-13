<?php
use App\Support\NumberFormat;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">
        @font-face {
            font-family: 'Mulish-Bold';
            src: url({{ storage_path('private_assets/fonts/Mulish-Bold.ttf') }}) format("truetype");
        }
        
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            font-family: 'Mulish-Bold';
            font-size: 14px;
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
            width: 100%;
            max-height: 3cm;
        }
        .subpage {
            position: relative;
            padding-top: 2.5cm;
            padding-left: 0.5cm;
            padding-right: 0.5cm;
            height: 100mm;
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

        table.table tr td {
            padding: 4px 0;
        }
        
        @page {
            size: A6;
            margin: 0;
        }
        @media print {
            html, body {
                width: 100mm;
                height: 100mm;
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
        .card {
            position: relative;
        }
        .order-no {
            position: absolute;
            top: 0;
            right: 0;
        }
    </style>
    <title>Shipment Label - {{ strtoupper($participant->recipient ?? $participant->name) }}</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="letter-bg">
                <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_shipment_label.png')))}}" alt="">
            </div>
            <div class="subpage">
                <p><strong>Kepada Yth:</strong></p>
                <p><strong>{{ strtoupper($participant->recipient ?? $participant->name) }}</strong> ({{$index}})</p>

                <table class="table" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td valign="top" width="18%">ISI</td>
                        <td valign="top" width="2%">:</td>
                        <td valign="top" width="80%">SET PERLENGKAPAN {{ strtoupper($participant->package_name) }} <strong>({{ strtoupper($participant->name) }})</strong></td>
                    </tr>
                    <tr>
                        <td valign="top">ALAMAT</td>
                        <td valign="top">:</td>
                        <td valign="top">{{ strtoupper($participant->address) }}</td>
                    </tr>
                    <tr>
                        <td valign="top">NO. HP</td>
                        <td valign="top">:</td>
                        <td valign="top">{{ strtoupper($participant->recipient_phone_number ?? $participant->no_hp) }}</td>
                    </tr>
                    <tr>
                        <td valign="bottom">TANGGAL KEBERANGKATAN</td>
                        <td valign="bottom">:</td>
                        <td valign="bottom">{{ strtoupper(\Carbon\Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y')) }}</td>
                    </tr>
                </table>

                <br/>

                <p><strong>Dari:</strong></p>
                <p class="mb-0"><strong>PT. Jejak Imani Berkah Bersama</strong></p>
                <p class="mb-0">Intermark Indonesia Ruko 9 & 10, Jalan Lingkar Timur No. 9 BSD Kota Tangerang Selatan, Banten 15310</p>
                <p class="mb-0"><strong>081 1917 8100</strong> | <strong>www.jejakimani.com</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
