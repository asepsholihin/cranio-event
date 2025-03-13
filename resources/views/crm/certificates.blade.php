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
            height: 1400px;
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

        p.indent {
            text-indent: 42px;
        }

        @page {
            size: A4;
            margin: 0;
        }

        @media print {

            html,
            body {
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
            top: 10.2cm;
            margin: auto;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 2.8em;
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
            top: 19.6cm;
            margin: auto;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 1.3em;
            font-style: italic;
            font-family: "Mulish";
        }

        .date-generated {
            position: absolute;
            top: 23cm;
            margin: auto;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 1.3em;
            font-style: italic;
            font-family: "Mulish";
        }
    </style>
    <title>SERTIFIKAT</title>
</head>

<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                <?php

                $name = $participant->name_in_certificate;
                if ($participant->name_in_certificate) {
                    $name = $participant->name_in_certificate;
                } else {
                    if ($participant->name_in_passport) {
                        $name = $participant->name_in_passport;
                    } else {
                        $name = $participant->name;
                    }
                }

                ?>
                <div class="main">
                    <div class="letter-bg">
                        <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_certificate.jpg')))}}" alt="">
                    </div>

                    <div class="name">{{ ($participant->front_title)?$participant->front_title.".":"" }} {{ strtoupper($name) }}{{ ($participant->back_title)?", ".$participant->back_title:"" }}</div>
                    <div class="title">{{ $umrohTrip->title_in_certificate }}</div>

                    @if(str_contains($participant->package_name, "Sapphire"))
                    <div class="star">
                        @for($i=1;$i<=5;$i++) <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/star.png')))}}" alt="">
                            @endfor
                    </div>
                    @elseif(str_contains($participant->package_name, "Emerlad"))
                    <div class="star">
                        @for($i=1;$i<=4;$i++) <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/star.png')))}}" alt="">
                            @endfor
                    </div>
                    @elseif(str_contains($participant->package_name, "Ruby"))
                    <div class="star">
                        @for($i=1;$i<=3;$i++) <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/star.png')))}}" alt="">
                            @endfor
                    </div>
                    @endif
                    <div class="date-umroh">
                        Pada Tanggal {{ \Carbon\Carbon::parse($umrohTrip->departure_at)->isoFormat('D') }} - {{ \Carbon\Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y') }}
                    </div>
                    <div class="date-generated">
                        Tangerang Selatan, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}
                    </div>
                </div>

                <div class="page_break"></div>
            </div>
        </div>
    </div>
</body>

</html>