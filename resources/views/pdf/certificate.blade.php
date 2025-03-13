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
            font: 12pt "Times New Roman";
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
            background-color: #fff;
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
            height: 1400px; 
        }
        .subpage {
            position: relative;
            height: 29.7cm;
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
        p.indent {
            text-indent: 42px;
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

        .name {
            position: absolute;
            top: 10.6cm;
            margin: auto;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 2em;
            font-weight: bold;
            font-family: cursive;
        }

        .title {
            position: absolute;
            width: 13cm;
            top: 15.3cm;
            margin: auto;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 1.4em;
            font-weight: bold;
            font-family: arial;
        }

    </style>
    <title>SURAT KETERANGAN</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                <div class="letter-bg">
                    <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(storage_path('private_assets/images/bg_certificate.jpg')))}}" alt="">
                </div>

                <div class="name">{{ $participant->name }}</div>
                <div class="title">{{ $umrohTrip->title }}</div>
            </div>
        </div>
    </div>
</body>
</html>