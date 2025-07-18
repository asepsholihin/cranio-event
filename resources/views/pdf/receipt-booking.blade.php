<?php
use App\Support\NumberFormat;
use Carbon\Carbon;

$nomor = str_replace('Inv', 'KW', $booking->booking_no);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .right {
            text-align: right;
        }

        .signature {
            width: 400px;
            text-align: center;
            font-size: 12pt;
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

        h1,
        h2,
        h3,
        h4,
        h5,
        p {
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

        a {
            color: black;
            text-decoration: none;
        }

        table tr.header>td {
            padding: 10px 4px;
            text-align: center;
        }

        table,
        td,
        th {
            padding: 10px;
            border-collapse: collapse;
        }

        table.no-padding td {
            padding: 4px;
        }

        table tr.due-payment {
            /* background-color: #f3e45d; */
        }

        table.border-0,
        table.border-0 td,
        table.border-0 th {
            border: none;
        }
        table.border-bottom td {
            border-bottom: 1px solid #000;
        }
        .text-nowrap {
            text-wrap: nowrap;
            white-space: nowrap;
        }

        @page {
            margin: 30px 40px;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
        }

        @media print {
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
        .title-doc {
            letter-spacing: 3px;
        }
    </style>
    <title>KWITANSI {{ $nomor }}</title>
</head>

<body>
    <div class="page">
        <div class="center">
            <img height="130"
            src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/kop-surat.jpg'))) }}"
            alt="">
        </div>
        <br />
        <h2 class="center underline title-doc">KWITANSI</h2>
        <p class="center"><b>Nomor:</b> {{ $nomor }}</p>
        <br />
        <br />
        <div style="margin: 0 24px;">
            <table class="border-bottom" width="100%">
                <tr>
                    <td width="40%"><b>Telah diterima dari</b></td>
                    <td valign="top" width="60%">{{$booking->account_name}}<br><b>{{$booking->account_hospital}}</b></td>
                </tr>
                <tr>
                    <td valign="top"><b>Uang Sejumlah</b></td>
                    <td valign="top"><b>{{ $currency }} {{ NumberFormat::separatorAmount($booking->total_paid) }}</b></td>
                </tr>
                <tr>
                    <td valign="top"><b>Terbilang</b></td>
                    <td valign="top">{{NumberFormat::terbilang($booking->total_paid)}} {{NumberFormat::currencyToString($currency)}}</td>
                </tr>
                <tr>
                    <td valign="top"><b>Untuk Pembayaran</b></td>
                    <td valign="top">
                        <ol style="margin: 0; padding-left: 16px;">
                            @foreach($participants as $row)
                                <li>{{$row->name}}</li>
                            @endforeach
                        </ol>
                    </td>
                </tr>
                <tr>
                    <td valign="top"><b>Sebagai</b></td>
                    <td valign="top">
                        <p style="text-align:justify;"><b>Registrasi peserta ({{ $booking->package }})</b> dalam acara Indonesian Neurosurgical Nurses Meeting Symposium & Workshop National dengan tema : <b>“Advanced Neurosurgical Endoscopy for Nurse”</b></p>
                    </td>
                </tr>
                <tr>
                    <td valign="top"><b>Tempat</b></td>
                    <td valign="top"><b>Prime Plaza Sanur Hotel - Bali</b>, tanggal 18 - 20 Juli 2025</td>
                </tr>
            </table>
        </div>
        
        <br />
        <br />
        <br />
        <br />
        <div>
            <div class="signature" style="margin-left: auto;">
                <p style="padding-left: 26px;margin:0;z-index:9"><b>Bali, {{ Carbon::parse($booking->created_at)->isoFormat('D MMMM Y') }}</b></p>
                <img width="320" style="margin-top:-12px;"
                    src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/signature_invoice.jpg'))) }}">
            </div>
        </div>
        <div style="clear:both"></div>
        

    </div>
</body>

</html>
