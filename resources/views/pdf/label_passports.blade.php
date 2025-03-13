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
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            font-weight:bold;
            font: 9pt "Mulish-ExtraBold";
        }
        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }
        .page {
            position: relative;
            padding: 0;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .subpage {
            position: relative;
            z-index: 999;
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }
        @media print {
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
        .wrap-table {
            width: 17.6cm;
            margin: auto;
        }
        .parent-table tr {
            page-break-inside: avoid;
        }
        .table {
            width: 8.7cm;
            float: left;
            border-spacing: 0;
            border-collapse: collapse;
            margin: -1px -0.1rem;
        }
        .table tr td {
            border: 2px solid #000;
            text-align: center;
        }

        .table tr td div.col1 {
            width: 0.8cm;
            height: 0.8cm;
            line-height: 0.8cm;
            margin: auto;
            font-weight: bold;
            font-family: 'Mulish-ExtraBold';
            font-size: 15px;
        }
        .table tr td div.col2 {
            width: 7.7cm;
            height: 0.8cm;
            line-height: 0.8cm;
            margin: auto;
            font-weight: bold;
            font-family: 'Mulish-ExtraBold';
        }

        .gender-font{
            width: 1cm !important;
            font-size:16px !important;
        }

        .col2[data-contentlength="0"]{ font-size: 15px; }
        .col2[data-contentlength="0.5"]{ font-size: 14px; }
        .col2[data-contentlength="1"]{ font-size: 12px; }
        .col2[data-contentlength="1.5"]{ font-size: 11px; }
        .col2[data-contentlength="2"]{ font-size: 11px; }
    </style>
    <title>{{ $umrohTrip->title }}</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                <div class="wrap-table">
                    <table class="parent-table" width="100%" cellspacing="0" cellpadding="0">
                        <?php
                            $i=0;
                            $c = ceil(count($participants)/2);
                        ?>
                        @for($i=0; $i<$c;++$i)
                            <tr>
                                <td>
                                    <table class="table" width="100%">
                                        <tr>
                                            <td>
                                                <div class="col1">
                                                    {{ $participants[$i]->no_urut }}
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                    $length = strlen($participants[$i]->name_in_passport ?? $participants[$i]->name);
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
                                                <div class="col2" data-contentlength="{{ $c_length }}">
                                                    {{ strtoupper($participants[$i]->name_in_passport ?? $participants[$i]->name) }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="col1 gender-font">
                                                    {{ ($participants[$i]->gender == 1) ? "M" : "F" }}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

                                </td>
                                <td>
                                    @if(isset($participants[$i+$c]))
                                    <?php
                                    $length = strlen($participants[$i+$c]->name_in_passport ?? $participants[$i+$c]->name);
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
                                    <table class="table" width="100%">
                                        <tr>
                                            <td>
                                                <div class="col1">
                                                    {{ $participants[$i+$c]->no_urut }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="col2" data-contentlength="{{ $c_length }}">
                                                    {{ strtoupper($participants[$i+$c]->name_in_passport ?? $participants[$i+$c]->name) }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="col1 gender-font">
                                                    {{ ($participants[$i+$c]->gender == 1) ? "M" : "F" }}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    @endif
                                </td>
                            </tr>
                        @endfor

                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
