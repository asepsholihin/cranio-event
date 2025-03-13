<?php
use Carbon\Carbon;

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
            width: 210mm;
            min-height: 297mm;
            background-color: #ffffff;
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
            height: 1399px; 
        }
        .subpage {
            position: relative;
            height: 29.7cm;
            z-index: 999;
        }
        .page_break {
            page-break-before: always;
        }
        h4 {
            margin-top: 0;
            margin-bottom: 4px;
        }
        li.withspace {
            padding: 0 0 8px;
        }
        table td {
            /* padding: 4px; */
        }
        p {
            margin: 0 0 0;
        }
        @page {
            size: A4;
            margin: 0;
        }
        @media print {
            html, body {
                width: 21cm;
                height: 29.7cm;         
                font: 12pt "Mulish";
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
        .box {
            box-sizing: border-box;
        }
        .column {
            position: relative;
            float: left;
            width: 33.33%;
            height: 50px;
            page-break-inside: avoid;
        }
        .row:after {
            content: "";
            display: table;
            clear: both;
        }
        .even {
            background-color: #fff;
        }
        .odd {
            background-color: #efefef;
        }
        .column .left {
            position:absolute;left:0;
            padding: 0 6px;
            height: 50px;
            width: 80%;
            line-height: 50px;
            border-left: 1px solid #ccc;
            border: 1px solid #ccc;
        }
        .column .right {
            position:absolute;
            right:0;
            height: 50px;
            width: 20%;
            line-height: 50px;
            text-align: center;
            border: 1px solid #ccc;
        }
        .uppercase {
            text-transform: uppercase;
        }
        .bold {
            font-weight: bold;
        }

    </style>
    <title>Resume {{$umrohTrip->title}}</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                <div class="main">
                    
                    <center>
                        <h1>PAKET INFO<br>{{ strtoupper($umrohTrip->title)}}</h1>
                    </center>
                    <br>

                    <h4 class="uppercase">{{ Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y') }} – {{ Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y') }}</h4>
                    <h4 class="uppercase">{{ $umrohTrip->airlines }} {{ $destinations }}</h4>
                    
                    <div style="margin: 24px 0;"></div>

                    <h4>KEBERANGKATAN :</h4>
                    <span class="bold uppercase">{{ Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y') }} <span style="background:yellow;">{{ $umrohTrip->airport_departure_code }} @if($umrohTrip->departure_at_time)({{ Carbon::parse($umrohTrip->departure_at_time)->format('H:i') }})@endif {{ $umrohTrip->airport_departure_destination_code }} @if($umrohTrip->departure_destination_at_time)({{ Carbon::parse($umrohTrip->departure_destination_at_time)->format('H:i') }})@endif {{ $umrohTrip->flight_number }}</span></span>
                    <br><br>
                    <h4>KEPULANGAN :</h4>
                    <span class="bold uppercase">{{ Carbon::parse($umrohTrip->return_at)->isoFormat('D MMMM Y') }} <span style="background:yellow;">{{ $umrohTrip->airport_return_code }} @if($umrohTrip->return_at_time)({{ Carbon::parse($umrohTrip->return_at_time)->format('H:i') }})@endif {{ $umrohTrip->airport_return_destination_code }} @if($umrohTrip->departure_destination_at_time)({{ Carbon::parse($umrohTrip->return_destination_at_time)->format('H:i') }})@endif  {{ $umrohTrip->flight_number_return }}</span></span>

                    <div style="margin: 24px 0;"></div>

                    <h4>HOTEL:</h4>
                    <ol>
                        @foreach($packages as $package)
                        <li>
                            <p>{{ $package->name }}</p>
                            <table width="100%">
                                <tr>
                                    <td width="14%"><strong>Madinah</strong></td>
                                    <td width="1"><strong>:</strong></td>
                                    <td>
                                        <ul style="list-style:none;margin:0;padding:0">
                                            <li><strong>{{ $package->hotel_madinah }} (&#9733; {{ $package->star_hotel_madinah }})</strong></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="14%"><strong>Makkah</strong></td>
                                    <td width="1"><strong>:</strong></td>
                                    <td>
                                        <ul style="list-style:none;margin:0;padding:0">
                                            <li><strong>{{ $package->hotel_makkah }} (&#9733; {{ $package->star_hotel_makkah }})</strong></li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </li>
                        @endforeach
                    </ol>

                    <div style="margin: 24px 0;"></div>

                    <h4>JUMLAH JAMAAH:</h4>
                    <ul>
                            <table width="100%">
                                <tr>
                                    <td width="35%">Total Participant</td>
                                    <td width="1">:</td>
                                    <td>{{ $participant->total_participant }}</td>
                                </tr>
                                <tr>
                                    <td>Participant Laki-laki</td>
                                    <td>:</td>
                                    <td>{{ $participant->total_male }} (Umur ≥17<60)</td>
                                </tr>
                                <tr>
                                    <td>Participant Perempuan</td>
                                    <td>:</td>
                                    <td>{{ $participant->total_female }} (Umur ≥17<60)</td>
                                </tr>
                                <tr>
                                    <td>Participant Lansia Laki-laki</td>
                                    <td>:</td>
                                    <td>{{ $participant->total_male_old }} (Umur ≥60)</td>
                                </tr>
                                <tr>
                                    <td>Participant Lansia Perempuan</td>
                                    <td>:</td>
                                    <td>{{ $participant->total_female_old }} (Umur ≥60)</td>
                                </tr>
                                <tr>
                                    <td>Participant Anak Laki-laki</td>
                                    <td>:</td>
                                    <td>{{ $participant->total_male_child }} (Umur<17)</td>
                                </tr>
                                <tr>
                                    <td>Participant Anak Perempuan</td>
                                    <td>:</td>
                                    <td>{{ $participant->total_female_child }} (Umur<17)</td>
                                </tr>
                            </table>
                    </ul>
                    
                    <div style="margin: 24px 0;"></div>
                    
                    <h4>Participant Berprofesi sbg Tenaga Kesehatan: @if(count($participantNakes) == 0) - @endif</h4>
                    <ol>
                        @if(count($participantNakes) > 0)
                            @foreach($participantNakes as $participant)
                                <li class="withspace">{{ $participant->front_title }} {{ $participant->name }}{{ ($participant->back_title) ? ", ".$participant->back_title : "" }}</li>
                            @endforeach
                        @endif
                    </ol>

                    <div style="margin: 24px 0;"></div>

                    <h4>Participant Berprofesi sbg TNI/POLRI: @if(count($participantTniPolri) == 0) - @endif</h4>
                    <ol>
                        @if(count($participantTniPolri) > 0)
                            @foreach($participantTniPolri as $participant)
                                <li class="withspace">{{ $participant->front_title }} {{ $participant->name }} {{ ($participant->back_title) ? ", ".$participant->back_title : "" }}</li>
                            @endforeach
                        @endif
                    </ol>
                    
                    <div style="margin: 24px 0;"></div>

                    <h4>Asal Provinsi Participant berdasarkan Tempat Kelahiran: @if(count($participantProvince) == 0) - @endif</h4>
                    <br>
                    @if(count($participantProvince) > 0)
                        <?php $i=1; ?>
                        <div class="box row">
                        @foreach($participantProvince as $participant)
                            <div class="column <?= ((($i%3) && ($i%3!=1)) ? 'event':'odd') ?>">
                                <p class="left">{{ $participant->ktp_province }}</p>
                                <p class="right">{{ $participant->total }}</p>
                            </div>
                            <?php $i++; ?>
                        @endforeach
                        </div>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</body>
</html>