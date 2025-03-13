<?php
use App\Support\NumberFormat;
use App\Models\Package;
?>

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
            padding: 0cm;
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
            width: 6.7cm;
            height: 10.5cm;
            margin-right: -3px;
            margin-bottom: 1px;
            display: inline-block;
            vertical-align: top;
        }
        .wrapimage {
            width: 6.7cm;
            height: 10.5cm;
            position:relative;
            border: 2px solid #555;
        }
        .img-cover {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }
        .wrap-avatar {
            width: 58px;
            height: 65px;
            position: absolute;
            bottom: 0;
            top: 54px;
            left: 100px;
            right: 0;
        }
        .container-svg { 
            position: absolute;
            display: table;
            width: 58px;
            height: 65px;
        }
        .subcontainer-svg {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        .img-svg {
            height: auto;
            width: 100%;
            max-height: 65px;
            object-fit: scale-down;
        }
        .wrap-name p, .wrap-info p, .wrap-room p {
            margin: 0;
            display: inline-block;
            vertical-align: middle;
            line-height: normal;
        }
        .wrap-room {
            width: 58px;
            height: 53px;
            position: absolute;
            bottom: 0;
            top: 66px;
            left: 166px;
            right: 0;
            font-size: 1.6em;
            line-height: 44px;
            font-weight: bold;
            text-align: center;
        }
        .wrap-info {
            position: absolute;
            left: 14px;
            right: 14px;
            top: 128px;
            font-size: 8px;
        }
        .wrap-bus {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 36px;
            line-height: 32px;
            padding-left: 58px;
            font-size: 13px;
            font-weight: bold;
            color: #fff;
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

        .color-primary {
            color: #f1b319;
        }
    </style>
    <title>{{ $umrohTrip->title }}</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                <div class="wrap-content">
                @foreach($participants as $key => $participant)
                    @php
                        $documentStyle = Package::find($participant->package_id)->document_style ?? 'lebih_hemat';
                    @endphp
                    <div class="display-inline">
                        <img class="wrapimage" src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/koper_'.$documentStyle.'.png')))}}" alt="">
                        <div class="wrap-avatar">
                        <div class="container-svg">  
                            <div class="subcontainer-svg"> 
                                <img class="img-svg" src="{{$participant->profile_thumbnail}}"  />
                            </div>
                        </div>
                        </div>
                        <div class="wrap-room">
                            <p>{{$participant->no_urut}}</p>
                        </div>
                        <div class="wrap-info">
                            <table width="100%">
                                <tr>
                                    <td valign="top">Nama</td>
                                    <td valign="top">:</td>
                                    <td>{{strtoupper($participant->name_in_passport??$participant->name)}}</td>
                                </tr>
                                <tr>
                                    <td valign="top">No. Passport</td>
                                    <td valign="top">:</td>
                                    <td>{{strtoupper($participant->no_passport)}}</td>
                                </tr>
                                <tr>
                                    <td valign="top">No. Indonesia</td>
                                    <td valign="top">:</td>
                                    <td>+{{$participant->tour_leader_phone}}</td>
                                </tr>
                                <tr>
                                    <td valign="top">Tour Leader</td>
                                    <td valign="top">:</td>
                                    <td>{{strtoupper($participant->tour_leader)}}</td>
                                </tr>
                                <tr>
                                    <td valign="top">Madinah</td>
                                    <td valign="top">:</td>
                                    <td>{{strtoupper($umrohTrip->pic_madinah)}}<br>+{{$umrohTrip->pic_madinah_phone_number}}</td>
                                </tr>
                                <tr>
                                    <td valign="top">Makkah</td>
                                    <td valign="top">:</td>
                                    <td>{{strtoupper($umrohTrip->pic_mekkah)}}<br>+{{$umrohTrip->pic_mekkah_phone_number}}</td>
                                </tr>
                                <tr>
                                    <td valign="top">Jeddah</td>
                                    <td valign="top">:</td>
                                    <td>{{strtoupper($umrohTrip->pic_jeddah)}}<br>+{{$umrohTrip->pic_jeddah_phone_number}}</td>
                                </tr>
                                <tr>
                                    <td valign="top">Mutawwif</td>
                                    <td valign="top">:</td>
                                    <td>{{strtoupper($participant->mutawwif)}}</td>
                                </tr>
                                <tr>
                                    <td valign="top">Hotel Madinah</td>
                                    <td valign="top">:</td>
                                    <td>{!! preg_replace("/[\x{2B50}]/u", "<span class='color-primary'>&#9733;</span>", strtoupper($participant->hotel_madinah_selected)) !!}</td>
                                </tr>
                                <tr>
                                    <td valign="top">Hotel Makkah</td>
                                    <td valign="top">:</td>
                                    <td>{!! preg_replace("/[\x{2B50}]/u", "<span class='color-primary'>&#9733;</span>", strtoupper($participant->hotel_makkah_selected)) !!}</td>
                                </tr>
                                <tr>
                                    <td valign="top">Muassasah</td>
                                    <td valign="top">:</td>
                                    <td>{{strtoupper($umrohTrip->muassasah)}}<br>+{{$umrohTrip->muassasah_phone_number}}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="wrap-bus">
                            INDONESIA | BUS {{$participant->group_bus}}
                        </div>
                    </div>
                @endforeach
                </div>
            </div>    
        </div>
    </div>
</body>
</html>