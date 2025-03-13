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
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 10mm auto;
            background-color: #ffffff;
        }
        .letter-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            padding: 20px;
        }
        .letter-bg img {
            width: 210mm;
            min-height: 297mm;
        }
        .subpage {
            position: relative;
            height: 235mm;
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
        .wrap-content {
            display: inline;
        }
        .display-inline {
            position: relative;
            width: 123px;
            height: 218px;
            margin-bottom: 3px;
            padding: 4px;
            display: inline-block;
            border: 1px solid;
            vertical-align: top;
        }
        .wrapimage {
            height: 162px;
            width: 113px;
            margin-bottom: 4px;
            position:relative;
        }
        .img-cover {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }
        .wrap-name {
            height: 46px;
            line-height: 46px;
            position: absolute;
            bottom: 0;
            text-align: center;
            left: 0;
            right: 0;
            padding: 0 4px;
        }
        .wrap-name p {
            margin: 0;
            display: inline-block;
            vertical-align: middle;
            line-height: normal;
        }
        .wrap-crew {
            font-size: 10px;
            text-align: center;
        }

        .card {
            height: 162px;
            width: 113px;
            position: relative;
            overflow: hidden;
            float: left;
        }
        .card img {
            top: 50%;
            left: 50%;
            position: relative;
            transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            -webkit-transform: translate(-50%, -50%);
        }
        .card.vertical {
            height: 162px;
            width: 113px;
            margin-bottom: 4px;
        }

        .card.vertical img {
            height: 100%;
            width: auto;
            min-width: 113px;
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
    </style>
    <title>{{ $umrohTrip->title }}</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                <?php
                $title = strtoupper("Daftar Foto Participant " . $umrohTrip->title);
                ?>
                <center><h2 class="uppercase">{{ $title }}</h2></center>
                <br><br>
                <div class="wrap-content">
                @foreach($participants as $participant)
                    <div class="display-inline">
                        <div class="wrap-crew">
                            {{ $participant->crew }}
                        </div>
                        <div class="card vertical">
                            <img src="{{$participant->profile_thumbnail}}" />
                        </div>
                        <div class="wrap-name">
                            <p>{{($participant->no_urut)?$participant->no_urut.'. ':''}}{{strtoupper($participant->name_in_passport??$participant->name)}}</p>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>    
        </div>
    </div>
</body>
</html>