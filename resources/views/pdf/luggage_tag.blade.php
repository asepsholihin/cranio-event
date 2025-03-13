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
            padding: 0.4cm;
            z-index: 999;
        }
        .subpage-ruby {
            position: relative;
            padding: 1cm 0.4cm;
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
            padding: 0.4cm 0;
            display: inline;
        }
        .wraplayout .display-inline {
            position: relative;
            width: 10.7cm;
            height: 6.73cm;
            margin-bottom: 0;
            display: inline-block;
            vertical-align: top;
        }
        
        .display-inline {
            position: relative;
            width: 10.7cm;
            height: 6.73cm;
            margin-bottom: 4px;
            display: inline-block;
            vertical-align: top;
        }
        .display-horizontal {
            position: relative;
            width: 23.8cm;
            height: 7.1cm;
            margin-bottom: 3px;
            display: inline-block;
            vertical-align: top;
        }
        .display-vertical {
            position: relative;
            width: 11.2cm;
            height: 14.76cm;
            margin-bottom: 0;
            display: inline-block;
            vertical-align: top;
        }
        .wraplayout .display-inline .wrapimage {
            width: 10.7cm;
            height: 6.73cm;
            position:relative;
        }
        .display-inline .wrapimage {
            width: 10.7cm;
            height: 6.73cm;
            position:relative;
        }
        .display-horizontal .wrapimage {
            width: 23.8cm;
            height: 7.1cm;
            margin-bottom: 4px;
            position:relative;
        }
        .display-vertical .wrapimage {
            width: 11.2cm;
            height: 14.76cm;
            margin-bottom: 0;
            position:relative;
        }
        .img-cover {
            height: 100%;
            width: 100%;
            object-fit: scale-down;
        }
        .wrap-avatar {
            width: 80px;
            height: 110px;
            position: absolute;
            bottom: 0;
            top: 23px;
            left: 30px;
            right: 0;
        }
        .display-horizontal .wrap-name {
            width: 160px;
            height: 16px;
            position: absolute;
            bottom: 0;
            top: 95px;
            left: 198px;
            right: 0;
            font-size: 12px;
            font-weight: bold;
        }
        .display-horizontal .wrap-passport {
            width: 160px;
            height: 16px;
            position: absolute;
            bottom: 0;
            top: 205px;
            left: 198px;
            right: 0;
            font-size: 12px;
            font-weight: bold;
        }
        .display-horizontal .wrap-avatar {
            width: 138px;
            height: 182px;
            position: absolute;
            bottom: 0;
            top: 43px;
            left: 34px;
            right: 0;
        }
        .display-horizontal .wrap-room {
            width: 42px;
            height: 20px;
            position: absolute;
            bottom: 0;
            top: 58px;
            left: 485px;
            right: 0;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }
        .display-horizontal .wrap-hotel1 {
            width: 42px;
            position: absolute;
            bottom: 0;
            top: 120px;
            left: 485px;
            right: 0;
            font-size: 6px;
            font-weight: bold;
            text-align: center;
        }
        .display-horizontal .wrap-hotel2 {
            width: 42px;
            position: absolute;
            bottom: 0;
            top: 184px;
            left: 485px;
            right: 0;
            font-size: 6px;
            font-weight: bold;
            text-align: center;
        }
        .display-vertical .wrap-name {
            width: 300px;
            height: 16px;
            position: absolute;
            bottom: 0;
            top: 220px;
            left: 145px;
            right: 0;
            font-size: 12px;
            font-weight: bold;
        }
        .display-vertical .wrap-passport {
            width: 300px;
            height: 16px;
            position: absolute;
            bottom: 0;
            top: 248px;
            left: 145px;
            right: 0;
            font-size: 12px;
            font-weight: bold;
        }
        .display-vertical .wrap-avatar {
            width: 108px;
            height: 144px;
            position: absolute;
            bottom: 0;
            top: 56px;
            left: 42px;
            right: 0;
        }
        .display-vertical .wrap-room {
            width: 42px;
            height: 20px;
            position: absolute;
            bottom: 0;
            top: 62px;
            left: 294px;
            right: 0;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }
        .display-vertical .wrap-hotel1 {
            width: 42px;
            position: absolute;
            bottom: 0;
            top: 120px;
            left: 294px;
            right: 0;
            font-size: 6px;
            font-weight: bold;
            text-align: center;
        }
        .display-vertical .wrap-hotel2 {
            width: 42px;
            position: absolute;
            bottom: 0;
            top: 166px;
            left: 294px;
            right: 0;
            font-size: 6px;
            font-weight: bold;
            text-align: center;
        }
        .container-svg { 
            position: absolute;
            display: table;
            width: 80px;
            height: 110px;
        }
        .display-horizontal .container-svg { 
            position: absolute;
            display: table;
            width: 138px;
            height: 182px;
        }
        .display-vertical .container-svg { 
            position: absolute;
            display: table;
            width: 108px;
            height: 144px;
        }
        .subcontainer-svg {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        .img-svg {
            height: auto;
            width: 100%;
            max-height: 108px;
        }
        .display-horizontal .img-svg {
            height: auto;
            width: 100%;
            max-height: 182px;
        }
        .display-vertical .img-svg {
            height: auto;
            width: 100%;
            max-height: 144px;
        }
        .wrap-name {
            width: 200px;
            height: 16px;
            position: absolute;
            bottom: 0;
            top: 111px;
            left: 182px;
            right: 0;
            font-size: 8px;
            font-weight: bold;
        }
        .wrap-name p, .wrap-passport p, .wrap-room p, .wrap-hotel1 p, .wrap-hotel2 p {
            margin: 0;
            vertical-align: middle;
            line-height: normal;
        }
        .wrap-passport {
            height: 16px;
            position: absolute;
            bottom: 0;
            top: 126px;
            left: 182px;
            right: 0;
            font-size: 8px;
            font-weight: bold;
        }
        .wrap-room {
            width: 42px;
            position: absolute;
            bottom: 0;
            top: 58px;
            left: 128px;
            right: 0;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }
        .wrap-hotel1 {
            width: 42px;
            position: absolute;
            bottom: 0;
            top: 62px;
            left: 192px;
            right: 0;
            font-size: 5px;
            font-weight: bold;
            text-align: center;
        }
        .wrap-hotel2 {
            width: 42px;
            position: absolute;
            bottom: 0;
            top: 62px;
            left: 252px;
            right: 0;
            font-size: 5px;
            font-weight: bold;
            text-align: center;
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

        .main {
            padding: 0 0.4cm;
        }

        .wraplayout {
            padding: 0 0.4cm;
            display: block;
        }

        .rotate-90 {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            height: 11.5cm;
            -moz-transform: rotate(90.0deg);  /* FF3.5+ */
            -o-transform: rotate(90.0deg);  /* Opera 10.6 */
            -webkit-transform: rotate(90.0deg);  /* Saf3.1+, Chrome */
            filter:  progid:DXImageTransform.Microsoft.BasicImage(rotation=0.083);  /* IE6,IE7 */
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=0.083)"; /* IE8 */
        }

        .page_break {
            page-break-before: always;
        }
    </style>
    <title>{{ $umrohTrip->title }}</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                <div class="main">
                    @foreach($participants as $participant)
                        <div class="wrap-content">
                            <div class="display-inline">
                                <img class="wrapimage" src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/luggage_'.$documentStyle.'.png')))}}" alt="">
                                <div class="wrap-avatar">
                                <div class="container-svg">  
                                    <div class="subcontainer-svg"> 
                                        <img class="img-svg" src="{{$participant->profile_thumbnail}}"  />
                                    </div>
                                </div>
                                </div>
                                <div class="wrap-room">
                                    <p>{{strtoupper($participant->group_hotel_room)}}</p>
                                </div>
                                <div class="wrap-hotel1">
                                    <p>{!! preg_replace("/[\x{2B50}]/u", "<span class='color-primary'>&#9733;</span>", strtoupper($participant->hotel_makkah_selected)) !!}</p>
                                </div>
                                <div class="wrap-hotel2">
                                    <p>{!! preg_replace("/[\x{2B50}]/u", "<span class='color-primary'>&#9733;</span>", strtoupper($participant->hotel_madinah_selected)) !!}</p>
                                </div>
                                <div class="wrap-name">
                                    <p>{{strtoupper($participant->name_in_passport??$participant->name)}}</p>
                                </div>
                                <div class="wrap-passport">
                                    <p>{{strtoupper($participant->no_passport)}}</p>
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