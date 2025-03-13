<?php
use Carbon\Carbon;
use App\Support\NumberFormat;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">
        @@font-face {
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
    <title>SURAT PERSETUJUAN</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="letter-bg">
                <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_letter_ppiu.png')))}}" alt="">
            </div>
            <div class="subpage">
                <div class="center">
                    <h4 class="mt-5 mb-0 underline">SURAT PERSETUJUAN</h4>
                </div>
                
                <div class="left mt-5">
                    <p>Pada hari ini {{ $hari_ini }} Tanggal {{ $ejaan_tanggal }} {{ $bulan_ini }} {{ $ejaan_tahun }}, Saya yang bertanda tangan di bawah ini:</p>
                    <table width="100%" style="margin-left:24px">
                        <tr>
                            <td width="28%">Nama</td>
                            <td width="2%">:</td>
                            <td class="uppercase">{{ strtoupper($participant->name) }}</td>
                        </tr>
                        <tr>
                            <td width="28%">Umur</td>
                            <td width="2%">:</td>
                            <td class="uppercase">{{ Carbon::parse($participant->birth_date)->age }} tahun</td>
                        </tr>
                        <tr>
                            <td>Pekerjaan/Jabatan</td>
                            <td>:</td>
                            <td class="uppercase">{{ $participant->job }}</td>
                        </tr>
                        <tr>
                            <td valign="top">Alamat</td>
                            <td valign="top">:</td>
                            <td class="uppercase">{{ $address }}</td>
                        </tr>
                        <tr>
                            <td>Telepon.</td>
                            <td>:</td>
                            <td class="uppercase">{{ $participant->no_hp }}</td>
                        </tr>
                    </table>
                </div>

                <div class="left mt">
                    <ol>
                        <li class="justify">
                            Bahwa dalam rangka proses izin Penyelenggara Ibadah Haji Khusus Kementerian Agama RI, PT Jejak Imani Berkah Bersama menyelenggarakan program Ibadah Haji Khusus dengan izin Penyelenggara Ibadah Haji Khusus Kementerian Agama RI No. 394 tahun 2021.
                        </li>
                        <li class="justify">
                            Menyetujui untuk melakukan pendaftaran Ibadah Haji Khusus melalui PT Jejak Imani Berkah Bersama.
                        </li>
                        <li class="justify">
                            Jemaah tetap akan berangkat dengan PT Jejak Imani Berkah Bersama pada waktu keberangkatan yang sudah ditentukan sesuai dengan nomor porsi Haji Khusus dari Kementerian Agama RI.
                        </li>
                    </ol>
                </div>

                <div class="left mt-3">
                    <p class="justify">
                    Demikianlah persetujuan ini saya buat dengan sesungguhnya, agar dapat dipergunakan sebagaimana mestinya.
                    </p>
                </div>

                <div class="left mt-5">
                    <p>Tangerang Selatan, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                    <p>Saya yang menyatakan,<br>Jemaah Haji Khusus</p>
                    <div>
                        <br><br>
                        <p>Materai 10.000</p>
                        <br>
                        <p class="uppercase">{{ strtoupper($participant->name) }}</p>
                    </div>
                </div>
            </div>    
        </div>
    </div>
</body>
</html>