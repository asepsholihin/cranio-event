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
            src: url({{ storage_path('private_assets/fonts/Mulish-Regular.ttf') }}) format("truetype");
        }
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            font: 11pt "Mulish";
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
            width: 26.17cm;
            height: 37cm;
            margin-left: 20px;
        }
        .subpage {
            position: relative;
            padding: 4cm 3cm;
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
        .mt-2 {
            margin-top: 24px;
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
        p.indent {
            text-indent: 42px;
        }
        
        @page {
            size: A4;
            margin: 0;
        }
        @media print {
            html, body {
                width: 100%;
                height: 100%;
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
    <title>SURAT PERJANJIAN PERJALANAN IBADAH UMRAH</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="letter-bg">
                <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter.png')))}}" alt="">
            </div>
            <div class="subpage">
                <div class="center">
                    <h4 class="mb-0">SURAT PERJANJIAN PERJALANAN IBADAH UMRAH</h4>
                </div>

                <div class="left mt-3">
                    <p>Yang bertanda tangan di bawah ini:</p>
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="28%">NAMA</td>
                            <td width="2%">:</td>
                            <td>{{ strtoupper($participant->name) }}</td>
                        </tr>
                        <tr>
                            <td>NO. ID</td>
                            <td>:</td>
                            <td>{{ $participant->nik }}</td>
                        </tr>
                        <tr>
                            <td valign="top">ALAMAT</td>
                            <td valign="top">:</td>
                            <td>{{ $address }}</td>
                        </tr>
                        <tr>
                            <td>PEKERJAAN</td>
                            <td>:</td>
                            <td>{{ ucwords(strtolower($participant->job)) }}</td>
                        </tr>
                    </table>
                    <p>Disebut PIHAK KESATU sebagai calon Participant Umrah</p>
                </div>
                
                <div class="left mt">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="28%">NAMA</td>
                            <td width="2%">:</td>
                            <td class="uppercase">H. M. Rizaldy Latief</td>
                        </tr>
                        <tr>
                            <td>NO. ID</td>
                            <td>:</td>
                            <td>3674030609880009</td>
                        </tr>
                        <tr>
                            <td valign="top">ALAMAT</td>
                            <td valign="top">:</td>
                            <td>Jl. Taruna VI No.79 Komplek Taruna Parahyangan RT.006 RW.002 Pasir Endah Ujung Berung, Bandung - Jawa Barat.</td>
                        </tr>
                        <tr>
                            <td>PEKERJAAN</td>
                            <td>:</td>
                            <td>Direktur Utama PPIU</td>
                        </tr>
                    </table>
                    <p>Disebut PIHAK KEDUA sebagai perwakilan resmi PPIU</p>
                </div>

                <div class="left mt">
                    <p class="justify">
                    Bahwa kedua belah pihak sepakat mengadakan perjanjian perjalanan ibadah umrah dengan ketentuan sebagai berikut:
                    </p>
                    <ol>
                        <li class="justify">
                        Bahwa PIHAK KESATU telah memilih PPIU bernama PT. JEJAK IMANI BERKAH BERSAMA sebagai biro perjalanan yang akan menyelenggarakan ibadah umrah;
                        </li>
                        <li class="justify">
                        Bahwa PIHAK KESATU telah memilih paket program umrah dengan BPIU sebesar Rp. {{ NumberFormat::separatorAmount($order->price ?? 0) }} ,- dengan ketentuan:
                            <?php
                                $quota = 0;
                                if($participantUmrohTrip->room_type == "double") {
                                    $quota = 2;
                                }
                                if($participantUmrohTrip->room_type == "triple") {
                                    $quota = 3;
                                }
                                if($participantUmrohTrip->room_type == "quad") {
                                    $quota = 4;
                                }
                                if($participantUmrohTrip->room_type == "queen") {
                                    $quota = 5;
                                }
                                if($participantUmrohTrip->room_type == "single") {
                                    $quota = 1;
                                }
                            ?>
                            <ul style="list-style:none;">
                                <li>a.	Lama perjalanan umrah sebanyak <strong>{{ $umrohTrip->total_days }}</strong> hari.</li>
                                <li>b.	Akomodasi di MAKKAH pada HOTEL <strong>{!! preg_replace("/[\x{2B50}]/u", "<span class='color-primary'>&#9733;</span>", strtoupper($packageUmrohTrip->hotel_makkah)) !!}</strong> selama {{$packageUmrohTrip->nights_in_makkah}} malam <strong>{{$quota}}</strong> orang/kamar.</li>
                                <li>c.	Akomodasi di MADINAH pada HOTEL <strong>{!! preg_replace("/[\x{2B50}]/u", "<span class='color-primary'>&#9733;</span>", strtoupper($packageUmrohTrip->hotel_madinah)) !!}</strong> selama {{$packageUmrohTrip->nights_in_madinah}} malam <strong>{{$quota}}</strong> orang/kamar</li>
                            </ul>
                        </li>
                        <li class="justify">
                        Bahwa PIHAK KEDUA wajib memberangkatkan PIHAK KESATU paling lambat 6 (enam) bulan sejak penyetoran awal BPIU dan/atau 3 (tiga) bulan sejak pelunasan BPIU dan wajib memenuhi paket program umrah yang telah dipilih oleh PIHAK KESATU;
                        </li>
                        <li class="justify">
                        Bahwa PIHAK KEDUA wajib mengasuransikan PIHAK KESATU berupa Asuransi Perjalanan Ibadah Umrah;
                        </li>
                        <li class="justify">
                        Bilamana terdapat pembatalan keberangkatan karena PIHAK KESATU meninggal dunia atau alasan kesehatan atau alasan lain yang sah, maka PIHAK KEDUA wajib mengembalikan BPIU yang telah disetorkan setelah dikurangi biaya administrasi dan pengurusan perjalanan umrah yang telah dikeluarkan oleh PIHAK KEDUA secara adil dan transparan;
                        </li>
                        <li class="justify">
                        Bilamana PIHAK KEDUA gagal memberangkatkan ibadah umrah PIHAK KESATU melebihi batas waktu yang ditentukan, maka BPIU wajib dikembalikan sebesar yang telah disetorkan oleh PIHAK KESATU;
                        </li>
                        <li class="justify">
                        Dalam hal terdapat perselisihan, maka kedua belah pihak sepakat akan menyelesaikan melalui jalur kekeluargaan terlebih dahulu sebelum ditempuh penyelesaian melalui jalur hukum.
                        </li>
                    </ol>
                </div>

                <div class="left mt-3">
                    <p class="justify indent">
                    Demikian surat perjanjian ini disepakati dan ditandatangani bersama-sama serta dibuat dengan penuh kesadaran tanpa adanya paksaan dari pihak manapun.
                    </p>
                </div>
                
                <div class="center mt-5">
                    <p>Tangerang Selatan, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                    <table width="100%">
                        <tr>
                            <td width="50%">
                                <p>PIHAK KESATU</p>
                                <br><br><br>
                                <p><span class="fw-bold">{{ ucwords(strtolower($participant->name)) }}</span></p>
                            </td>
                            <td width="50%">
                                <p>PIHAK KEDUA</p>
                                <br><br><br>
                                <p><span class="fw-bold">H. M. Rizaldy Latief</span></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>    
        </div>
    </div>
</body>
</html>