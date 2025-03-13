<?php
use App\Support\NumberFormat;
use Illuminate\Support\Facades\DB;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">
        @font-face {
            font-family: 'Mulish';
            src: url({{ storage_path('private_assets/fonts/Mulish-Regular.ttf') }}) format("truetype");
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
            width: 210mm;
            height: 100%;
            top: 0;
            left: 0;
            right: 0;
            margin: auto;
        }
        .letter-bg img {
            width: 210mm;
            min-height: 297mm;
            margin: auto;
        }
        .subpage {
            position: relative;
            padding-top: 6.2cm;
            padding-left: 10px;
            margin: auto;
            width: 210mm;
            height: 235mm;
            z-index: 999;
        }

        .right {
            text-align: right;
            float: right;
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
        h1,h2,h3,h4,h5,p {
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
            html, body {
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
        .page_break {
            page-break-before: always;
        }
        /* Invoice */
        .invoice-wrapper {
            padding: 1cm;
        }
        .invoice-wrapper table tr.header>td {
            padding: 10px 4px;
            text-align: center;
        }

        .invoice-wrapper table,
        .invoice-wrapper td,
        .invoice-wrapper th {
            padding: 4px;
            border-collapse: collapse;
            border: 1px solid #c4c4c4;
        }
        .invoice-wrapper table tr.due-payment {
            background-color: #f3e45d;
        }
        .signature {
            text-align: right;
            float: right;
            font-size: 12pt;
        }
    </style>
    <title>Receipt</title>
</head>
<body>
    @php
    $packages = "";
    if($order->is_badal) {
        $category = 1;
        $title = "Badal";
        $badal = DB::table('badal_umroh_trips')->where('order_umroh_trip_id', $order->id)->first();
        $packages = $badal->badal_package;
        $participant_badal = json_decode($badal->participant_badal);
        $totalPax = $badal->pax;
    } else {
        $countPackages = count($orderItems);
        foreach($orderItems as $key => $item) {
            $packages .= $item->name;
            if($key == $countPackages - 2) {
                $packages .= " and ";    
            } else {
                $packages .= ", ";    
            }
        }
        $packages = rtrim($packages, ', ');
        $category = $umrohTrip->category_id;
        $title = $umrohTrip->title;
    }
    @endphp
    <div class="book">
        <div class="page">
            <div class="main">
                <div class="letter-bg">
                    @if($invoice->invoice_url != null)
                        <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_receipt_alt.jpg')))}}" alt="">
                    @else
                        @if ($category == 2)
                            <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_receipt_usd_.jpg')))}}" alt="">
                        @else
                            @if($currency == 'USD')
                            <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_receipt_usd.jpg')))}}" alt="">
                            @else
                            <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_receipt.jpg')))}}" alt="">
                            @endif
                        @endif
                    @endif
                </div>
                <div class="subpage">
                    <br/>
                    <div style="margin-bottom:12px;margin-left:150px;font-weight:bold">Bpk/Ibu {{$order->name}}</div>
                    <div style="margin-bottom:12px;margin-left:150px;">{{str_replace('INV', 'KWT', $invoice->invoice_no)}}</div>
                    <div style="margin-bottom:6px;margin-left:150px;">{{\Carbon\Carbon::parse($invoice->created_at)->isoFormat('D MMMM Y')}}</div>
                    <div style="margin-bottom:6px;margin-left:150px;">@if($sales) {{ $sales->whatsapp_number }} ({{ $sales->sales_name }}) @endif</div><br/><br/><br/><br/>
                    <table width="100%">
                        <tr>
                            <td width="350" class="justify">
                                @if($order->is_badal)
                                    Tanggal {{\Carbon\Carbon::parse($invoice->payment_date)->isoFormat('D MMMM Y')}} telah diterima <b>Pembayaran {{$invoice->description}}</b> {{$packages}} sebesar {{$currency}} {{number_format($invoice->payment_amount)}},-
                                    <br>
                                    @foreach($participant_badal as $row)
                                    - {{ $row->name_in_badal }}<br>
                                    @endforeach
                                @else
                                    Tanggal {{\Carbon\Carbon::parse($invoice->payment_date)->isoFormat('D MMMM Y')}} telah diterima <b>Pembayaran {{$invoice->description}}</b> Paket {{$packages}} {{$title}} sebesar 
                                    @if($invoice->usd_convertion)
                                        IDR {{number_format($invoice->payment_amount)}},-
                                    @else
                                        {{$currency}} {{number_format($invoice->payment_amount)}},-
                                    @endif
                                @endif
                            </td>
                            <td width="65"></td>
                            <td width="185" style="vertical-align: top; text-align: center">{{$totalPax}} pax</td>
                            <td class="text-nowrap" style="vertical-align: top; text-align: center">
                                @if($invoice->usd_convertion)
                                    IDR {{number_format($invoice->payment_amount)}},-
                                @else
                                    {{$currency}} {{number_format($invoice->payment_amount)}},-
                                @endif
                            </td>
                        </tr>
                    </table>
                    <br/><br/><br/>
                    <div style="padding-left: 5px" >
                        @if($invoice->usd_convertion)
                            <b><i>Terbilang: "{{NumberFormat::terbilang($invoice->payment_amount)}} Rupiah"</i></b>
                        @else
                            <b><i>Terbilang: "{{NumberFormat::terbilang($invoice->payment_amount)}} {{NumberFormat::currencyToString($currency)}}"</i></b>
                        @endif
                        <br/><br/><br/><br/>
                        <br/>
                        <i><p>Semoga Bapak/Ibu dan keluarga selalu diberikan Kesehatan,</p>
                        <p>keberkahan dalam kesehariannya, dan dilancarkan rizkinya,</p>
                        <p>Aamiin.</p></i><br/>
                        <b>*DP Non-Refundable (down payment tidak dapat dikembalikan)</b>
                    </div>
                    <span class="text-nowrap" style="font-weight:bold; position:absolute; top:738px; right:20px; font-size:12pt;">
                        @if($invoice->usd_convertion)
                            IDR {{number_format($invoice->payment_amount)}},-
                        @else
                            {{$currency}} {{number_format($invoice->payment_amount)}},-
                        @endif
                    </span>
                    <!-- <img width="100" style="position:absolute; top:848px; right:100px;" src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/signature_invoice.jpg'))) }}"> -->
                    <!-- <span style="font-weight:bold; position:absolute; top:950px; right:65px; font-size:9pt; background-color:#fff">Firda Ayuningtyas, S.E.</span> -->
                </div>
            </div>
            
            <div class="page_break"></div>

            <div class="main invoice-wrapper">
                <img width="100" style="position: absolute" class="left"
                    src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/logo_invoice.png'))) }}"
                    alt="">
                <br /><br />
                <h2 class="center">INVOICE</h2>
                <br /><br />
                <div class="left">
                    Kepada: <b>Bpk/Ibu {{ $order->name }}</b><br />
                    No Order: {{ $order->order_no }}
                </div>
                <div class="right">
                    Invoice No &nbsp;&nbsp;&nbsp;&ensp;&emsp;<b>{{ $invoice->invoice_no }}</b><br />
                    Invoice Date &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<b>{{ $invoice->created_at->format('d M Y') }}</b>
                </div>
                <table width="100%">
                    <tr class="header">
                        <td width="5">No</td>
                        <td width="570px">Description</td>
                        <td>Pax</td>
                        <td>Price</td>
                        <td>Total Price</td>
                    </tr>
                    <?php $no = 1; foreach($orderItemsForInvoice as $orderItem): ?>
                    <tr>
                        <td><?php echo $no; ?></td>
                        <td>{{ $orderItem->description }}</td>
                        <td class="center">{{ $orderItem->pax }}</td>
                        <td>{{ $currency }}<span
                                class="right">{{ number_format($orderItem->price, 0, ',', '.') }}</span></td>
                        <td>{{ $currency }}<span
                                class="right">{{ number_format($orderItem->total_price, 0, ',', '.') }}</span></td>
                    </tr>
                    <?php $no++; endforeach; ?>
                    <tr>
                        <td colspan="2"><span class="right fw-bold">JUMLAH</span></td>
                        <td colspan="2"></td>
                        <td class="text-nowrap"><b>{{ $currency }}<span class="right">{{ number_format($order->total_payment, 0, ',', '.') }}</span></b></td>
                    </tr>
                    <tr>
                        <td colspan="4"><span class="right fw-bold">TOTAL</span></td>
                        <td class="text-nowrap"><b>{{ $currency }}<span class="right">{{ number_format($order->total_payment, 0, ',', '.') }}</span></b></td>
                    </tr>
                    <?php
                    $paid = 0;
                    foreach($invoices as $invAmount) {
                        if($invAmount->status != 3 && $invAmount->status != 4) {
                            if($invAmount->usd_convertion){
                                $paid += round($invAmount->payment_amount/$invAmount->usd_price);
                            } else {
                                $paid += $invAmount->payment_amount;
                            }
                        }
                    }
                    foreach($refunds as $refAmount) {
                        $paid -= $refAmount->refund_amount;
                    }
                    $i = 1; foreach($invoices as $inv): ?>
                    <tr>
                        <td colspan="3">
                            @if($inv->status != 3 && $inv->status != 4)
                                {{ $inv->getDescriptionPDF($currency) }}
                            @else
                                <span class="font-italic">{{ $inv->getDescriptionPDF($currency) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($inv->usd_convertion)
                                {{ $currency }}<span class="right">{{ number_format(round($inv->payment_amount/$inv->usd_price), 0, ',', '.') }}</span>
                            @else
                                {{ $currency }}<span class="right">{{ number_format($inv->payment_amount, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <?php if($i == 1): ?>
                        @if($inv->usd_convertion)
                            <td rowspan="<?php echo count($invoices) + count($refunds); ?>">
                                <b>{{ $currency }}<span class="right">{{ number_format($paid, 0, ',', '.') }}</span></b>
                            </td>
                        @else
                            <td rowspan="<?php echo count($invoices) + count($refunds); ?>">
                                <b>{{ $currency }}<span class="right">{{ number_format($paid, 0, ',', '.') }}</span></b>
                            </td>
                        @endif
                    </tr>
                    <?php else: ?>
                    </tr>
                    <?php endif; ?>
                    <?php $i++; endforeach;
                foreach($refunds as $inv):?>
                    <tr>
                        <td colspan="3">Refund {{ $inv->description }} @if (!empty($inv->refund_date))
                                ({{ $inv->refund_date->format('d M Y') }})
                            @endif
                        </td>
                        <td>{{ $currency }}<span class="right">({{ number_format($inv->refund_amount, 0, ',', '.') }})</span></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="due-payment">
                        <td colspan="4"><span
                                class="right fw-bold text-nowrap">{{ NumberFormat::amountInvoiceDescription($order->total_payment - $paid) }}</span>
                        </td>
                        <td>
                            @if($currency == "IDR")
                                <b>{{ $currency }}<span class="right">{{ number_format($order->total_payment - $paid, 0, ',', '.') }}</span></b>
                            @else
                                <b>{{ $currency }}<span class="right">{{ number_format($order->total_payment - $paid, 0, ',', '.') }}</span></b>
                            @endif
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
                @if($inv->invoice_url == null)
                    <p>PT Jejak Imani Berkah Bersama </p>
                    @if ($tripCategory == 2)
                        @if($tripSubCategory == 5) 
                            <p><b>BANK SYARIAH INDONESIA ({{ $currency }})</b><br />1313-13-2998</p>
                            <p><b>BANK MUAMALAT ({{ $currency }})</b><br />124-00-122-47</p>
                            <p><b>BANK MANDIRI ({{ $currency }})</b><br />127-00-1190595-5</p>
                            <br />
                        @else
                            <p><b>BANK SYARIAH INDONESIA ({{ $currency }})</b><br />1313-13-2998</p>
                            <p><b>BANK MUAMALAT ({{ $currency }})</b><br />124-00-122-47</p>
                            <br /><br /><br />
                        @endif
                    @else
                        @if ($currency != 'USD')
                            <p><b>BANK SYARIAH INDONESIA</b><br /> 1414-14-2997</p>
                            <p><b>BANK MANDIRI</b><br /> 127-000-702-8903</p><br /><br /><br />
                        @else
                            <p><b>BANK SYARIAH INDONESIA</b><br /> 1414-14-2997</p><br /><br /><br /><br /><br />
                        @endif
                    @endif
                @else
                    <br /><br /><br /><br /><br />
                @endif

                <span style="font-size:10pt">
                    @if ($tripCategory == 2)
                        <b>Keterangan : </b> <br />
                        <ol>
                            <!-- FURODA -->
                            @if($tripSubCategory == 5) 
                                <li>Harga paket bersifat sementara. Finalisasi harga akan diinformasikan pada tahun keberangkatan.</li>
                                <li>Uang muka (down payment) Haji Furoda senilai USD 11.000,- (sebelas ribu US Dolar) per jemaah.</li>
                                <li>Pembayaran menggunakan IDR akan disesuaikan menggunakan kurs dari Jejak Imani pada hari transaksi.</li>
                                <li>Khusus untuk uang muka (down payment) yang dibayarkan dengan skema cicilan (bertahap), akan disesuaikan menggunakan kurs Jejak Imani pada saat pelunasan uang muka (down payment).</li>
                                <li>Jemaah yang melakukan pembatalan setelah pendaftaran sampai dengan dengan sebelum visa terbit, akan dikenakan biaya pembatalan sebesar USD 2.500,- (dua ribu lima ratus US Dolar).</li>
                                <li>Apabila tidak ada teman sekamar (berempat), maka jemaah akan dikenakan biaya paket sesuai teman sekamar yang ada (harga paket sekamar berdua atau bertiga).</li>
                            @else 
                                <!-- KHUSU/PLUS -->
                                <li>Harga paket bersifat sementara. Finalisasi harga akan diinformasikan pada tahun keberangkatan.</li>
                                <li>Uang muka (down payment) Haji Arbain (Haji Khusus) senilai USD 5.000,- (lima ribu US Dolar) per jemaah..</li>
                                <li>Pembayaran menggunakan IDR akan disesuaikan menggunakan kurs dari Jejak Imani pada hari transaksi.</li>
                                <li>Khusus untuk uang muka (down payment) yang dibayarkan dengan skema cicilan (bertahap), akan disesuaikan menggunakan kurs Jejak Imani pada saat pelunasan uang muka (down payment).</li>
                                <li>Jemaah yang melakukan pembatalan setelah pendaftaran (pembayaran uang muka) sampai dengan 6 (enam) bulan sebelum tanggal keberangkatan, termasuk pindah travel (pindah PIN), akan dikenakan biaya pembatalan sebesar USD 1.000,- (seribu US Dolar).</li>
                                <li>Apabila tidak ada teman sekamar (berempat), maka jemaah akan dikenakan biaya paket sesuai teman sekamar yang ada (harga paket sekamar berdua atau bertiga).</li>
                            @endif
                        </ol>
                        <b>Pembatalan </b> <br />
                        <ol>
                            <li>Setelah proses visa dikenakan 50% dari harga paket.</li>
                            <li>Satu bulan sebelum keberangkatan dikenakan 100% dari harga paket.</li>
                            <li>DP tidak dapat dikembalikan setelah pendaftaran dilakukan.</li>
                        </ol>
                    @else
                        Keterangan :<br />
                        * Batas pelunasan H-{{$invoiceDueDate}} sebelum keberangkatan @if($maxPaidAt) ({{ $maxPaidAt->format('d M Y') }}) @endif<br />
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
        </div>
    </div>
</body>
</html>
