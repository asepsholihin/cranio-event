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

        .clear {
            clear: both;
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
    <title>INVOICE </title>
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
            Kepada: <b>Bpk/Ibu </b><br />
            No Order: {{ $order->order_no }}
        </div>
        <div class="right">
            Invoice No &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<b></b><br />
            Invoice Date &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<b></b>
        </div>
        <div class="clear"></div>
        <br>
        <table width="100%">
            <tr class="header">
                <td width="5">No</td>
                <td width="570px">Description</td>
                <td>Pax</td>
                <td>Price</td>
                <td>Total Price</td>
            </tr>
            <tr>
                <td height="400"></td>
                <td></td>
                <td class="center"></td>
                <td><span class="right"></span></td>
                <td><span class="right"></span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="right fw-bold">JUMLAH</span></td>
                <td colspan="2"></td>
                <td><b>{{ $currency }}<span class="right"></span></b></td>
            </tr>
            <tr>
                <td colspan="4"><span class="right fw-bold">TOTAL</span></td>
                <td><b>{{ $currency }}<span class="right"></span></b></td>
            </tr>

            <tr class="due-payment">
                <td colspan="4"><span class="right fw-bold"></span></td>
                <td><b>{{ $currency }}<span class="right"></span></b></td>
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
        @if ($tripCategory == 2)
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
            @if ($tripCategory == 2)
                <b>Keterangan : </b> <br />
                <ol>
                    <li>Apabila saat berangkat jumlah participant tidak memenuhi standar quad (4 orang/kamar) maka calon
                        participant haji akan dikenakan biaya paket sesuai jumlah participant yang ada, yaitu harga paket 3
                        orang/kamar atau 2 orang/kamar.</li>
                    <li>Harga diatas bersifat sementara, harga tetap akan diinformasikan menjelang keberangkatan.</li>
                    <li>Harga menggunakan USD akan disesuaikan dengan kurs pada saat pendaftaran dan pelunasan.</li>
                    <li>Apabila Participant melakukan Pembatalan Haji Mandiri Jejak Imani, maka akan dibebankan biaya
                        administrasi sesuai dengan ketentuan yang berlaku di Jejak Imani.</li>
                    <li>Visa yang digunakan adalah bisa Furoda/Mujamalah.</li>
                    <li>Tambahan biaya yang timbul akibat adanya penerapan protokol Covid-19 menjadi tanggungan participant,
                        seperti hotel karantina, klasifikasi hotel yang digunakan dan penerapan kapasitas participant per
                        kamar, dll.</li>
                    <li>Harga belum termasuk asuransi kesehatan dan asuransi Covid-19.</li>
                    <li>DP booking Haji Mandiri USD 10.000 per participant.</li>
                </ol>
                <b>Pembatalan </b> <br />
                <ol>
                    <li>Setelah proses visa dikenakan 50% dari harga paket.</li>
                    <li>Satu bulan sebelum keberangkatan dikenakan 100% dari harga paket.</li>
                    <li>DP tidak dapat dikembalikan setelah pendaftaran dilakukan.</li>
                </ol>
            @else
                Keterangan :<br />
                * Batas pelunasan H-35 sebelum keberangkatan ({{ $maxPaidAt->format('d M Y') }})<br />
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
            @endif
        </span>

    </div>
</body>

</html>
