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
            font: 10pt "Mulish";
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
            width: 800px;
        }
        .table {
            margin: auto;
            border-spacing: 0;
            border-collapse: collapse;
        }
        .table tr th {
            font-weight: bold;
            padding: 12px;
        }
        .table tr td, .table tr th {
            border: 1px solid #000;
            text-align: center;
            padding: 4px 8px;
        }
        .group-1 {
            background-color: #ffd4d1;
        }
        .group-2 {
            background-color: #ffd1fe;
        }
        .group-3 {
            background-color: #d5d1ff;
        }
        .group-4 {
            background-color: #d1f0ff;
        }
        .group-5 {
            background-color: #d1ffff;
        }
        .group-6 {
            background-color: #b6ffff;
        }
        .group-7 {
            background-color: #b6ffce;
        }
        .group-8 {
            background-color: #dbffb6;
        }
        .group-9 {
            background-color: #fff1b6;
        }
        .group-10 {
            background-color: #ffc3b6;
        }
        .group-11 {
            background-color: #D1E9F6;
        }
        .group-12 {
            background-color: #F6EACB;
        }
        .group-13 {
            background-color: #F1D3CE;
        }
        .group-14 {
            background-color: #EECAD5;
        }
        .group-15 {
            background-color: #CBFFA9;
        }
        .group-16 {
            background-color: #FFFEC4;
        }
        .group-17 {
            background-color: #FFD6A5;
        }
        .group-18 {
            background-color: #FF9B9B;
        }
        .group-19 {
            background-color: #6DA9E4;
        }
        .group-20 {
            background-color: #ADE4DB;
        }

        .main {
            position: relative;
            padding: 0;
        }
        .page_break {
            page-break-before: always;
        }

    </style>
    <title>{{ $event->name }}</title>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                @foreach($packages as $package)
                    <div class="main">
                        <center>
                        <h4>Paket: {{ $package->package_name }}</h4>
                        </center>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Title</th>
                                    <th>Nama</th>
                                    <th>Pass No</th>
                                    <th style="width:80px">Nomer Meja</th>
                                    <th style="width:80px">Hotel Manasik</th>
                                    <th style="width:80px">Hotel Lain/Rumah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($package->data as $key => $participant)
                                <tr>
                                    <td>{{ $participant->no_urut }}</td>
                                    <td>{{ strtoupper($participant->title) }}</td>
                                    <td style="text-align: left;">{{ ($participant->name_in_passport) ? strtoupper($participant->name_in_passport) : strtoupper($participant->name) }}</td>
                                    <td>{{ $participant->no_passport }}</td>
                                    <td class="group-{{ $participant->manasik_table }}">{{ $participant->manasik_table }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="page_break"></div>
                @endforeach
            </div>    
        </div>
    </div>
</body>
</html>