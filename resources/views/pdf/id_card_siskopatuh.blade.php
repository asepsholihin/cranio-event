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
            font-family: 'Martel';
            src: url({{ storage_path('private_assets/fonts/Martel-Bold.ttf') }}) format("truetype");
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
            border: 4px solid #555;
            text-align: center;
            padding: 16px;
            margin-top: 1.3cm;
        }

        .wrapimage {
            width: 6.9cm;
            height: 10.5cm;
            margin-bottom: 4px;
            position: relative;
            border: 4px solid #555;
        }

        .img-cover {
            height: 100%;
            width: 100%;
            object-fit: scale-down;
        }

        .wrap-avatar {
            width: 100px;
            height: 130px;
            position: absolute;
            bottom: 0;
            top: 92px;
            left: 0;
            right: 0;
            margin: 0 auto;
        }

        .container-svg {
            position: absolute;
            display: table;
            width: 98px;
            height: 130px;
        }

        .subcontainer-svg {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .img-svg {
            height: auto;
            width: 100%;
            max-height: 130px;
        }

        .wrap-name p,
        .wrap-info p,
        .wrap-title p {
            margin: 0;
            display: inline-block;
            vertical-align: middle;
            line-height: normal;
        }

        .wrap-title {
            height: 48px;
            position: absolute;
            bottom: 0;
            top: 18px;
            left: 0;
            right: 0;
            margin: 0 auto;
            text-align: center;
        }

        .wrap-title h3 {
            font-size: 1em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .wrap-title p {
            font-size: 7px;
            font-weight: bold;
            margin: 0;
            line-height: 5px;
        }

        .wrap-title p.small {
            display: block;
            font-size: 6px;
            font-style: italic;
            font-weight: normal;
            color: #f0b21e;
        }

        .wrap-info {
            position: absolute;
            top: 226px;
            left: 0;
            right: 0;
            margin: 0 auto;
            font-size: 8px;
            text-align: center;
        }

        .wrap-info p {
            display: block;
        }

        .wrap-info p span.strong {
            font-weight: bold;
        }

        .wrap-info p span.italic {
            font-style: italic;
        }

        .wrap-footer {
            position: absolute;
            bottom: 360px;
            width: 100%;
            padding: 6px;
            text-align: center;
            border-top: 2px solid #d70909;
        }

        .wrap-footer p {
            font-size: 6px;
            margin: 0;
        }

        .wrap-bus {
            position: absolute;
            width: 50px;
            height: 24px;
            top: 182px;
            left: 5px;
            text-align: center;
        }

        .wrap-bus p {
            font-size: 10px;
            display: inline-block;
            vertical-align: middle;
            line-height: normal;
            font-weight: bold;
        }

        .wrap-bus p span.big {
            font-size: 16px;
            font-weight: bold;
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

        .tahun {
            position: absolute;
            top: 30px;
            margin: auto;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
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
                                    src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/id_card_siskopatuh_leader.png'))) }}"
                                    alt="">
                            @elseif($participant->crew == 'Mutawwif')
                                @php $classColor = "text-white"; @endphp
                                <img class="wrapimage"
                                    src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/id_card_siskopatuh_mutawwif.png'))) }}"
                                    alt="">
                            @else
                                @php $classColor = "text-black"; @endphp
                                <img class="wrapimage"
                                    src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/id_card_siskopatuh.png'))) }}"
                                    alt="">
                            @endif

                            <div class="tahun {{ $classColor }}">
                                {{ General::tahunHijriah() }}
                            </div>

                            <div class="wrap-avatar">
                                <div class="container-svg">
                                    <div class="subcontainer-svg">
                                        <img class="img-svg" src="{{$participant->profile_thumbnail}}"  />
                                    </div>
                                </div>
                            </div>
                            <div class="wrap-bus">
                                <p @if ($participant->crew == 'Tour Leader' || $participant->crew == 'Mutawwif') class="text-white" @endif>BUS <span
                                        class="big">{{ $participant->group_bus }}</span></p>
                            </div>
                            <div class="wrap-info">
                                <p><spanclass="strong">{{ strtoupper($participant->name_in_passport ?? $participant->name) }}</span></p>
                                <p><span class="italic">PASSPORT:</span> <span class="strong">{{ strtoupper($participant->no_passport) }}</span></p>

                                <div class="barcode">
                                    @if ($participant->code_siskopatuh)
                                        <center>
                                            <p><span class="strong">{{ $participant->code_siskopatuh }}</span></p><br>
                                            {!! DNS2D::getBarcodeSVG($participant->siskopatuh_id, 'QRCODE', 2.7, 2.7) !!}
                                        </center>
                                    @endif
                                </div>
                            </div>

                            <div class="travel-info">
                                <div class="wrapper-rotate">
                                    <h2>PT. JEJAK IMANI BERKAH BERSAMA</h2>
                                    <p style="margin-bottom: 16px">Jejak Imani Lounge, Intermark Indonesia Ruko No.
                                        9-10, Jl. Lingkar Timur BSD -
                                        Kota Tangerang Selatan 15310 Indonesia, Telp. (+62) 811 9184 376, Email:
                                        jejakimani@gmail.com</p>
                                    <table width="100%">
                                        <tr>
                                            <td class="text-center" valign="top">
                                                <p class="small">Tour Leader</p>
                                                <p><span class="strong">{{ strtoupper($participant->tour_leader) }}</span>
                                                </p>
                                                <p><span class="strong">+{{ $participant->tour_leader_phone }}</span></p>
                                                <br>
                                            </td>
                                            <td class="text-center" valign="top">
                                                <p class="small">Muthawif</p>
                                                <p><span class="strong">{{ strtoupper($participant->mutawwif) }}</span></p>
                                                <p><span class="strong">+{{ $participant->mutawwif_phone }}</span></p>
                                                <br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <table width="100%">
                                                    <tr>
                                                        <td width="25%"></td>
                                                        <td>
                                                            @if ($participant->runner)
                                                                <p class="small">Runner</p>
                                                                <p><span
                                                                        class="strong">{{ strtoupper($participant->runner) }}</span>
                                                                </p>
                                                                <p><span
                                                                        class="strong">+{{ $participant->runner_phone }}</span>
                                                                </p>
                                                            @else
                                                                <br>
                                                            @endif
                                                        </td>
                                                        <td width="25%"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center" valign="top">
                                                <p class="small">Hotel Makkah</p>
                                                <p><span class="strong">{!! preg_replace(
                                                    '/[\x{2B50}]/u',
                                                    "<span class='color-primary'>&#9733;</span>",
                                                    strtoupper($participant->hotel_makkah_selected),
                                                ) !!}</span></p>
                                                <p><span class="strong"></span></p>
                                            </td>
                                            <td class="text-center" valign="top">
                                                <p class="small">Hotel Madinah</p>
                                                <p><span class="strong">{!! preg_replace(
                                                    '/[\x{2B50}]/u',
                                                    "<span class='color-primary'>&#9733;</span>",
                                                    strtoupper($participant->hotel_madinah_selected),
                                                ) !!}</span></p>
                                                <p><span class="strong"></span></p>
                                            </td>
                                        </tr>
                                    </table>
                                    <br>
                                    <p>PT. JEJAK IMANI BERKAH BERSAMA</p>
                                    <p>HP: (+62) 811 9184 376</p>
                                    <p><span class="strong">Kantor Perwakilan:</span></p>
                                    <table width="100%">
                                        <tr>
                                            <td class="text-center">
                                                <p><span class="strong">Makkah</span></p>
                                                <p>{{ $umrohTrip->pic_mekkah }}</p>
                                                <p class="small">Saudi Arabia</p>
                                                <p>+{{ $umrohTrip->pic_mekkah_phone_number }}</p>
                                            </td>
                                            <td class="text-center">
                                                <p><span class="strong">Madinah</span></p>
                                                <p>{{ $umrohTrip->pic_madinah }}</p>
                                                <p class="small">Saudi Arabia</p>
                                                <p>+{{ $umrohTrip->pic_madinah_phone_number }}</p>
                                            </td>
                                        </tr>
                                    </table>
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
