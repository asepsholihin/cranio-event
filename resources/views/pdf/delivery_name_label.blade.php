<?php
use App\Support\NumberFormat;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">
        @font-face {
            font-family: 'Mulish-ExtraBold';
            src: url({{ storage_path('private_assets/fonts/Mulish-ExtraBold.ttf') }}) format("truetype");
        }

        body {
            width: 100%;
            height: 100%;
            margin: auto;
            padding: 0;
            background-color: #ffffff;
            font-family: 'Mulish-ExtraBold';
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
        .letter-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }
        .letter-bg img {
            width: 100%;
            max-height: 3cm;
        }
        .subpage {
            position: relative;
            padding: 0cm;
            z-index: 999;
        }
        .wrap-content {
            display: inline;
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

        table.table tr td {
            padding: 4px 0;
        }
        
        @page {
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
        .card {
            width: 100mm;
            height: 60mm;
            margin-bottom: 0;
            position:relative;
            border: 1px solid #ccc;
            display: inline-block;
            font-size: 11px;
        }
        .card .wrapimage {
            width: 100mm;
            height: 62mm;
            position: absolute;
            margin: auto;
            top: 0;
            left: 0;
        }
        .wrap-name {
            position: absolute;
            margin: auto;
            top: 15px;
            left: 20px;
            right: 0;
            font-weight: bold;
            font-family: "Mulish-ExtraBold";
            font-size: 12px;
        }
        /* .wrap-name[data-contentlength="0"]{ font-size: 26px; }
        .wrap-name[data-contentlength="1"]{ font-size: 20px; }
        .wrap-name[data-contentlength="2"]{ font-size: 18px; } */
        .nametext[data-contentlength="0"]{ font-size: 13px; }
        .nametext[data-contentlength="0.5"]{ font-size: 12px; }
        .nametext[data-contentlength="1"]{ font-size: 10px; }
        .nametext[data-contentlength="1.5"]{ font-size: 10px; }
        .nametext[data-contentlength="2"]{ font-size: 9px; }
    </style>
    <title>Delivery Name Label {{ $equipmentDelivery->name }}</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                <div class="wrap-content">
                    <?php 
                    $length = strlen($equipmentDelivery->name ?? $equipmentDelivery->name_in_passport); 
                    $c_length = 0;
                    if($length < 26) {
                        $c_length = 0;
                    }
                    if($length >= 26 && $length < 34) {
                        $c_length = 1;
                    }
                    if($length >= 34 && $length < 42) {
                        $c_length = 1.5;
                    }
                    ?>
                    <div class="card">
                        <img class="wrapimage" src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/delivery_name_label.png')))}}" alt="">
                        <div class="wrap-name">
                            <p><span style="color:#777;">NAME:</span> <span class="nametext" data-contentlength="{{ $c_length }}">{{strtoupper($equipmentDelivery->name ?? $equipmentDelivery->name_in_passport)}}</span></p>
                            <p><span style="color:#777;">PASSPORT:</span> {{strtoupper($equipmentDelivery->no_passport)}}</p>
                            <p><span style="color:#777;">AGE:</span> {{strtoupper($equipmentDelivery->age)}}</p>
                            <p><span style="color:#777;">GENDER:</span> {{strtoupper(($equipmentDelivery->gender==1) ? 'Man' : 'Woman' )}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
