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
            padding: 8px;
            border-collapse: collapse;
            border: 1px solid #c4c4c4;
        }

        table.noborder td, table.noborder {
            border: 0 !important;
        }

        table tr.due-payment {
            background-color: #f3e45d;
        }

        @page {
            size: A4;
            margin: 0;
        }
        @media print {
            html, body {
                height: 21cm;
                width: 29.7cm;
                font: 12pt "Times New Roman";
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
        .w-50{
            width: 50%;
        }
        .d-grid{
            display:grid !important;
            text-align:right !important;
        }
        .justify-content-end{
            justify-content:end !important;
        }
        .title-right{
            font-size:30px;
            color: #BF8F00;
            text-transform:uppercase;
        }
        .text-left{
            font-size:18px;
            /* letter-spacing:1px; */
            text-transform:uppercase;
        }
        .subtitle-text-left{
            font-size:16px;
            line-height:25px;
            font-weight:400;
        }
        .mb-40{
            margin-bottom:40px;
        }
        .border-text{
            border:1px solid gray;
            padding-left:20px;
            padding-right:20px;
            padding-bottom:5px;
            padding-top:5px;
            text-align:center;
            font-size:16px;
            font-weight:400;
        }
        .bg-orange{
            background-color: #BF8F00;
            padding:1px;
            width: 350px;
        }
        .bg-orange h5{
            color:white;
            font-size:16px;
            font-style:italic;
            margin-top:5px;
            margin-left:10px;
            text-transform:uppercase;
        }
        .w-100{
            width: 100%;
        }

        .title-table{
            text-transform:uppercase;
            font-size:12px;
            text-align:center;
        }
        .title-vendor{
            margin-top:10px;
            font-size:16px;
            font-weight:bold;
        }
        .text-vendor{
            font-size:16px;
            font-weight:400;
        }

        .title-right-2{
            font-size:16px;
            /* letter-spacing:1px; */
            font-weight:400;
            text-transform:uppercase;
            text-align:left;
        }

        .subpage{
            padding-top:80px;
            padding-right:25px;
            padding-left:25px;
        }
        .text-center{
            text-align:center;
        }

        .fs-table-item{
            font-size:14px;
            font-weight:500;
        }
        .fs-table-item-bottom{
            font-size:15px;
        }
        .noborder-td{
            border:0 !important;
        }
        .bordered{
            padding-top:10px;
            padding-bottom:0;
            border : 1px solid gray;
        }
        .fs-bold{
            font-weight:bold !important;
        }
        .coret-kuning{
            background-color:#d641e1;
        }
        .header{
            width: 100%;
            text-align:center;
            padding-top:20px;
            padding-bottom:20px;
            border:1px solid black;
        }
        .header h5{
            text-transform:uppercase;
            font-size:20px;
        }
        .title-table{
            color:black;
        }
        .text-align-left{
            text-align:left;
        }
        .text-capitalize{
            text-transform: capitalize;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
    <title>Payment Approval</title>
</head>
<body>
    <div class="book">
    @foreach ( $paymentApproval->approval_items as $payment)
        <div class="page">
            <div class="subpage">
                <div class="header">
                    <h5>Form Approval Pembayaran</h5>
                </div>
                <div class="sub-header bordered">
                    <table class="w-100 noborder" style="border:0;">
                        <tr>
                            <td style="width:20%">
                                <h5>DIVISI</h5>
                            </td>
                            <td style="width:10%">
                                <h5>:</h5>
                            </td>
                            <td style="width:70%;">
                                <h5 style="border-bottom:1px solid black">{{ $paymentApproval->department_name }}</h5>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:20%">
                                <h5>Program</h5>
                            </td>
                            <td style="width:10%">
                                <h5>:</h5>
                            </td>
                            <td style="width:70%;">
                                <h5 style="border-bottom:1px solid black">{{ $payment->purposes }}</h5>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:20%">
                                <h5>Tanggal Pengajuan</h5>
                            </td>
                            <td style="width:10%">
                                <h5>:</h5>
                            </td>
                            <td style="width:70%; border-bottom: 1px solid black">
                                <h5 style="border-bottom:1px solid black">{{ $paymentApproval->date }}</h5>
                            </td>
                        </tr>
                    </table>
                </div>
                <table class="w-100">
                    <thead>
                        <tr>
                            <td rowspan="2" class="bordered" style="vertical-align:center">
                                <h5 class="title-table">No.</h5>
                            </td>
                            <td rowspan="2" class="bordered" style="vertical-align:center">
                                <h5 class="title-table">Keterangan</h5>
                            </td>
                            <td class="bordered" colspan="3" style="vertical-align:center">
                                <h5 class="title-table">Total Harga</h5>
                            </td>
                        </tr>
                        <tr>
                            <td class="bordered">
                                <h5 class="title-table">Satuan <br>Unit/Pcs/Pax</h5>
                            </td>
                            <td class="bordered">
                                <h5 class="title-table">Harga Satuan</h5>
                            </td>
                            <td class="bordered">
                                <h5 class="title-table">Jumlah</h5>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- <tr>
                            <td class="bordered" colspan="6"><h5 class="title-table text-align-left text-capitalize">Item</h5></td>
                        </tr> -->
                        @php
                            $number = count($payment->items);
                        @endphp
                        @foreach ($payment->items as $keys=>$item)
                            <tr>
                                <td class="bordered"><h5 class="title-table">{{ $keys+1 }}</h5></td>
                                <td class="bordered" style="width:55%"><h5 class="title-table text-align-left text-capitalize">{{ $item->item_name }}</h5></td>
                                <td class="bordered"><h5 class="title-table">{{ $item->qty }}</h5></td>
                                <td class="bordered"><h5 class="title-table">Rp. {{ number_format($item->unit_price,0,',','.') }}</h5></td>
                                <td class="bordered"><h5 class="title-table">Rp. {{ number_format($item->total_price,0,',','.') }}</h5></td>
                            </tr>
                        @endforeach
                        @foreach ($payment->evidence as $key=>$item)
                            <tr>
                                <td class="bordered"><h5 class="title-table">{{ $key+$number+1 }}</h5></td>
                                <td class="bordered" style="width:55%"><h5 class="title-table text-align-left text-capitalize">{{ $item->description }}</h5></td>
                                <td class="bordered"><h5 class="title-table">-</h5></td>
                                <td class="bordered"><h5 class="title-table">-</h5></td>
                                <td class="bordered"><h5 class="title-table">-</h5></td>
                            </tr>
                        @endforeach

                        <!-- JUMLAH -->
                        <tr class="coret-kuning">
                            <td class="bordered" colspan="2"><h5 class="title-table">Jumlah</h5></td>
                            <td class="bordered"><h5 class="title-table"></h5></td>
                            <td class="bordered"><h5 class="title-table"></h5></td>
                            <td class="bordered"><h5 class="title-table">Rp. {{ number_format($payment->total_amount,0,',','.') }}</h5></td>
                        </tr>
                    </tbody>
                </table>
                <div class="bordered">
                    <table style="padding-top:40px" class="w-100 noborder">
                        <tr>
                            <td><h5 class="title-table text-capitalize">Diusulkan Oleh, </h5></td>
                            <td><h5 class="title-table text-capitalize">Manager </h5></td>
                            <td><h5 class="title-table text-capitalize">Menyetujui, <br>Vice President </h5></td>
                            <td><h5 class="title-table text-capitalize">President Director </h5></td>
                        </tr>
                        <tr>
                            <td style="height:80px"></td>
                            <td style="height:80px"></td>
                            <td style="height:80px"></td>
                            <td style="height:80px"></td>
                        </tr>
                        <tr>
                            <td><h5 class="title-table text-capitalize" style="border-bottom:1px solid black">{{ $paymentApproval->submitted_by_name }}</h5></td>
                            <td><h5 class="title-table text-capitalize" style="border-bottom:1px solid black">{{ $paymentApproval->app_manager_by_name }}</h5></td>
                            <td><h5 class="title-table text-capitalize" style="border-bottom:1px solid black">{{ $paymentApproval->app_vp_by_name }}</h5></td>
                            <td><h5 class="title-table text-capitalize" style="border-bottom:1px solid black">{{ $paymentApproval->president_approval_by_name }}</h5></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>
