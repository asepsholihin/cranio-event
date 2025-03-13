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
    <title>INVOICE {{ $invoice->invoice_no }}</title>
</head>

<body>
    <div class="page">
        <img width="100" style="position: absolute" class="left"
            src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/logo_invoice.png'))) }}"
            alt="">
        <br /><br />
        <h2 class="center">INVOICE</h2>
        <br /><br />
        <div class="left">
            Kepada: <b>Bpk/Ibu {{ $invoice->name }}</b>
        </div>
        <div class="right">
            <table class="border-0">
                <tr>
                    <td>Invoice No</td>
                    <td align="right"><b>{{ $invoice->invoice_no }}</b></td>
                </tr>
                <tr>
                    <td>Invoice Date</td>
                    <td align="right"><b>{{ $invoice->created_at->format('d M Y') }}</b></td>
                </tr>
            </table>
        </div>
        <table width="100%">
            <tr class="header">
                <td width="5">No</td>
                <td width="570px">Description</td>
                <td>Qty</td>
                <td>Price</td>
                <td>Total Price</td>
            </tr>
            <?php $no = 1; foreach($items as $item): ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td>{{ $item->description }}</td>
                <td class="center">{{ $item->qty }}</td>
                <td class="text-nowrap">{{ $currency }}<span class="right">{{ NumberFormat::separatorAmount($item->price) }}</span></td>
                <td class="text-nowrap">{{ $currency }}<span class="right">{{ NumberFormat::separatorAmount($item->total_price) }}</span></td>
            </tr>
            <?php $no++; endforeach; ?>
            <tr>
                <td colspan="2"><span class="right fw-bold">JUMLAH</span></td>
                <td colspan="2"></td>
                <td class="text-nowrap"><b>{{ $currency }}<span class="right">{{ number_format($invoice->total_payment + $invoice->total_discount) }}</span></b>
                </td>
            </tr>
            <?php if($invoice->total_discount > 0): ?>
            <tr>
                <td colspan="4"><span class="right fw-bold">DISKON</span></td>
                <td class="text-nowrap"><b>{{ $currency }}<span class="right">{{ number_format($invoice->total_discount) }}</span></b>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <td colspan="4"><span class="right fw-bold">TOTAL</span></td>
                <td class="text-nowrap"><b>{{ $currency }}<span class="right">{{ number_format($invoice->total_payment) }}</span></b>
                </td>
            </tr>
            <tr class="due-payment">
                <td colspan="4"><span
                        class="right fw-bold text-nowrap">{{ NumberFormat::amountInvoiceDescription($invoice->due_payment) }}</span>
                </td>
                <td class="text-nowrap"><b>{{ $currency }}<span class="right">{{ NumberFormat::separatorAmount($invoice->due_payment) }}</span></b>
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
        <p><b>BANK SYARIAH INDONESIA ({{ str_replace('.', '', $currency) }})</b><br />1414-14-2997</p>
        <br /><br /><br /><br /><br />

        <span style="font-size:10pt">
            Keterangan :<br />
            * Batas pelunasan sebelum ({{ $maxPaidAt->format('d M Y') }})<br />
        </span>

    </div>
</body>

</html>
