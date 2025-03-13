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
            font: 10pt "Mulish";
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
        .subpage {
            position: relative;
            height: 29.7cm;
            z-index: 999;
        }
        .page_break {
            page-break-before: always;
        }
        table.table {
            border-top: 1px solid #000;
            border-right: 1px solid #000;
        }
        table.table td, table.table th {
            padding: 6px 12px;
            border-left: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        table.table th {
            padding: 16px 12px;
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
        .uppercase {
            text-transform: uppercase;
        }
        .font-weight-bolder {
            font-weight: bolder;
        }

    </style>
    <title>Absensi Kedatangan Participant {{$umrohTrip->title}}</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                <div class="main">
                    <p>Total Album: {{ $totalAlbum }}</p>
                    <br>
                    <table width="100%" class="table" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr style="background-color: #fbf295">
                                <th>No</th>
                                <th>Nama</th>
                                <th>Title</th>
                                <th width="1">Album</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; ?>
                            @foreach($participants as $participant)
                            <?php
                                $color = "#ffeaad";
                                if (str_contains(strtolower($participant->package_name), "ruby")) {
                                    $color = '#ffc6cb';
                                }
                                if (str_contains(strtolower($participant->package_name), "emerald")) {
                                    $color = '#ceffd9';
                                }
                                if (strtolower($participant->package_name) == "sapphire") {
                                    $color = '#bdeaff';
                                }
                                if (strtolower($participant->package_name) == "sapphire plus") {
                                    $color = '#aacffd';
                                }
                                if (str_contains(strtolower($participant->package_name),"lebih_hemat")) {
                                    $color = '#ffeaad';
                                }
                            ?>
                            <tr>
                                <td style="background-color:<?= $color ?>" align="center">{{ $no++ }}.</td>
                                <td @if($participant->is_reference == true) class="font-weight-bolder" @endif>{{ $participant->name_in_passport ?? $participant->name }}</td>
                                <td align="center">{{ $participant->title }}</td>
                                <?php if(isset($participant->rowspan) > 0){ ?>
                                    <td align="center" rowspan="<?php echo $participant->rowspan; ?>"><strong>{{ $participant->album_qty }}</strong></td>
                                <?php } else if(isset($participant->has_rowspan)) { ?>
                                    
                                <?php } else { ?>
                                    <td align="center"><strong>{{ $participant->rowspan }}</strong></td>
                                <?php } ?>
                                <td></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>