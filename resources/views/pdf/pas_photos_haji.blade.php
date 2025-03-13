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
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            font: 9pt "Mulish";
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
        .main {
            width: 1123px;
            height: 794px;
        }
        .img-layout-1 {
            position: relative;
            float: left;
            display: inline-block;
            width: 151.181px;
            height: 113.385px;
            border: 1px solid #000;
            margin: 0.2px;
        }
        .img-layout-1 img {
            position: absolute;
            height: 149px;
            width: 111px;
            -webkit-transform: rotate(-90deg);
            -moz-transform: rotate(-90deg);
            -ms-transform: rotate(-90deg);
            -o-transform: rotate(-90deg);
            transform: rotate(-90deg);
            object-fit: cover;
            object-position: top;
            left: 19px;
            top: -19px;
            margin: 0.2px;
        }
        .img-layout-2 {
            position: relative;
            float: left;
            display: inline-block;
            width: 75.590px;
            height: 113.385px;
            border: 1px solid #000;
            margin: 0.2px;
        }
        .img-layout-2 img {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top;
        }
        .img-layout-3 {
            position: relative;
            float: left;
            display: inline-block;
            width: 151.181px;
            height: 226.771px;
            border: 1px solid #000;
            margin: 0.2px;
        }
        .img-layout-3 img {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top;
        }
        .img-layout-4 {
            position: relative;
            float: left;
            display: inline-block;
            height: 75.590px;
            width: 113.385px;
            border: 1px solid #000;
            margin: 1px;
        }
        .img-layout-4 img {
            position: absolute;
            height: 111px;
            width: 73px;
            -webkit-transform: rotate(90deg);
            -moz-transform: rotate(90deg);
            -ms-transform: rotate(90deg);
            -o-transform: rotate(90deg);
            transform: rotate(90deg);
            object-fit: cover;
            object-position: top;
            left: 19px;
            top: -19px;
            margin: 0.2px;
        }
    </style>
    <title>{{ $umrohTrip->title }}</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                @foreach($participants as $participant)
                    <div class="main">
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td valign="top" style="width:454px">
                                    @for($i=1;$i<=18;$i++)
                                    <div class="img-layout-1">
                                        <img src="{{$participant->profile_thumbnail}}" />
                                    </div>
                                    @endfor
                                </td>
                                <td valign="top" style="width:152px;">
                                    @for($i=1;$i<=2;$i++)
                                    <div class="img-layout-1">
                                        <img src="{{$participant->profile_thumbnail}}" />
                                    </div>
                                    @endfor
                                    @for($i=1;$i<=4;$i++)
                                    <div class="img-layout-2">
                                        <img src="{{$participant->profile_thumbnail}}" />
                                    </div>
                                    @endfor
                                    <div class="img-layout-3">
                                        <img src="{{$participant->profile_thumbnail}}" />
                                    </div>
                                </td>
                                <td valign="top" style="width:455px;">
                                    @for($i=1;$i<=9;$i++)
                                    <div class="img-layout-3">
                                        <img src="{{$participant->profile_thumbnail}}" />
                                    </div>
                                    @endfor
                                    <div class="img-layout-4">
                                        <img src="{{$participant->profile_thumbnail}}" />
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="page_break"></div>
                @endforeach
            </div>    
        </div>
    </div>
</body>
</html>