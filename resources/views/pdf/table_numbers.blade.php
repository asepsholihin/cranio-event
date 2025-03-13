<?php
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
            margin: 0;
            padding: 0;
            background-color: #fff;
            font-family: arial;
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
            margin: 0;
            padding: 0;
            position: relative;
            background-color: #fff;
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
            margin-top: 10px;
        }

        .mt-2 {
            margin-top: 24px;
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

        p.indent {
            text-indent: 42px;
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

        .wrap-name p {
            margin-bottom: 12px;
            font-weight: bold;
        }

        .wrap-name table td p {
            font-size: 0.8em;
            margin-bottom: 12px;
            font-weight: bold;
        }

        .wrap-name {
            position: absolute;
            width: 23.4cm;
            height: 8cm;
            bottom: 70px;
            left: 8cm;
            font-size: 1.6em;
        }

        .wrap-number {
            position: absolute;
            width: 8cm;
            height: 5cm;
            bottom: 4cm;
            font-size: 10em;
            color: #fff;
            font-weight: bold;
            text-align: center;
            padding: 12px;
        }

        .wrap-date {
            position: absolute;
            width: 8cm;
            height: 1cm;
            bottom: 3cm;
            font-size: 1.8em;
            color: #fff;
            text-align: center;
        }

        .wrapper-rotate .wrap-number {
            position: absolute;
            right: 0;
            width: 8cm;
            height: 5cm;
            bottom: 4cm;
            font-size: 10em;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .wrapper-rotate .wrap-date {
            position: absolute;
            right: 0;
            width: 8cm;
            height: 1cm;
            bottom: 3cm;
            font-size: 1.8em;
            color: #fff;
            text-align: center;
        }

        .wrapper-rotate .wrap-name {
            position: absolute;
            width: 23.4cm;
            height: 8cm;
            bottom: 70px;
            left: 0;
            font-size: 1.6em;
        }

        .main {
            position: relative;
            width: 31.4cm;
            height: 25cm;
            display: block;
            margin: auto;
            background-color: #f5f5f5;
        }

        .letter-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9;
        }

        .letter-bg img {
            width: 31.4cm;
            height: 25cm;
        }

        .page_break {
            page-break-before: always;
        }

        .wrapper-rotate {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            height: 12.5cm;
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

        .wrapper-rotate .footer {
            left: 0;
        }

        .bg-left {
            position: absolute;
            left: 0;
            width: 8cm;
            height: 100%
        }

        .bg-sapphire {
            background: #2b3e5a;
        }

        .bg-emerald {
            background: #336c2d;
        }

        .bg-ruby {
            background: #911c14;
        }

        .bg-plus {
            background: #d7b143;
        }

        .bg-gold {
            background: #2b3e5a;
        }

        .bg-silver {
            background: #d7b143;
        }

        .footer {
            position: absolute;
            bottom: 30px;
            width: 23.4cm;
            left: 8cm;
            font-size: 1em;
            text-align: center;
            color: #777;
        }

        .padding-horizontal {
            padding: 0 3cm;
        }
    </style>
    <title>TABLE NUMBER</title>
</head>

<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                @foreach ($tables as $table)
                    <?php
                    $packageColor = Package::find($table->package_id)->color ?? '#d7b143';
                    ?>
                    <div class="main">
                        <div class="letter-bg">
                            <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('private_assets/images/bg_table.png'))) }}"
                                alt="">
                        </div>

                        <div class="bg-left" style="background:{{$packageColor}}"></div>

                        <div class="wrapper-normal">
                            <div class="wrap-number">
                                {{ str_pad($table->manasik_table, 2, '0', STR_PAD_LEFT) }}
                            </div>
                            <div class="wrap-date">
                                {{ \Carbon\Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y') }}
                            </div>

                            <div class="wrap-name">
                                @if (count($table->participants) > 4)
                                    <table width="100%" style="padding: 0 36px;">
                                        <tr>
                                            <td width="50%" valign="top" style="padding-right: 12px;">
                                                <?php
                                                $numParticipant = count($table->participants);
                                                $maxParticipantPerColumn = ceil($numParticipant / 2);
                                                
                                                for ($i = 0; $i < $numParticipant; $i++) {
                                                    echo '<p>' . $table->participants[$i]->title . '. ' . strtoupper($table->participants[$i]->name_in_passport ?? $table->participants[$i]->name) . '</p>';
                                                    if ($i + 1 == $maxParticipantPerColumn) {
                                                        echo '</td><td width="50%" valign="top" style="padding-left: 12px;">';
                                                    }
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                @else
                                    <div class="padding-horizontal">
                                        @foreach ($table->participants as $participant)
                                            <p>{{ $participant->title }}. {{ strtoupper($participant->name_in_passport ?? $participant->name) }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="footer">
                                <p>(Silahkan membuka buku pegangan dan doa saat menyimak pemaparan materi manasik)</p>
                            </div>
                        </div>

                        <div class="wrapper-rotate">
                            <div class="wrap-number">
                                {{ str_pad($table->manasik_table, 2, '0', STR_PAD_LEFT) }}
                            </div>
                            <div class="wrap-date">
                                {{ \Carbon\Carbon::parse($umrohTrip->departure_at)->isoFormat('D MMMM Y') }}
                            </div>

                            <div class="wrap-name">
                                @if (count($table->participants) > 4)
                                    <table width="100%" style="padding: 0 36px;">
                                        <tr>
                                            <td width="50%" valign="top" style="padding-right: 12px;">
                                                <?php
                                                $numParticipant = count($table->participants);
                                                $maxParticipantPerColumn = ceil($numParticipant / 2);
                                                
                                                for ($i = 0; $i < $numParticipant; $i++) {
                                                    echo '<p>' . $table->participants[$i]->title . '. ' . strtoupper($table->participants[$i]->name_in_passport ?? $table->participants[$i]->name) . '</p>';
                                                    if ($i + 1 == $maxParticipantPerColumn) {
                                                        echo '</td><td width="50%" valign="top" style="padding-left: 12px;">';
                                                    }
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                @else
                                    <div class="padding-horizontal">
                                        @foreach ($table->participants as $participant)
                                            <p>{{ $participant->title }}. {{ strtoupper($participant->name_in_passport ?? $participant->name) }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="footer">
                                <p>(Silahkan membuka buku pegangan dan doa saat menyimak pemaparan materi manasik)</p>
                            </div>
                        </div>

                    </div>

                    <div class="page_break"></div>
                    <div style="padding-top:0cm"></div>
                @endforeach
            </div>
        </div>
    </div>
</body>

</html>
