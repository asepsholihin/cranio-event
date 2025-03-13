<?php
use App\Support\NumberFormat;
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

        body {
            width: 100%;
            height: 100%;
            margin: auto;
            padding: 0;
            background-color: #ffffff;
            font-family: 'Mulish';
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
            padding: 0cm;
            z-index: 999;
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

        .wrap-content {
            display: inline;
        }

        .display-inline {
            position: relative;
            width: 6.9cm;
            height: 23cm;
            margin-right: -3px;
            display: inline-block;
            vertical-align: top;
        }

        .travel-info {
            width: 6.9cm;
            height: 10.5cm;
            border: 2px solid #ccc;
            text-align: center;
            padding: 16px;
            margin-top: 1.3cm;
        }

        .wrapimage {
            width: 6.9cm;
            height: 10.5cm;
            margin-bottom: 4px;
            position: relative;
            border: 2px solid #ccc;
        }

        .wrapimage-rotate {
            position: absolute;
            top: 0;
            left: 0;
        }
        .wrapimage-rotate img {
            width: 6.9cm;
            height: 10.5cm;
            margin-bottom: 4px;
            position: relative;
            border: 2px solid #ccc;
        }

        .img-cover {
            height: 100%;
            width: 100%;
            object-fit: scale-down;
        }

        .wrap-avatar {
            width: 142px;
            height: 184px;
            position: absolute;
            bottom: 0;
            top: 5cm;
            right: 60px;
            margin: 0 auto;
        }

        .wrap-avatar .container-svg {
            position: absolute;
            display: table;
            width: 142px;
            height: 184px;
        }

        .wrap-avatar .subcontainer-svg {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .wrap-avatar .img-svg {
            height: auto;
            width: 100%;
            height: 160px;
        }

        .wrap-avatar-rotate {
            width: 75px;
            height: 90px;
            position: absolute;
            bottom: 0;
            top: 30px;
            right: 32px;
            margin: 0 auto;
        }
        .wrap-avatar-rotate .container-svg {
            position: absolute;
            display: table;
            width: 75px;
            height: 90px;
        }

        .wrap-avatar-rotate .subcontainer-svg {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .wrap-avatar-rotate .img-svg {
            height: auto;
            width: 100%;
            height: 86px;
        }

        .wrap-name p {
            margin: 0;
            display: inline-block;
            vertical-align: middle;
            line-height: normal;
        }

        .text-white {
            color: #fff !important;
        }

        .travel-info h2 {
            font-size: 13px;
        }

        .travel-info p.small {
            font-size: 8px;
            margin: 0;
        }

        .travel-info p {
            font-size: 8px;
            margin: 0;
        }

        .travel-info p span.strong {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        @page {
            size: A4;
            margin: 0;
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

        .wrapper-rotate {
            position: absolute;
            top: 12cm;
            left: 0;
            right: 0;
            bottom: 0;
            height: 10.5cm;
            padding: 24px 16px;
            -moz-transform: rotate(180.0deg);
            /* FF3.5+ */
            -o-transform: rotate(180.0deg);
            /* Opera 10.5 */
            -webkit-transform: rotate(180.0deg);
            /* Saf3.1+, Chrome */
            filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=0.083);
            /* IE6,IE7 */
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=0.083)";
            /* IE8 */
        }

        .barcode {
            margin-top: 2px;
        }

        .color-primary {
            color: #f1b319;
        }

        .wrap-name {
            position: absolute;
            top: 2.6cm;
            margin: auto;
            left: 26px;
            right: 26px;
            font-size: 14px;
            color: #2b3c88;
        }

        .wrap-name-rotate {
            position: absolute;
            top: 3.7cm;
            margin: auto;
            left: 0;
            right: 0;
            text-align: left;
            padding-left: 20px;
            font-size: 11px;
        }

        .title-trip {
            margin-top: 10px;
            margin-left: 18px;
            margin-right: 18px;
        }
        
        .text-small {
            font-size: 10px;
        }
        
        .text-large {
            font-size: 14px;
        }
        
        .text-bold {
            font-family: 'Mulish-Bold'
        }
        
        .text-extrabold {
            font-family: 'Mulish-ExtraBold'
        }

        .text-white {
            color: #fff;
        }

        .text-black {
            color: #000;
        }
    </style>
    <title>{{ $umrohTrip->title_in_idcard ?? $umrohTrip->title }}</title>
</head>

<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                <div class="wrap-content">
                    @foreach ($participants as $participant)
                        <div class="display-inline">
                            @if ($participant->crew == 'Tour Leader')
                                @php $classColor = "text-white"; @endphp
                                <img class="wrapimage"
                                    src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/id_card_jelajah_dunya.png'))) }}"
                                    alt="">
                            @elseif($participant->crew == 'Mutawwif')
                                @php $classColor = "text-white"; @endphp
                                <img class="wrapimage"
                                    src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/id_card_jelajah_dunya.png'))) }}"
                                    alt="">
                            @else
                                @php $classColor = "text-white"; @endphp
                                <img class="wrapimage"
                                    src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/id_card_jelajah_dunya.png'))) }}"
                                    alt="">
                            @endif

                            <div class="wrap-name">
                                <div class="text-extrabold text-large">{{ strtoupper($participant->name_in_passport ?? $participant->name) }}</div>
                                {{ strtoupper($participant->no_passport) }}
                            </div>

                            <div class="wrap-avatar">
                                <div class="container-svg">
                                    <div class="subcontainer-svg">
                                        <div style="
                                            background-image: url({{$participant->profile_thumbnail}});
                                            background-size: contain;
                                            background-position: center center;
                                            background-repeat: no-repeat;
                                            width: 100%;
                                            height: 184px;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="travel-info">
                                <div class="wrapper-rotate">
                                    <div class="wrapimage-rotate">
                                        <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/id_card_jelajah_dunya_back.png'))) }}"
                                            alt="">
                                    </div>

                                    <div class="wrap-avatar-rotate">
                                        <div class="container-svg">
                                            <div class="subcontainer-svg">
                                                <div style="
                                                    background-image: url({{$participant->profile_thumbnail}});
                                                    background-size: contain;
                                                    background-position: center center;
                                                    background-repeat: no-repeat;
                                                    width: 75px;
                                                    height: 90px;"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="wrap-name-rotate {{ $classColor }}">
                                        <div class="text-bold">
                                            <table>
                                                <tr>
                                                    <td colspan="3">Nama : {{ ucwords(strtolower($participant->name_in_passport ?? $participant->name)) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>No. Passport</td>
                                                    <td>:</td>
                                                    <td>{{ strtoupper($participant->no_passport) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tour Leader</td>
                                                    <td>:</td>
                                                    <td>{{ ucwords(strtolower($participant->tour_leader)) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>No. Indonesia</td>
                                                    <td>:</td>
                                                    <td>{{ NumberFormat::formatPhoneNumberIndonesia($participant->tour_leader_phone) }}</td>
                                                </tr>
                                            </table>
                                        </div>

                                        <div class="text-bold" style="margin-top: 6px;">
                                            Hotel
                                            <table>
                                                @foreach($hotels as $hotel)
                                                <tr>
                                                    <td>&#8226; {{ $hotel->city_name }}</td>
                                                    <td>:</td>
                                                    <td>{{ $hotel->hotel_name }}</td>
                                                </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</body>

</html>
