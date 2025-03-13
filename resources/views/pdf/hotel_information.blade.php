<?php
use App\Support\General;

$color = 'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/header_hotel_info_ruby.png')));
if ($hotel->document_style == "ruby") {
    $color = 'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/header_hotel_info_ruby.png')));
}
if ($hotel->document_style == "emerald") {
    $color = 'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/header_hotel_info_ruby.png')));
}
if ($hotel->document_style == "sapphire") {
    $color = 'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/header_hotel_info_sapphire.png')));
}
if ($hotel->document_style == "lebih_hemat") {
    $color = 'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/header_hotel_info_onyx.png')));
}

$informations = json_decode($hotel->informations);
$images = json_decode($hotel->images);
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
            font-family: 'Mulish-Bold';
            src: url({{ storage_path('private_assets/fonts/Mulish-Bold.ttf') }}) format("truetype");
        }
        body {
            width: 14.8cm;
            height: 21cm;
            margin: 0;
            padding: 0;
            background-color: #fff;
            font-size: 12pt;
        }
        @page { margin:0px; padding:0px; }
        html { margin:0px; padding:0px; }
        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
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
            height: 4cm; 
        }
        .subpage {
            position: relative;
            width: 14.8cm;
            height: 21cm;
            z-index: 999;
        }
        .font-bold {
            font-weight: bold;
            font-family: 'Mulish-Bold' !important;
        }
        
        .main {
            position: relative;
            width: 14.8cm;
            height: 21cm;
            display: block;
            margin: auto;
        }
        .letter-bg {
            padding: 0.5cm 1cm;
            height: 4cm;
            position: relative;
            background-position: left center;
            background-size: cover;
            background-repeat: no-repeat;
        }
        .footer {
            position: absolute;
            left: 12px;
            bottom: 12px;
            font-size: 11px;
            font-family: 'Mulish';
        }
        img.logo {
            width: 2.5cm;
            height: 2.5cm;
        }
        .city-name {
            font-family: 'Mulish';
            font-size: 20pt;
            color: #fff;
        }
        .hotel-name {
            font-weight: bold;
            font-family: 'Mulish-Bold';
            font-size: 20pt;
            color: #fff;
        }
        .logo-wrapper {
            position: absolute;
        }
        .hotel-wrapper {
            position: absolute;
            width: calc(100% - 4cm);
            left: 4.5cm;
            padding: 0.5cm 0;
            text-align: center;
        }
        .side-wrapper {
            position: relative;
            height: calc(100% - 4cm);
        }
        .left-side {
            position: absolute;
            left: 0;
            width: 8.3cm;
            height: 100%;
            padding-top: 24px;
        }
        .right-side {
            position: absolute;
            right: 0;
            width: 6.5cm;
            height: 100%;
        }
        .content {
            margin: 6px 0 0;
        }
        .ribbon {
            font-family: 'Mulish';
            font-size: 13pt;
            background: #dabb6c;
            padding: 12px;
            width: 240px;
            border-radius: 0 46px 46px 0;
        }
        .body {
            font-family: 'Mulish';
            padding: 12px;
        }
        .body p {
            margin-top: 0; 
            margin-bottom: 12px;   
        }
        .hotel-image-wrapper {
            position: relative;
            width: 6.5cm;
        }
        .hotel-image {
            width: 6.5cm;
            height: auto;
            max-height: 300px;
            object-fit: cover;
        }
        .ribbon-image {
            position: absolute;
            bottom: 12px;
            right: 12px;
            font-family: 'Mulish';
            font-size: 9pt;
            color: #fff;
            background: #231f20;
            border: 2px solid #dabb6c;
            padding: 4px 18px;
            width: auto;
            border-radius: 46px;
        }

    </style>
    <title>SERTIFIKAT</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                <div class="main">
                    <div class="letter-bg" style="background-image: url('{{ $color }}');">
                        <div class="hotel-wrapper">
                            <div class="city-name">Informasi Hotel {{ ucwords(strtolower($hotel->city)) }}</div>
                            <div class="hotel-name">{{ ucwords(strtolower($hotel->name)) }}</div>
                        </div>
                    </div>
                    <div class="side-wrapper">
                        <div class="left-side">
                            @foreach($informations as $item)
                                <?php
                                    $space = "0";
                                    $fontSize = "12pt";
                                    $length = strlen($item->content);
                                    if($length >= 1 && $length < 10) {
                                        $fontSize = "16pt";
                                        $space = "0";
                                    }
                                    if($length > 50) {
                                        $fontSize = "12pt";
                                        $space = "0";
                                    }
                                ?>
                                <div class="content">
                                    <div class="ribbon">{{ $item->title }}</div>
                                    <div class="body" style="font-size:{{$fontSize}};margin-top:{{$space}};">
                                        {!! $item->content !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="right-side">
                            @foreach($images as $item)
                                <?php 
                                    $image = str_replace('data:image/jpeg;base64,', '', $item->url);
                                    $image = str_replace(' ', '+', $image);
                                    $imageName = 'asdasd.'.'jpg';
                                ?>
                                <div class="hotel-image-wrapper">
                                    <img src="{!! $item->url !!}" class="hotel-image">
                                    <div class="ribbon-image">
                                        {{ $item->title  }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="footer">
                        Jejak Imani @ <?= date('Y') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>