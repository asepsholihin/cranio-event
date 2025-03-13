<?php
use Illuminate\Support\Str;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">
        @font-face {
            font-family: 'Mulish-Bold';
            src: url({{ storage_path('private_assets/fonts/Mulish-ExtraBold.ttf') }}) format("truetype");
        }

        body {
            width: 100%;
            height: 100%;
            margin: auto;
            padding: 0;
            background-color: #ffffff;
            font-family: 'Mulish-Bold';
            font-size: 30px;
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

        .wrapper {
            position: relative;
        }

        .image {
            width: 1080px;
        }

        .wrap-caption {
            z-index: 999;
            position: absolute;
            width: 1080px;
            bottom: 19%;
            padding-left: 5%;
            padding-right: 5%;
            color: white;
        }
        .wrap-caption .name {
            margin: auto;
            left: 0;
            right: 0;
            text-align: center;
            color: white;
            position: absolute;
            font-size: 100%;
            font-weight: bold;
            text-shadow: 0px 4px 6px #000;
        }
        .wrap-caption .info {
            position: absolute;
            margin: auto;
            left: 0;
            right: 0;
            text-align: center;
            padding-top: 4.5%;
            font-size: 60%;
            font-weight: bold;
            text-shadow: 0px 4px 6px #000;
        }

        @media print {

            html,
            body {
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
    <title>{{ $mitra->name??'' }}</title>
</head>

<body>
    <div class="book">
        <div class="page">
            <div class="wrapper">
                <img class="image" src="{{ $media->image }}" alt="">
                @if($mitra)
                <div class="wrap-caption">
                    <div class="name">{{ $mitra->name }}</div>
                    <div class="info">@if($mitra->instagram) {{$mitra->instagram}} &#8226; @endif {{ Str::replaceFirst('62', '0', $mitra->no_hp) }} &#8226; {{ $mitra->email }} </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>

</html>
