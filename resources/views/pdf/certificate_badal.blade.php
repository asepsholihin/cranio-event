<?php
use App\Models\Package;
use App\Support\General;
use Carbon\Carbon;
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
        @font-face {
            font-family: 'Mulish-ExtraBold';
            src: url({{ storage_path('private_assets/fonts/Mulish-ExtraBold.ttf') }}) format("truetype");
        }
        @font-face {
            font-family: 'Mulish-Bold';
            src: url({{ storage_path('private_assets/fonts/Mulish-Bold.ttf') }}) format("truetype");
        }
        @font-face {
            font-family: 'Martel';
            src: url({{ storage_path('private_assets/fonts/Martel-Bold.ttf') }}) format("truetype");
        }
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #fff;
            font: 12pt "Mulish";
        }
        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        .page {
            position: relative;
            background-color: #fff;
        }
        .letter-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1000;
            background-color: #fff;
        }
        .letter-bg img {
            height: 1399px; 
        }
        .subpage {
            position: relative;
            height: 29.7cm;
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
            margin-top: 10px;
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
                width: 21cm;
                height: 29.7cm;         
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

        .name {
            position: absolute;
            top: 14cm;
            width: 20cm;
            margin: auto;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 2.8em;
            line-height: 1.2em;
            font-weight: bold;
            font-family: "Martel";
        }

        .title {
            position: absolute;
            top: 14.8cm;
            margin: auto;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 2.8em;
            font-weight: bold;
            font-family: "Mulish-ExtraBold";
        }

        .main {
            position: relative;
            height: 29.7cm;
            display: block;
        }

        .page_break {
            page-break-before: always;
        }

        .star {
            position: absolute;
            width: 13cm;
            top: 17cm;
            margin: auto;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 1.4em;
            font-weight: bold;
            font-family: "Mulish";
        }
        .star img {
            width: 64px;
            height: 64px;
        }
        .date-umroh {
            position: absolute;
            top: 18cm;
            width: 20cm;
            margin: auto;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 1.2em;
            font-style: italic;
            font-family: "Mulish";
        }
        .date-generated {
            position: absolute;
            top: 22.3cm;
            margin: auto;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 1.2em;
            font-style: italic;
            font-family: "Mulish";
        }
        .signatur {
            position: absolute;
            width: 12cm;
            top: 27.6cm;
            left: 2.5cm;
            right: 0;
            text-align: center;
            font-size: 1.2em;
            font-weight: bold;
            font-family: "Mulish-Bold";
        }

    </style>
    <title>SERTIFIKAT</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                @foreach($badals as $badal)
                <?php 
                    $participants = json_decode($badal->participant_badal);

                    $tanggalHajiHijriah = General::tanggalHijriah($badal->badal_date, true);
                    $tanggalHajiMasehi = Carbon::parse($badal->badal_date)->isoFormat('D MMMM Y');
                ?>
                    @foreach($participants as $participant)
                    <?php 
                        $title = $participant->reason;
                        $mid = "Binti";
                        if($participant->reason == "Sakit atau Uzur") {
                            $title = "";
                        }
                        if($participant->gender_in_badal == 1) {
                            $mid = "Bin";
                        }

                        $fullname = $title ." ". $participant->name_in_badal ." ". $mid ." ". $participant->father_name_in_badal; 
                        $description = "Pada jam " . " waktu Makkah tanggal " . $tanggalHajiMasehi ."/". $tanggalHajiHijriah . 
                        " dengan pelaksana badal umrah Ustadz " . $badal->badal_by_name;

                        $space = "0";
                        $fontSize = "2.8em";
                        $length = strlen($fullname);
                        if($length >= 29 && $length < 50) {
                            $fontSize = "2.2em";
                            $space = "16px";
                        }
                        if($length >= 50) {
                            $fontSize = "1.8em";
                            $space = "24px";
                        }
                    ?>
                    <div class="main">
                        <div class="letter-bg">
                            <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_certificate_badal.jpg')))}}" alt="">
                        </div>

                        <div class="name" style="font-size:{{$fontSize}};margin-top:{{$space}};">{{ $fullname }}</div>
                        
                        <div class="date-umroh">
                            {{ $description }}
                        </div>

                        <div class="date-generated">
                            Tangerang Selatan, {{ Carbon::now()->isoFormat('D MMMM Y') }}
                        </div>

                        <div class="signatur">
                            {{ $badal->badal_by_name }}
                        </div>
                    </div>
                    
                    <div class="page_break"></div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>