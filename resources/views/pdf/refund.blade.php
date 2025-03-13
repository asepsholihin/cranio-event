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

        table tr.header > td {
            padding: 10px 4px;
            text-align: center;
        }

        table, td, th {
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
    <title>INVOICE {{ $refund->refund_no }}</title>
</head>

<body>
    <div class="page">
    <img width="100" style="position: absolute" class="left"
        src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/logo_invoice.png'))) }}"
        alt="">
    <br/><br/>
    <h2 class="center">INVOICE</h2>
    <br /><br />
    <div class="left">
        Kepada: <b>Bpk/Ibu {{ $refund->name }}</b><br/>
        No Order: {{$order->order_no}}
    </div>
    <div class="right">
        Invoice No &nbsp;&nbsp;&nbsp;&ensp;&emsp;<b>{{$refund->refund_no}}</b><br/>
        Invoice Date &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<b>{{ $refund->created_at->format('d M Y') }}</b>
    </div>
    <table width="100%">
        <tr class="header"><td width="5">No</td><td>Description</td><td>Pax</td><td>Price</td><td>Total Price</td> </tr>
        <?php $no = 1; foreach($orderItems as $orderItem): ?>
        <tr><td><?php echo $no; ?></td><td>{{$orderItem->description}}</td><td class="center">{{$orderItem->pax}}</td><td>{{$currency}}<span class="right">{{NumberFormat::separatorAmount($orderItem->price)}}</span></td><td>{{$currency}}<span class="right">{{NumberFormat::separatorAmount($orderItem->total_price)}}</span></td> </tr>
        <?php $no++; endforeach; ?>
        <tr><td colspan="2"><span class="right fw-bold">JUMLAH</span></td><td colspan="2"></td><td><b>{{$currency}}<span class="right">{{number_format($order->total_payment)}}</span></b></td></tr>
        <tr><td colspan="4"><span class="right fw-bold">TOTAL</span></td><td><b>{{$currency}}<span class="right">{{number_format($order->total_payment)}}</span></b></td></tr>
        <?php
            $paid = 0;
            foreach($invoices as $invAmount) {
                $paid += $invAmount->payment_amount;
            }
            foreach($refunds as $refAmount) {
                $paid -= $refAmount->refund_amount;
            }
            $i = 1; foreach($invoices as $inv): ?>
        <tr><td colspan="3">{{$inv->getDescriptionPDF($currency)}}</td><td>{{$currency}}<span class="right">{{ number_format($inv->payment_amount) }}</span></td>
        <?php if($i == 1): ?>
            <td rowspan="<?php echo (count($invoices) + count($refunds)); ?>"><b>{{$currency}}<span class="right">{{number_format($paid)}}</span></b></td></tr>
        <?php else: ?>
        </tr>
        <?php endif; ?>
        <?php $i++; endforeach; 
        foreach($refunds as $inv):?>
        <tr><td colspan="3">Refund {{$inv->description}} @if(! empty($inv->refund_date))({{$inv->refund_date->format('d M Y')}})@endif</td><td>{{$currency}}<span class="right">({{ number_format($inv->refund_amount) }})</span></td></tr>
        <?php endforeach; ?>
        <tr class="due-payment"><td colspan="4"><span class="right fw-bold text-nowrap">{{NumberFormat::amountInvoiceDescription($order->total_payment - $paid)}}</span></td><td><b>{{$currency}}<span class="right">{{NumberFormat::separatorAmount($order->total_payment - $paid)}}</span></b></td></tr>
    </table>
    <br/>
    <div class="signature">
        PT. Jejak Imani Berkah Bersama <br/>
        <img width="150" style="margin-right:60px" src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/signature_invoice.jpg'))) }}"> <br/>
        <span  style="margin-right:40px;font-size:11pt;font-weight: bold;">Firda Ayuningtyas, S.E.</span>
    </div>
    <b>Email : finance@jejakimani.com</b><br/><br/><br/>
    <p>PT Jejak Imani Berkah Bersama </p>
    <p><b>BANK SYARIAH INDONESIA</b><br/> 1414-14-2997</p>
    <p><b>BANK MANDIRI</b><br/> 127-000-702-8903</p><br/><br/><br/>
    <span style="font-size:10pt">
        Keterangan :<br/>
* Batas pelunasan H-30 sebelum keberangkatan<br/>
* Harga paket sesuai rincian tidak termasuk :<br/>
&emsp;&emsp;- Penyesuaian harga paket dapat berubah mengikuti kebijakan hotel / finalisasi roomlist<br/>
&emsp;&emsp;- Tambahan PPN Pemerintah Arab Saudi<br/>
* Biaya sewaktu-waktu dapat berubah sesuai dengan kondisi di Indonesia maupun di luar negri<br/>
* Pembatalan keberangkatan:<br/>
&emsp;&emsp;a. Uang muka (down payment) tidak dapat dikembalikan (Non-refundable)<br/>
&emsp;&emsp;b. Pembatalan setelah pelunasan visa dan/atau tiket pesawat dan/atau akomodasi lainnya (termasuk perlengkapan umrah<br/>
&emsp;&emsp;&emsp;&nbsp;jika telah diterima oleh Pihak Kedua), maka biaya yang sudah dikeluarkan tersebut tidak dapat dikembalikan (Non-refundable).<br/>
        &emsp;&emsp;c. Pengembalian dana (setelah dikurangi dengan biaya pembatalan), dilakukan kurang dari 1 (satu) bulan setelah <br/>
        &emsp;&emsp;&emsp;&nbsp;Pihak Kedua membatalkan keberangkatanya.<br/>
    </span>

    </div>
</body>

</html>
