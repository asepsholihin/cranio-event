<?php
use App\Support\NumberFormat;
use Carbon\Carbon;

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
            padding: 0 40px;
            background-color: #ffffff;
            font: 10pt "Arial";
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .right {
            text-align: right;
            float: right;
        }

        .signature {
            width: 400px;
            text-align: center;
            font-size: 12pt;
        }

        .address {
            float: right;
            margin-top: 5px;
        }

        .left {
            text-align: left;
            float: left;
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
            border: 1px solid #c4c4c4;
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
        .text-nowrap {
            text-wrap: nowrap;
            white-space: nowrap;
        }

        @page {
            margin: 0px;
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
    </style>
    <title>INVOICE {{ $booking->booking_no }}</title>
</head>

<body>
    <div class="page">
        <div class="center">
            <img height="180"
            src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/kop-surat.jpg'))) }}"
            alt="">
        </div>
        <br /><br />
        <h2 class="center underline">INVOICE</h2>
        <br /><br />
        <div>
            <table width="100%">
                <tr>
                    <td width="50%">
                        <p><b>Kepada:</b></p>
                        <p><b>{{ $booking->account_name }}</b></p>
                    </td>
                    <td width="50%">
                        <p><b>Nomor:</b> {{ $booking->booking_no }}</p>
                        <p><b>Tanggal:</b> {{ Carbon::parse($booking->created_at)->isoFormat('D MMMM Y') }}</p>
                    </td>
                </tr>
            </table>
        </div>
        <br /><br />
        <table width="100%">
            <tr class="header">
                <td width="5" valign="top">No</td>
                <td width="570px">Description</td>
                <td valign="top">Jumlah</td>
                <td valign="top">Harga / Orang</td>
                <td valign="top">Total</td>
            </tr>
            
            <tr>
                <td valign="top">1.</td>
                <td valign="top">
                    <p>Registrasi {{ $booking->total_pax }} orang peserta Symposium & Workshop ({{ $booking->package }})</p>
                    <ul>
                        @foreach($participants as $participant)
                        <li>{{ $participant->name }}</li>
                        @endforeach
                    </ul>
                    <p>Institusi: {{ $booking->account_hospital }} dalamacaraIndonesian Neurosurgical NursesMeeting Symposium & Workshop Nasional “CRANIO” dengan tema : “An Integrated Perioperative Nursing Care on Neurosurgery with Approach Craniotomy”</p>
                </td>
                <td valign="top" class="center">{{ $booking->total_pax }}</td>
                <td valign="top"><span class="right">{{ $currency }} {{ NumberFormat::separatorAmount($booking->price_per_pax) }}</span></td>
                <td valign="top"><span class="right">{{ $currency }} {{ NumberFormat::separatorAmount(($booking->price_per_pax * $booking->total_pax)) }}</span></td>
            </tr>
            
            <tr>
                <td colspan="5" class="text-nowrap"><b><span class="right">{{ $currency }} {{ number_format($booking->total_price) }}</span></b>
                </td>
            </tr>
            <tr class="due-payment">
                <td colspan="2"><span class="right fw-bold text-nowrap">Terbilang</span>
                </td>
                <td colspan="3" class="text-nowrap"><b><span class="right">{{ NumberFormat::terbilang($booking->total_price) }} Rupiah</span></b>
                </td>
            </tr>
        </table>
        
        <br /> <br />
        <div>
            <p style="padding:0 4px 0 4px;"><b>Pembayaran melalui transfer ke:</b></p>
            <table class="border-0 no-padding">
                <tr>
                    <td>Bank</td>
                    <td>:</td>
                    <td><b>Bank Mandiri Cab. Semarang</b></td>
                </tr>
                <tr>
                    <td>Atas Nama</td>
                    <td>:</td>
                    <td><b>PARAMARTA YULI ADMAJA</b></td>
                </tr>
                <tr>
                    <td>Nomor Rekening</td>
                    <td>:</td>
                    <td><b>135002 - 065 - 8165</b></td>
                </tr>
            </table>
        </div>

        <br />
        <div class="right">
            <div class="signature">
                <p style="padding-left: 26px;margin:0;"><b>Bali, {{ Carbon::parse($booking->created_at)->isoFormat('D MMMM Y') }}</b></p>
                <img width="320"
                    src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/signature_invoice.jpg'))) }}">
            </div>
        </div>
        <div style="clear:both"></div>
        
        <br /> <br />
        <div style="font-size:10pt">
            <p><b>Note :</b></p>
            <p>- Mohon mengirimkan bukti transfer keWA 085294949418 (Paramarta Yuli A.)</p>
            <p>- Pembayaran diterima jika sudah ada bukti pembayaran yang diterim oleh panitia</p>
        </div>

    </div>
</body>

</html>
