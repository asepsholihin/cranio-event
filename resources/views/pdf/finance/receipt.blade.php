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
            font-family: 'Mulish';
            src: url({{  storage_path('private_assets/fonts/Mulish-Regular.ttf') }}) format("truetype");
            /* src: url({{ str_replace('\\', '/', storage_path('private_assets/fonts/Mulish-Regular.ttf')) }}) format("truetype"); */
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
        .text-nowrap {
            text-wrap: nowrap;
            white-space: nowrap;
        }

        @page {
            size: A4;
            margin: 0;
        }

        @media print {

            html,
            body {
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
        $itemNames = '';
        $countItems = count($items);
        foreach ($items as $key => $item) {
            $itemNames .= $item->description;
            if ($key == $countItems - 2) {
                $itemNames .= ' and ';
            } else {
                $itemNames .= ', ';
            }
        }
        $itemNames = rtrim($itemNames, ', ');
    @endphp
    <div class="book">
        <div class="page">
            <div class="letter-bg">
                @if ($currency == 'USD')
                    <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_receipt_usd.jpg'))) }}"
                        alt="">
                @else
                    <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_receipt.jpg'))) }}"
                        alt="">
                @endif
            </div>
            <div class="subpage">
                <br />
                <div style="margin-bottom:12px;margin-left:150px;font-weight:bold">Bpk/Ibu {{ $invoice->name }}</div>
                <div style="margin-bottom:12px;margin-left:150px;">{{ str_replace('INV', 'KWT', $invoice->invoice_no) }}
                </div>
                <div style="margin-bottom:6px;margin-left:150px;">
                    {{ \Carbon\Carbon::parse($invoice->created_at)->isoFormat('D MMMM Y') }}</div>
                <br /><br /><br /><br /><br />
                <table>
                    <tr>
                        <td width="350" class="justify">
                            Tanggal {{ \Carbon\Carbon::parse($invoice->payment_date)->isoFormat('D MMMM Y') }} telah
                            diterima <b>{{ $itemNames }} sebesar {{ $currency }}
                                {{ number_format($invoice->payment_amount) }},-
                        </td>
                        <td width="80"></td>
                        <td width="120" style="vertical-align: top;">
                            <table width="100%">
                                @foreach ($items as $item)
                                    <tr>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ $item->qty . ' ' . $item->unit }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                        <td width="110"></td>
                        <td style="vertical-align: top;">
                            <table width="100%">
                                @foreach ($items as $item)
                                    <tr>
                                        <td class="text-nowrap">{{ $currency }} {{ number_format($item->total_price) }}</td>
                                    </tr>
                                @endforeach
                            </table>
                            {{-- {{ number_format($invoice->payment_amount) }},- --}}
                        </td>
                    </tr>
                </table>
                <br /><br /><br />
                <div style="padding-left: 5px">
                    <b><i>Terbilang: "{{ NumberFormat::terbilang($invoice->payment_amount) }}
                            {{ NumberFormat::currencyToString($currency) }}"</i></b>
                    <br /><br /><br /><br />
                    <br />
                    <i>
                        <p>Semoga Bapak/Ibu dan keluarga selalu diberikan Kesehatan,</p>
                        <p>keberkahan dalam kesehariannya, dan dilancarkan rizkinya,</p>
                        <p>Aamiin.</p>
                    </i><br />
                    <b>*DP Non-Refundable (down payment tidak dapat dikembalikan)</b>
                </div>
                <br>
                <br>
                <br>
                <br>
                <br>
                <table width="100%" style="position:absolute;bottom:4.3cm">
                    <tr>
                        <td width="55%"></td>
                        <td width="17%" style="font-size:12pt;">
                            DISKON
                        </td>
                        <td style="font-size:12pt;font-weight:bold;background-color:">
                            {{ $currency }} {{ number_format($invoice->total_discount) }}
                        </td>
                    </tr>
                </table>
                <span
                    style="font-weight:bold; position:absolute; top:738px; right:20px; font-size:12pt;width: 25%;">{{ $currency }}
                    {{ number_format($invoice->payment_amount) }},-</span>
                <!-- <img width="100" style="position:absolute; top:848px; right:100px;" src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/signature_invoice.jpg'))) }}"> -->
                <!-- <span style="font-weight:bold; position:absolute; top:950px; right:65px; font-size:9pt; background-color:#fff">Firda Ayuningtyas, S.E.</span> -->
            </div>
        </div>
    </div>
</body>

</html>
