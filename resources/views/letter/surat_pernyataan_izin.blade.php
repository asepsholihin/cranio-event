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
            font: 13pt "Mulish";
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
    <title>SURAT PERNYATAAN</title>
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

                <div class="center mt-5">
                    <h4 class="mb-0 underline">SURAT PERNYATAAN</h4>
                    <p>Nomor : {{$letterNumber}}</p>
                </div>

                <div class="left mt-5">
                    <p>Yang bertandatangan di bawah ini:</p>
                    <table width="100%">
                        <tr>
                            <td width="28%">Nama</td>
                            <td width="2%">:</td>
                            <td class="uppercase">H. M. Rizaldy Latief</td>
                        </tr>
                        <tr>
                            <td valign="top">Jabatan</td>
                            <td valign="top">:</td>
                            <td class="uppercase">Direktur Utama <br>
                            PT. Jejak Imani Berkah Bersama (Izin PPIU Nomor U.533 Tahun 2020)
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">Alamat</td>
                            <td valign="top">:</td>
                            <td class="uppercase">Jl. Taruna VI No.79 Komplek Taruna Parahyangan RT.006 RW.002 Pasir Endah Ujung Berung, Bandung - Jawa Barat.</td>
                        </tr>
                    </table>
                </div>

                <div class="left mt-3">
                    <p class="justify indent">
                    Dengan surat ini, kami menerangkan bahwa nama yang tertera dibawah ini terdaftar sebagai jama'ah {{ $tripCategory }} Paket <span class="fw-bold">{{ucwords(strtolower($packageUmrohTrip->name))}}</span>, Keberangkatan <span class="fw-bold">{{ \Carbon\Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y') }} - {{ \Carbon\Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y') }}</span>, dengan rincian sebagai berikut:
                    </p>
                </div>

                <div class="left mt-3">
                    <table width="100%">
                        <tr>
                            <td width="28%">Nama</td>
                            <td width="2%">:</td>
                            <td class="uppercase">{{ strtoupper($participant->name) }}</td>
                        </tr>
                    </table>
                </div>

                <div class="left mt-3">
                    <p class="justify indent">
                        Demikian surat keterangan ini kami sampaikan dan untuk dipergunakan sesuai peruntukkannya. Atas kerjasama dan perhatian Bapak/Ibu kami ucapkan terima kasih.
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