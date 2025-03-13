<?php
use App\Support\NumberFormat;
use Carbon\Carbon;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            font: 10pt "Arial";
        }

        /* @font-face {
            font-family: 'Mulish';
            src: url({{ storage_path('private_assets/fonts/Mulish-Regular.ttf') }}) format("truetype");
        } */

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
            /* font-family: 'Mulish'; */
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
            font-style:italic;
            margin-top:8px;
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
        .fs-12{
            font-size:12px;
        }
        .fs-13{
            font-size:13px;
        }
        .fs-20{
            font-size:20px;
        }
        .fs-lighter{
            font-weight:400;
        }
        .text-align-right{
            text-align:right:
        }
    </style>
    <title>Purchase Order</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                <table class="noborder w-100">
                    <tr>
                        <td class="w-50" style="vertical-align:top">
                            <h5 class="text-left">PT JEJAK IMANI BERKAH BERSAMA</h5>
                            <h5 class="subtitle-text-left">
                                Kantor (Jejak Imani Lounge Haji <span></span>Umroh<span></span>Islamic Tours) <br>
                                Jl. Lingkar Timur, Ruko No. 9 & 10 <br>
                                BSD - Tangerang Selatan 15310 <br>
                                Phone : +62 812-8502-2850
                            </h5>
                        </td>
                        <td class="w-50" style="vertical-align:top">
                            <div class="d-grid justify-content-end mb-40">
                                <h5 class="title-right">Purchase Order</h5>
                                <table class="noborder w-100">
                                    <tr>
                                        <td style="width:35%"></td>
                                        <td style="width:40%"><h5 class="title-right-2 fs-12">PO Date</h5></td>
                                        <td style="width:5%"><h5 class="fs-12">:</h5></td>
                                        <td style="width:20%"><h5 class="fs-12 fs-lighter">{{ date('d/m/Y', strtotime($purchaseOrder->po_date)) }}</h5></td>
                                    </tr>
                                    <tr>
                                        <td style="width:35%"></td>
                                        <td style="width:40%"><h5 class="title-right-2 fs-12">PO Number</h5></td>
                                        <td style="width:5%"><h5 class="fs-12">:</h5></td>
                                        <td style="width:20%"><h5 class="fs-12 fs-lighter">{{ $purchaseOrder->po_number }}</h5></td>
                                    </tr>
                                    <tr>
                                        <td style="width:35%"></td>
                                        <td style="width:40%"><h5 class="title-right-2 fs-12">Delivery Due Date</h5></td>
                                        <td style="width:5%"><h5 class="fs-12">:</h5></td>
                                        <td style="width:20%"><h5 class="fs-12 fs-lighter">{{  date('d/m/Y', strtotime($purchaseOrder->delivery_due_date)) }}</h5></td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top">
                            <div class="bg-orange">
                                <h5 class="fs-12">Vendor</h5>
                            </div>
                            <h5 class="title-vendor fs-13">{{ $purchaseOrder->vendor_name }}</h5>
                            <h5 class="text-vendor fs-12">Phone : {{ $purchaseOrder->vendor_phone }}</h5>
                            <h5 class="text-vendor fs-12">Email : {{ $purchaseOrder->vendor_email }}</h5>
                        </td>
                        <td>
                            <div class="bg-orange w-100">
                                <h5 class="fs-12">SHIP TO</h5>
                            </div>
                            <h5 class="text-vendor" style="margin-top:10px;">
                                <span class="fs-13 fs-bold">PT JEJAK IMANI BERKAH BERSAMA</span> <br>
                                <span class="fs-12">Kantor (Jejak Imani Lounge Haji <span></span>Umroh<span></span>Islamic Tours) <br>
                                Jl. Lingkar Timur, Ruko No. 9 & 10 <br>
                                BSD - Tangerang Selatan 15310 <br>
                                Phone : +62 812-8502-2850</span>
                            </h5>
                        </td>
                    </tr>
                </table>
                <br>
                <table class="w-100" style="border:0;">
                    <tr style="background-color:#BF8F00;" class="bordered">
                        <td style="width:10%;" class="bordered">
                            <h5 class="title-table fs-13">No.</h5>
                        </td>
                        <td style="width:35%" class="bordered">
                            <h5 class="title-table fs-13">Item</h5>
                        </td>
                        <td style="width:15%" class="bordered">
                            <h5 class="title-table fs-13">PO Qty</h5>
                        </td>
                        <td style="width:20%" class="bordered">
                            <h5 class="title-table fs-13">Remaining</h5>
                        </td>
                        <td style="width:20%" class="bordered">
                            <h5 class="title-table fs-13">Unit</h5>
                        </td>
                    </tr>
                    @php $total_qty = 0; @endphp
                    @foreach($purchaseOrder->items as $key=>$item)
                    @php
                        $total_qty += $item->po_qty;
                    @endphp
                    <tr class="bordered">
                        <td class="bordered"><h5 class="text-center fs-table-item fs-12">{{ $key + 1 }}</h5></td>
                        <td class="bordered"><h5 class="fs-table-item fs-12">{{ $item->item_name }}</h5></td>
                        <td class="bordered"><h5 class="text-center fs-table-item fs-12">{{ $item->po_qty }}</h5></td>
                        <td class="bordered"><h5 class="text-center fs-table-item fs-12">{{ $item->remaining_qty }}</h5></td>
                        <td class="bordered"><h5 class="text-center fs-table-item fs-12">{{ $item->unit_name ?? " - "}}</h5></td>
                    </tr>
                    @endforeach
                </table>
                <table class="noborder w-100 mt-5">
                    <tr>
                        <td style="width:50%; vertical-align:top">
                            <div class="">
                                <table class="w-100 noborder">
                                    <tr>
                                        <td style="width:40%">
                                            <h5 class="fs-12">Total PO Qty</h5>
                                        </td>
                                        <td style="width:10%"><h5 class="fs-12">:</h5></td>
                                        <td><h5 class="fs-12">{{ $purchaseOrder->total_po_qty }} pcs</h5></td>
                                    </tr>
                                    <tr>
                                        <td style="width:40%">
                                            <h5 class="fs-12">Total Received Qty</h5>
                                        </td>
                                        <td style="width:10%"><h5 class="fs-12">:</h5></td>
                                        <td><h5 class="fs-12">0 pcs</h5></td>
                                    </tr>
                                    <tr>
                                        <td style="width:40%">
                                            <h5 class="fs-12">Total Remaining Qty</h5>
                                        </td>
                                        <td style="width:10%"><h5 class="fs-12">:</h5></td>
                                        <td><h5 class="fs-12">{{ $purchaseOrder->total_po_qty }} pcs</h5></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h5 class="fs-lighter">Data Printed : {{ date('d M Y') }}</h5>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <td style="width:50%">
                            <h5 class="fs-table-item" style="text-align:center">Tangerang Selatan, {{ $purchaseOrder->po_pr_date }}</h5>
                            <div class="" style="height:80px"></div>
                            <h5 class="fs-table-item" style="text-align:center">{{ $purchaseOrder->updated_by_name }}</h5>
                            <h5 class="fs-table-item" style="text-align:center; font-weight:bold">(PT. Jejak Imani Berkah Bersama)</h5>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
