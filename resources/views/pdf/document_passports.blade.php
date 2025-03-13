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
            font-family: 'Mulish-Bold';
            src: url({{ storage_path('private_assets/fonts/Mulish-Bold.ttf') }}) format("truetype");
        }
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
            font-family: 'Mulish-Bold';
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
            text-align: center;
        }
        .wrap-content {
            margin: auto;
            display: inline;
        }
        .card {
            position: relative;
            border: 1px solid #ccc;
            display: inline-block;
            font-size: 12px;
            margin: 8px;
        }
        .card-passport img {
            height: 20cm;
            object-fit: contain;
        }
        .name {
            padding: 12px;
            font-size: 3em;
            text-align: center;
            font-family: 'Mulish-ExtraBold';
            font-weight: bold;
            position: absolute;
            z-index: 99;
            left: 0;
            right: 0;
            bottom: 50%;
            margin: auto;
            color: #000;
            text-shadow: 0 4px 12px;
            opacity: 0.2; 
            filter:alpha(opacity=0.2);
        }
        .name-footer {
            padding: 12px;
            text-align: center;
            font-family: 'Mulish-ExtraBold';
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

        .page_break {
            page-break-before: always;
        }

        .rotate0 {
        }
        .rotate90 {
            -webkit-transform: rotate(90deg);
            -moz-transform: rotate(90deg);
            -o-transform: rotate(90deg);
            -ms-transform: rotate(90deg);
            transform: rotate(90deg);
            width: 20cm;
            height: 20cm;
        }
        .rotatemin90 {
            -webkit-transform: rotate(-90deg);
            -moz-transform: rotate(-90deg);
            -o-transform: rotate(-90deg);
            -ms-transform: rotate(-90deg);
            transform: rotate(-90deg);
            width: 20cm;
            height: 20cm;
        }
        .rotate180 {
            -webkit-transform: rotate(180deg);
            -moz-transform: rotate(180deg);
            -o-transform: rotate(180deg);
            -ms-transform: rotate(180deg);
            transform: rotate(180deg);
            height: 20cm;
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
    </style>
    <title>Passport {{ $umrohTrip->title }} Part {{ $page }}</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                @foreach($participants as $participant)
                    <div class="wrap-content">
                        @foreach($participant->files as $file)

                        <?php
                        $angle = "";
                        try {
                            $destination_extension = strtolower(pathinfo($file->file_path_url, PATHINFO_EXTENSION));
                            if(in_array($destination_extension, ["jpg","jpeg"]) || exif_imagetype($file->file_path_url) === IMAGETYPE_JPEG) {
                                
                                $exif = exif_read_data($file->file_path_url);
                                if (!empty($exif['Orientation'])) {
                                    if (in_array($exif['Orientation'], [3, 4])) {
                                        $angle = "rotate180";
                                    }
                                    if (in_array($exif['Orientation'], [5, 6])) {
                                        $angle = "rotate90";
                                    }
                                    if (in_array($exif['Orientation'], [7, 8])) {
                                        $angle = "rotatemin90";
                                    }
                                }
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                        ?>
                            <div class="card card-passport">
                                <img src="{{$file->file_path_url}}" class="{{$angle}}" alt="{{$file->name}}">
                                <div class="name">
                                    {{$participant->name_in_passport ?? $participant->name}}
                                </div>
                                <div class="name-footer">
                                    {{$participant->name_in_passport ?? $participant->name}}<br>NO. URUT: {{ $participant->no_urut }}
                                </div>
                            </div>
                            <div class="page_break"></div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>
