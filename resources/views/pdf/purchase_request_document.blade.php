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
            color:white;
            text-transform:uppercase;
            font-size:14px;
            font-style:italic;
            margin-top:4px;
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
            background-color:yellow;
        }
    </style>
    <title>Purchase Request</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                <table class="noborder w-100">
                    <tr>
                        <td class="w-50">
                            <h5 class="text-left">PT JEJAK IMANI BERKAH BERSAMA</h5>
                            <h5 class="subtitle-text-left">
                                Kantor (Jejak Imani Lounge Haji <span></span>Umroh<span></span>Islamic Tours) <br>
                                Jl. Lingkar Timur, Ruko No. 9 & 10 <br>
                                BSD - Tangerang Selatan 15310 <br>
                                Phone : +62 812-8502-2850
                            </h5>
                        </td>
                        <td class="w-50">
                            <div class="d-grid justify-content-end mb-40">
                                <h5 class="title-right">Purchase Request</h5>
                                <table class="noborder w-100">
                                    <tr>
                                        <td style="width:60%"></td>
                                        <td style="width:20%"><h5 class="title-right-2">Date</h5></td>
                                        <td style="width:20%"><h5 class="border-text">{{ date('d/m/Y', strtotime($purchaseRequest->submitted_date)) }}</h5></td>
                                    </tr>
                                    <tr>
                                        <td style="width:60%"></td>
                                        <td style="width:20%"><h5 class="title-right-2" style="margin-top:-24px">No PR</h5></td>
                                        <td style="width:20%"><h5 class="border-text" style="margin-top:-24px">{{ $purchaseRequest->purchase_request_number }}</h5></td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="bg-orange">
                                <h5>Vendor</h5>
                            </div>
                            <h5 class="title-vendor">{{ $purchaseRequest->vendor_name }}</h5>
                            <h5 class="text-vendor">Phone : {{ $purchaseRequest->vendor_phone }}</h5>
                        </td>
                    </tr>
                </table>
                <br>
                <table class="w-100" style="border:0;">
                    <tr style="background-color:#BF8F00;" class="bordered">
                        <td style="width:10%;" class="bordered">
                            <h5 class="title-table">No.</h5>
                        </td>
                        <td style="width:45%" class="bordered">
                            <h5 class="title-table">Item</h5>
                        </td>
                        <td style="width:15%" class="bordered">
                            <h5 class="title-table">Qty</h5>
                        </td>
                        <td style="width:20%" class="bordered">
                            <h5 class="title-table">Delivery Due Date</h5>
                        </td>
                    </tr>
                    @php $total_qty = 0; @endphp
                    @foreach($purchaseRequest->items as $key=>$item)
                    @php
                        $total_qty += $item->po_qty;
                    @endphp
                    <tr class="bordered">
                        <td class="bordered"><h5 class="text-center fs-table-item">{{ $key + 1 }}</h5></td>
                        <td class="bordered"><h5 class="fs-table-item">{{ $item->item_name }}</h5></td>
                        <td class="bordered"><h5 class="text-center fs-table-item">{{ $item->po_qty }}</h5></td>
                        @if(($key == 0))
                           <td class="bordered" rowspan="{{ count($purchaseRequest->items) }}"><h5 class="text-center fs-table-item-bottom fs-bold">{{ date('d - M', strtotime($purchaseRequest->pr_date)) }}</h5></td>
                        @endif
                    </tr>
                    @endforeach
                    <tr class="noborder-td">
                        <td class="noborder-td"></td>
                        <td class="noborder-td"></td>
                        <td class="noborder-td coret-kuning"><h5 class="text-center fs-table-item-bottom fs-bold">{{ $total_qty }}</h5></td>
                        <td class="noborder-td"></td>
                    </tr>
                </table>

                <table class="noborder w-100 mt-5">
                    <tr>
                        <td style="width:30%;"></td>
                        <td style="width:30%"></td>
                        <td style="width:30%">
                            <h5 class="fs-table-item"">Tangerang Selatan, 25 Januari 2024</h5>
                            <div class="" style="height:80px"></div>
                            <h5 class="fs-table-item"">(PT. Jejak Imani Berkah Bersama)</h5>
                        </td>
                        <td style="width:10%"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
