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
    <title>Surat Pemberitahuan Nomor Porsi</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="letter-bg">
                <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter.png')))}}" alt="">
            </div>
            <div class="subpage">
                <div class="center mt-5">
                    <h4 class="mb-0 underline">SURAT PEMBERITAHUAN</h4>
                    <p>Nomor : {{$letterNumber}}</p>
                </div>

                <div class="left mt-5">
                    <table width="100%">
                        <tr>
                            <td>Perihal</td>
                            <td>:</td>
                            <td><span class="fw-bold">Pemberitahuan Nomor Porsi Haji Plus</span></td>
                        </tr>
                        <tr>
                            <td>Lampiran</td>
                            <td>:</td>
                            <td>-</td>
                        </tr>
                    </table>
                </div>

                <div class="left mt-5">
                    <p><i>Assalamu’alaikum warahmatullahi wabarakatuh</i></p>
                    <p class="justify">
                        Dengan ini kami beritahukan Nomor Porsi Haji Plus untuk participant yang sudah mendaftar dan sudah didaftarkan ke Kementerian Agama RI, dengan participant sebagai berikut :
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
                            <td width="28%">No. KTP</td>
                            <td width="2%">:</td>
                            <td>{{ strtoupper($participant->nik) }}</td>
                        </tr>
                        <tr>
                            <td>Tempat/Tanggal Lahir</td>
                            <td>:</td>
                            <td class="uppercase">{{ucwords(strtolower($participant->birth_place)) }}, {{ \Carbon\Carbon::parse($participant->birth_date)->isoFormat('D MMMM Y') }}</td>
                        </tr>
                        <tr>
                            <td valign="top">Alamat</td>
                            <td valign="top">:</td>
                            <td class="uppercase">{{ $address }}</td>
                        </tr>
                        <tr>
                            <td>Agama</td>
                            <td>:</td>
                            <td class="uppercase"><span class="uppercase">Islam</span></td>
                        </tr>
                        <tr>
                            <td>Nomor Porsi</td>
                            <td>:</td>
                            <td class="uppercase">{{ $participantUmrohTrip->nomor_porsi }}</td>
                        </tr>
                    </table>
                </div>

                <div class="left mt-3">
                    <p class="justify">
                        Demikian surat pemberitahuan ini kami sampaikan, semoga bermanfaat.
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