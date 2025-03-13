<?php
use App\Models\Package;
use App\Support\General;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">
        @font-face {
            font-family: 'Mulish';
            src: url({{ file_get_contents(storage_path('private_assets/fonts/Mulish-Regular.ttf')) }}) format("truetype");
        }
        @font-face {
            font-family: 'Mulish-ExtraBold';
            src: url({{ file_get_contents(storage_path('private_assets/fonts/Mulish-ExtraBold.ttf')) }}) format("truetype");
        }
        @font-face {
            font-family: 'Mulish-Bold';
            src: url({{ file_get_contents(storage_path('private_assets/fonts/Mulish-Bold.ttf')) }}) format("truetype");
        }
        @font-face {
            font-family: 'Martel';
            src: url({{ file_get_contents(storage_path('private_assets/fonts/Martel-Bold.ttf')) }}) format("truetype");
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
            height: 1399px; 
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
        .mb-1 {
            margin-bottom: 12px !important;
        }
        .mb-2 {
            margin-bottom: 24px !important;
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

        .main {
            position: relative;
            /* height: 29.7cm; */
            display: block;
        }

        .page_break {
            page-break-before: always;
        }

        .wrap-page {
            width: 21cm;
            margin: 12px;
            padding: 24px;
            border: 1px solid #ccc;
        }

    </style>
    <title>Survey Report</title>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
</head>
<body>
    <div class="book">
        <div class="page">
            <div class="subpage">
                <div class="main">
                    <div class="letter-bg">
                        
                    </div>
                    
                    <div class="wrap-page">
                        <h3>Ulasan Perjalanan</h3>
                        <h1>Participant Umrah</h1>
                        <h2>Jejak Imani</h2>
                        <h4>{{ $titleTrip }}</h4>
                    </div>
                </div>
                <div class="page_break"></div>
                
                <div class="main">
                    <div class="letter-bg">
                        
                    </div>
                    
                    <div class="wrap-page">
                        <h3 class="mb-2">Ragam Bahasan</h3>
                        <div class="mb-2">
                            <h1>01 Review</h1>
                            <p>Resume dari review dan insight yang diisi oleh {{ number_format($totalRespon, 0, ",", ".") }} participant dari {{ $totalTrip }} keberangkatan</p>
                        </div>
                    </div>
                </div>
                <div class="page_break"></div>

                <div class="main">
                    <div class="letter-bg">
                        
                    </div>
                    
                    <div class="wrap-page">
                        <h3 class="mb-2">{{ $form['title_in_report'] }}</h3>
                        <p>{{ "https://www.jejakimani.com/" . $form['slug'] }}</p>
                        <p>Periode Survey {{ $tanggalPeriode }} (total {{ $totalParticipant }} participant), sebanyak {{ $totalRespon }} Participant ({{ number_format(($totalRespon/$totalParticipant)*100, 2, ",", ".") }}%) memberikan review mengenai {{ $form['title_in_report'] }}</p>
                    </div>
                </div>
                <div class="page_break"></div>

                @foreach($questions as $question)
                @if($question->view_in_report)
                <div class="main">
                    <div class="letter-bg">
                        
                    </div>
                    
                    <div class="wrap-page">
                        <h3 class="mb-0">{{ $question->title_in_report }}</h3>
                        <p class="mb-2">*Jumlah responden {{ $totalRespon }} participant</p>
                        
                        @if($question->model_in_report == "vote")
                        <div>
                            @foreach($question->answers as $answer)
                                <p>{{ $answer->count }} Vote - {{ $answer->answer }}</p>
                            @endforeach
                        </div>
                        @endif


                        @if($question->model_in_report == "chart")
                        <div>
                            <div id="myChart" style="width:100%"></div>
                            <script>
                                var chartData = [
                                    ['', '']
                                ];
                                google.charts.load('current', {'packages':['corechart']});
                                google.charts.setOnLoadCallback(drawChart);

                                function drawChart() {
                                    // Set Data
                                    const data = google.visualization.arrayToDataTable(chartData);

                                    // Set Options
                                    const options = {
                                      title: "",
                                      colors: ['#efb10f'],
                                    };

                                    // Draw
                                    const chart = new google.visualization.BarChart(document.getElementById('myChart'));
                                    chart.draw(data, options);
                                }
                            </script>
                            @foreach($question->answers as $answer)
                                <script>
                                    chartData.push(["<?php echo $answer->answer ?>", <?php echo $answer->count ?>])
                                </script>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                <div class="page_break"></div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>