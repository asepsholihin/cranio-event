<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">
        body {
            width: 100%;
            height: 100%;
            margin: auto;
            padding: 0;
            background-color: #ffffff;
            font-family: arial;
        }
        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }
        .page {
            position: relative;
            background-color: #ffffff;
            margin: auto;
        }
        .subpage {
            position: relative;
            padding: 0.2cm;
            z-index: 999;
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
        .display-inline {
            position: relative;
            width: 11.4cm;
            height: 7.1cm;
            margin-left: -3px;
            margin-bottom: 1px;
            display: inline-block;
            vertical-align: top;
        }
        .display-inline .wrapimage {
            width: 11.4cm;
            height: 7.1cm;
            position:relative;
        }
        .img-cover {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }
        .wrap-title p, .wrap-name p, .wrap-passport p {
            margin: 0;
            vertical-align: middle;
            line-height: normal;
        }
        .wrap-title {
            width: 300px;
            height: 75px;
            position: absolute;
            bottom: 0;
            top: 30px;
            left: 115px;
            right: 0;
            font-size: 14px;
            font-weight: bold;
        }
        .wrap-title .subtitle {
            font-size: 12px;
            font-weight: normal;
        }
        .wrap-name {
            width: 100%;
            height: 16px;
            position: absolute;
            bottom: 42px;
            margin:auto;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }
        .wrap-passport {
            width: 100%;
            height: 16px;
            position: absolute;
            bottom: 20px;
            margin:auto;
            font-size: 12px;
            text-align: center;
        }
        .wrap-barcode {
            width: 100px;
            position: absolute;
            bottom: 70px;
            margin: auto;
            left: 0;
            right: 0;
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

        .page_break {
            page-break-before: always;
        }
    </style>
    <title>{{ $umrohTrip->title }}</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                @foreach($participants as $participant)
                    <div class="display-inline">
                        <img class="wrapimage" src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/attendance_tag.png')))}}" alt="">
                        <div class="wrap-title">
                            <p>{{$participant->event_name}}</p>
                            <p class="subtitle">{{$participant->location}} - {{\Carbon\Carbon::parse($participant->event_date)->isoFormat('D MMMM Y')}}</p>
                        </div>
                        <div class="wrap-barcode">
                            <center>
                                @if($participant->barcode)
                                    {!!DNS2D::getBarcodeSVG($participant->barcode, 'QRCODE', 2.8, 2.8)!!}
                                @endif
                            </center>
                        </div>
                        <div class="wrap-name">
                            @php
                                $noUrut = "";
                                if($participant->no_urut) {
                                    $noUrut = str_pad($participant->no_urut, 2, 0, STR_PAD_LEFT);
                                }
                            @endphp
                            <p>{{$noUrut}} {{strtoupper($participant->name_in_passport??$participant->name)}}</p>
                        </div>
                        <div class="wrap-passport">
                            <p>NOMOR MEJA: {{$participant->manasik_table??'-'}}</p>
                        </div>
                    </div>
                @endforeach
            </div>    
        </div>
    </div>
</body>
</html>