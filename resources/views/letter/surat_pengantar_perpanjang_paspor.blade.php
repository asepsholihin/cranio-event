<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
            font: 12pt "Mulish";
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
        .fs-2{
            font-size:20px;
            margin:0;
        }
        .ml-2{
            margin-left:5px;
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
    <title>Surat Pengantar Perpanjang Paspor</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="letter-bg">
                <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter.png')))}}" alt="">
            </div>
            <div class="subpage">
                <div class="right">
                    <p>Tangerang Selatan, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                </div>

                <div class="left mt-5">
                    <table width="100%">
                        <tr>
                            <td width="15%">Nomor</td>
                            <td width="2%">:</td>
                            <td>{{$letterNumber}}</td>
                        </tr>
                        <tr>
                            <td>Lampiran</td>
                            <td>:</td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>Perihal</td>
                            <td>:</td>
                            <td><span class="fw-bold">Permohonan Surat Rekomendasi Pembuatan Paspor Baru</span></td>
                        </tr>
                    </table>
                </div>

                <div class="left mt-5">
                    <span class="fw-bold">
                        Kepada Yth.<br>
                        {{$letterInformation->destination_imigration??'-'}}<br>
                        {{$letterInformation->address_imigration??'Di Tempat'}}
                    </span>
                </div>

                <div class="left mt-5">
                    <p><i>Assalamu’alaikum warahmatullahi wabarakatuh</i></p>
                    <p class="justify indent">
                        Segala puji dan syukur kita panjatkan kehadirat Allah <span class="fs-2">ﷻ</span> atas segala nikmat dan karunia yang Allah <span class="fs-2">ﷻ</span> berikan dan limpahkan kepada kita. Sholawat dan salam semoga senantiasa tercurahkan kepada junjungan kita Nabi Muhammad <span class="fs-2 ml-2">ﷺ</span>, beserta para keluarga, para sahabat dan orang-orang yang istiqomah berjalan di bawah naungan sunah beliau sampai hari kiamat kelak.
                    </p>
                    <p class="justify indent">
                        Kami dari PT. Jejak Imani Berkah Bersama, selaku Penyelenggara Perjalanan Ibadah Umrah (PPIU) dengan izin nomor U.533 Tahun 2020, dengan ini bermaksud menyampaikan surat pengantar untuk <span class="fw-bold">Perpanjang Paspor</span> bagi participant kami sebagai berikut:
                    </p>
                </div>

                <div class="left mt-3">
                    <table width="100%">
                        <tr>
                            <td width="28%">Nama</td>
                            <td width="2%">:</td>
                            <td class="uppercase">{{ strtoupper($participant->name) }}</td>
                        </tr>
                        <tr>
                            <td>Tempat, Tanggal Lahir</td>
                            <td>:</td>
                            <td class="uppercase">{{ucwords(strtolower($participant->birth_place)) }}, {{ \Carbon\Carbon::parse($participant->birth_date)->isoFormat('D MMMM Y') }}</td>
                        </tr>
                        <tr>
                            <td valign="top">Alamat</td>
                            <td valign="top">:</td>
                            <td class="uppercase">{{ $address }}</td>
                        </tr>
                        <tr>
                            <td>NIK</td>
                            <td>:</td>
                            <td><span class="uppercase">{{ $participant->nik }}</span></td>
                        </tr>
                        <tr>
                            <td>Rencana keberangkatan</td>
                            <td>:</td>
                            <td class="uppercase">{{ strtoupper(\Carbon\Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y')) }} - {{ strtoupper(\Carbon\Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y')) }}</td>
                        </tr>
                    </table>
                </div>

                <div class="left mt-3">
                    <p class="justify indent">
                        Demikian surat pengantar ini kami sampaikan untuk dipergunakan sebagaimana mestinya. Atas kerja sama dan perhatian Bapak/Ibu, kami ucapkan terima kasih.
                    </p>
                </div>

                <div class="left mt-3">
                    <p>
                    <i>Wassalamu’alaikum warahmatullahi wabarakatuh</i>
                    </p>
                </div>

                <div class="center mt-5">
                    <h4 class="mb-0">PT. JEJAK IMANI BERKAH BERSAMA</h4>
                    <p>Direktur Utama</p>
                    <img width="240" style="margin-right:60px" src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/signature_director.png'))) }}"> <br/>
                    <h4 class="mb-0 underline uppercase">H. M. Rizaldy Latief</h4>
                    <p>NIP. 14.1-DIR.JIBB-001</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
