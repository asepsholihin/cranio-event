<?php
use App\Support\NumberFormat;

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
            text-align: right;
            float: right;
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
            padding: 4px;
            border-collapse: collapse;
            border: 1px solid #c4c4c4;
        }

        table tr.due-payment {
            background-color: #f3e45d;
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
    <title>PROFORMA INVOICE {{ $items[0]->order_no }}</title>
</head>

<body>
    <div class="page">
        <img width="100" style="position: absolute" class="left"
            src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/logo_invoice.png'))) }}"
            alt="">
        <br /><br />
        <h2 class="center">PROFORMA INVOICE</h2>
        <br /><br />
        <div class="left">
            Kepada: <b>Bpk/Ibu {{ $invoice->name }}</b>
        </div>
        <div class="right">
            <table style="border:none;">
                <tr>
                    <td style="border:none;">Invoice No</td>
                    <td style="border:none;"><b>{{ $items[0]->order_no }}</b></td>
                </tr>
                <tr>
                    <td style="border:none;">Invoice Date</td>
                    <td style="border:none;"><b>{{ $invoice->date }}</b></td>
                </tr>
            </table>
            <br />
        </div>
        <table width="100%">
            <tr class="header">
                <td width="5">No</td>
                <td width="570px">Description</td>
                <td>Pax</td>
                <td>Price</td>
                <td>Total Price</td>
            </tr>
            <?php $no=1; $totalDiscount=0; $totalPayment=0; foreach ($items as $value): ?>
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $value->title }} - {{ $value->package_name }} - {{ ucwords($value->room_type) }}</td>
                <td align="center">{{ $value->pax + $value->without_bed}}</td>
                <td><span class="right">{{ NumberFormat::separatorAmount($value->price_per_pax) }}</span></td>
                <td>{{ $currency }} <span class="right">{{ NumberFormat::separatorAmount(($value->pax+$value->without_bed) * $value->price_per_pax) }}</span></td>
            </tr>
            <?php 
                $totalDiscount = $value->total_discount;
                $totalPayment = $value->total_payment;
            ?>
            <?php if($value->price_infants > 0): ?>
            <tr>
                <td>{{ $no++ }}</td>
                <td>Infants</td>
                <td align="center">{{ $value->pax_infants }}</td>
                <td></td>
                <td>{{ $currency }}<span class="right">{{ NumberFormat::separatorAmount($value->price_infants) }}</span>
                </td>
            </tr>
            <?php endif; ?>
            <?php if($value->price_without_bed > 0): ?>
            <tr>
                <td colspan="4"><span class="right fw-bold">Potongan Tanpa Bed</span></td>
                <td><b>{{ $currency }}<span class="right">({{ NumberFormat::separatorAmount($value->price_without_bed) }})</span></b>
                </td>
            </tr>
            <?php endif; ?>
            <?php endforeach; ?>
            <?php if($totalDiscount > 0): ?>
            <tr>
                <td colspan="4"><span class="right fw-bold">{{ $value->discount_notes }}</span></td>
                <td><b>{{ $currency }}<span class="right">({{ NumberFormat::separatorAmount($totalDiscount) }})</span></b>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <td colspan="4"><span class="right fw-bold">TOTAL</span></td>
                <td><b>{{ $currency }}<span class="right">{{ NumberFormat::separatorAmount($totalPayment) }}</span></b>
                </td>
            </tr>
        </table>
        <br />
        <div class="signature">
            PT. Jejak Imani Berkah Bersama <br />
            <img width="150" style="margin-right:60px"
                src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/signature_invoice.jpg'))) }}">
            <br />
            <span style="margin-right:40px;font-size:11pt;font-weight: bold;">Firda Ayuningtyas, S.E.</span>
        </div>
        <b>Email : finance@jejakimani.com</b><br /><br /><br />
        <p>PT Jejak Imani Berkah Bersama </p>
        
        @if ($items[0]->category_id == 2)
            <p><b>BANK SYARIAH INDONESIA ({{ $currency }})</b><br />1313-13-2998</p><br /><br /><br /><br /><br />
        @else
            @if ($currency != 'USD')
                <p><b>BANK SYARIAH INDONESIA</b><br /> 1414-14-2997</p>
                <p><b>BANK MANDIRI</b><br /> 127-000-702-8903</p><br /><br /><br />
            @else
                <p><b>BANK SYARIAH INDONESIA</b><br /> 1414-14-2997</p><br /><br /><br /><br /><br />
            @endif
        @endif

        <span style="font-size:10pt">
            Keterangan :<br />
            * Batas pelunasan H-{{ $items[0]->invoice_due_date }} sebelum keberangkatan<br />
            * Harga paket sesuai rincian tidak termasuk :<br />
            &emsp;&emsp;- Penyesuaian harga paket dapat berubah mengikuti kebijakan hotel / finalisasi
            roomlist<br />
            &emsp;&emsp;- Tambahan PPN Pemerintah Arab Saudi<br />
            * Biaya sewaktu-waktu dapat berubah sesuai dengan kondisi di Indonesia maupun di luar negri<br />
            * Pembatalan keberangkatan:<br />
            &emsp;&emsp;a. Uang muka (down payment) tidak dapat dikembalikan (Non-refundable)<br />
            &emsp;&emsp;b. Pembatalan setelah pelunasan visa dan/atau tiket pesawat dan/atau akomodasi lainnya
            (termasuk
            perlengkapan umrah<br />
            &emsp;&emsp;&emsp;&nbsp;jika telah diterima oleh Pihak Kedua), maka biaya yang sudah dikeluarkan
            tersebut
            tidak dapat dikembalikan (Non-refundable).<br />
            &emsp;&emsp;c. Pengembalian dana (setelah dikurangi dengan biaya pembatalan), dilakukan kurang dari 1
            (satu)
            bulan setelah <br />
            &emsp;&emsp;&emsp;&nbsp;Pihak Kedua membatalkan keberangkatanya.<br />
        </span>

    </div>
</body>

</html>
