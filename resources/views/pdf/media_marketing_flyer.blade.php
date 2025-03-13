<?php
use App\Models\Package;
use App\Support\General;
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
            width: 1280px;
            height: 1600px;
            background-size: contain;
            background-repeat: no-repeat;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1000;
            background-color: #fff;
        }
        .subpage {
            position: relative;
            height: 1600px;
            z-index: 999;
        }

        @page {
            margin: 0;
        }
        @media print {
            html, body {
                width: 1280px;
                height: 1600px;
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

        .main {
            position: relative;
            height: 1600px;
            display: block;
        }

        .page_break {
            page-break-before: always;
        }

        .name {
            position: absolute;
            bottom: 150px;
            text-align: center;
            width: 100%;
            font-family: 'Mulish-Bold';
        }

    </style>
    <title>{{ $mediaMarketing->title }}</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                @foreach($images as $key => $image)
                
                <div class="main">
                    <div class="letter-bg" style="background-image:url('{{ $image->image_url }}')">
                    </div>

                    @if($custom_name)
                        <div class="name">
                            <h1>www.jejakimani.com | {{ $custom_name }} ({{ $custom_no_hp }})</h1>
                        </div>
                    @else
                        @if($key == 0 && $sales)
                        <div class="name">
                            <h1>www.jejakimani.com | {{ $sales->whatsapp_number }} ({{ $sales->name }})</h1>
                        </div>
                        @endif
                    @endif
                </div>
                
                <div class="page_break"></div>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>