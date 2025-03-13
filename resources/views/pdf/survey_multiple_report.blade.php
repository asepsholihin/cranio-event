<?php
use App\Models\Package;
use App\Support\General;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <style type="text/css">
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

        .main {
            position: relative;
            /* height: 29.7cm; */
            display: block;
        }

        .page_break {
            page-break-before: always;
        }

        .wrap-page {
            /* width: 21cm; */
            width: 100%;
            margin: 12px;
            padding: 24px;
            border: 1px solid #ccc;
        }
        .link {
            color: #ca960e;
        }
        .info {
            color: #41bcff;
        }
        ul {
            margin: 0;
            padding: 0 18px;
        }
        li {
            margin: 0 8px 12px 0;
            padding: 0;
        }
        ul.per1 {
            -moz-column-count: 1;
            -webkit-column-count: 1;
            column-count: 1;
        }
        ul.per2 {
            -moz-column-count: 2;
            -webkit-column-count: 2;
            column-count: 2;
        }
        ul.per3 {
            -moz-column-count: 3;
            -webkit-column-count: 3;
            column-count: 3;
        }
        .btn-export {
            margin: 12px;
            width: fit-content;
            display: block;
            background: #4ab953;
            padding: 6px 12px;
            color: #fff;
            text-decoration: none;
        }

        .fixed{
            position:fixed;
            z-index:9;
        }

        .right-0 {
            right:1%;
        }

        .bottom-24{
            bottom:15%;
        }
        .btn-download{
            background-color:rgb(239, 177, 15);
            border:0;
            font-size:15px;
            padding-top:15px;
            padding-bottom:15px;
            padding-left:25px;
            padding-right:25px;
            border-radius:10px;
            text-decoration:none;
            color:black;
            cursor: pointer;
        }
    </style>
    <title>Survey Report</title>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        google.charts.load('current', {'packages':['corechart']});
    </script>
</head>
<body>
    <!--
    <a class="btn-export" href="<?= $_SERVER['REQUEST_URI'] .'&export=true' ?>">Export PDF</a>
     -->
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
                        @foreach($forms as $key => $form)
                        <div class="mb-2">
                            <h1>{{ sprintf("%02d", $key+1) }} {{ $form->title_in_report }}</h1>
                            <p>Resume dari review dan insight yang diisi oleh {{ number_format($form->totalRespon, 0, ",", ".") }} participant dari {{ $totalTrip }} keberangkatan</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="page_break"></div>

                <form action="<?= $_SERVER['REQUEST_URI'] .'&export=true' ?>" method="post">
                @csrf  <!-- Include CSRF Token -->
                @foreach($forms as $form)
                <div class="main">
                    <div class="letter-bg">

                    </div>

                    <div class="wrap-page">
                        <h3 class="mb-2">{{ $form->title_in_report }}</h3>
                        <p><a class="link" href='{{ "https://www.jejakimani.com/survey/" . $form->slug }}' target="_blank">{{ "https://www.jejakimani.com/survey/" . $form->slug }}</a></p>
                        <p>Periode Survey {{ $tanggalPeriode }} (total {{ $totalParticipant }} participant), sebanyak {{ $form->totalRespon }} Participant ({{ number_format(($form->totalRespon/$totalParticipant)*100, 2, ",", ".") }}%) memberikan review mengenai {{ $form->title_in_report }}</p>
                    </div>
                </div>
                <div class="page_break"></div>

                    @foreach($form->questions as $question)
                        @if($question->view_in_report)
                        <div class="main">
                            <div class="letter-bg">

                            </div>

                            <div class="wrap-page">
                                <h3 class="mb-0">{{ $question->title_in_report }}</h3>
                                <p class="mb-2">*Jumlah responden {{ $form->totalRespon }} participant</p>

                                @if($question->model_in_report == "vote")
                                <div>
                                    <div class="mb-2">
                                    @foreach($question->answers as $key => $answer)
                                        <p>{{ $answer->count }} Vote - {{ $answer->answer }}</p>
                                        @if($key == 10) @break @endif
                                    @endforeach
                                    </div>

                                    <?php
                                        $arrayAnswer = [];
                                        if($question->option_value){
                                            foreach($question->option_value as $row) {
                                                $arrayAnswer[] = $row->value;
                                            }
                                        }
                                        $answerOthersCount = 0;
                                        $answerOthers = "";
                                    ?>

                                    @foreach($question->answers as $answer)
                                    <?php if(in_array($answer->answer, $arrayAnswer)) { ?>

                                    <?php } else {
                                        $answerOthersCount += 1;
                                        $answerOthers .= '"'.$answer->answer.'"' . ", ";
                                    } ?>
                                    @endforeach

                                    @if($answerOthers)
                                    <div class="mb-2">
                                        <h4 class="mb-1">Jawaban Lainnya</h4>
                                        <p>Sebanyak {{ $answerOthersCount }} dari {{ $form->totalRespon }} menjawab lainnya: </p>
                                        <ul
                                        <?php if($answerOthersCount <= 10) { ?>
                                            class="per1"
                                        <?php } else if($answerOthersCount <= 20) { ?>
                                            class="per2"
                                        <?php } else { ?>
                                            class="per3"
                                        <?php } ?>
                                        >
                                            @for($i=0; $i<$answerOthersCount;++$i)
                                                <li>{{ $question->answers[$i]->answer }}</li>
                                            @endfor
                                        </ul>
                                    </div>
                                    @endif

                                    @if($question->tidakpuas)
                                    <div class="mb-2">
                                        <h4 class="mb-1">Jawaban Tidak Puas</h4>
                                        <ul>
                                            @foreach($question->tidakpuas as $item)
                                            <li>
                                                {{ $item }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    @if($question->sangattidakpuas)
                                    <div class="mb-2">
                                        <h4 class="mb-1">Jawaban Sangat Tidak Puas</h4>
                                        <ul>
                                            @foreach($question->sangattidakpuas as $item)
                                            <li>
                                                {{ $item }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    @if($question->tidaknyaman)
                                    <div class="mb-2">
                                        <h4 class="mb-1">Jawaban Tidak Nyaman</h4>
                                        <ul>
                                            @foreach($question->tidaknyaman as $item)
                                            <li>
                                                {{ $item }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    @if($question->sangattidaknyaman)
                                    <div class="mb-2">
                                        <h4 class="mb-1">Jawaban Sangat Tidak Nyaman</h4>
                                        <ul>
                                            @foreach($question->sangattidaknyaman as $item)
                                            <li>
                                                {{ $item }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                                @endif

                                @if($question->model_in_report == "list")
                                @if($question->section_id)
                                <div>
                                    <div id="cart<?= $question->id ?>" style="width:100%;height:550px;" class="mb-2"></div>
                                    <script>
                                        var chartData<?= $question->id ?> = [];
                                        google.charts.setOnLoadCallback(drawChart<?= $question->id ?>);

                                        function drawChart<?= $question->id ?>() {
                                            // Set Data
                                            var formatPercent = new google.visualization.NumberFormat({
                                                pattern: '#,##0.0%'
                                            });

                                            var formatShort = new google.visualization.NumberFormat({
                                                pattern: 'short'
                                            });
                                            const data = new google.visualization.DataTable();
                                            data.addColumn('string', 'Name');
                                            data.addColumn('number', '<?= $question->title_in_report ?>');
                                            data.addColumn({
                                                type: 'string',
                                                role: 'annotation'
                                            });
                                            data.addRows(chartData<?= $question->id ?>)

                                            var view = new google.visualization.DataView(data);
                                            view.setColumns([0, 1, 2]);

                                            // Set Options
                                            const options = {
                                                title: "",
                                                colors: ['#efb10f'],
                                                legend: { position: 'none' },
                                                annotations: { alwaysOutside: true },
                                                bar: { groupWidth: "90%" }
                                            };

                                            // Draw
                                            const chart = new google.visualization.BarChart(document.getElementById('cart<?= $question->id ?>'));
                                            chart.draw(view, options);
                                            google.visualization.events.addListener(chart, 'ready', function () {
                                                var imageURI = chart.getImageURI();
                                                document.getElementById('bar-cart<?= $question->id ?>').value = imageURI
                                            });
                                        }
                                    </script>
                                    <input type="hidden" id="bar-cart<?= $question->id ?>" name="bar<?= $question->id ?>">
                                    @if($question->sections)
                                    @foreach($question->sections as $answer)
                                        <script>
                                            chartData<?= $question->id ?>.push(["<?php echo $answer->section_option ?>", <?php echo $answer->count ?>, "<?php echo number_format(($answer->count/$form->totalRespon)*100, 2, ',', '.') .'%' ?>"])
                                        </script>
                                    @endforeach
                                    @endif

                                    <h4 class="mb-1">Rincian Jawaban</h4>
                                </div>
                                @endif
                                <ul class="per3">
                                    <?php
                                        $c = count($question->answers);
                                    ?>
                                    @for($i=0; $i<$c;++$i)
                                        <li>
                                            @if($question->sections)
                                            <span class="info" style="font-style: italic;">{{ $question->answers[$i]->section_option }}</span><br>
                                            @endif
                                            {{ $question->answers[$i]->answer }}<br>
                                            @if($question->sections)
                                            <span class="link">{{ $question->answers[$i]->umroh_trip }}</span>
                                            @endif
                                        </li>
                                    @endfor
                                </ul>
                                @endif

                                @if($question->model_in_report == "chart")
                                <div>
                                    <div id="cart<?= $question->id ?>" style="width:100%;height:750px;" class="mb-2"></div>
                                    <script>
                                        var chartData<?= $question->id ?> = [];
                                        google.charts.setOnLoadCallback(drawChart<?= $question->id ?>);

                                        function drawChart<?= $question->id ?>() {
                                            // Set Data
                                            var formatPercent = new google.visualization.NumberFormat({
                                                pattern: '#,##0.0%'
                                            });

                                            var formatShort = new google.visualization.NumberFormat({
                                                pattern: 'short'
                                            });
                                            const data = new google.visualization.DataTable();
                                            data.addColumn('string', 'Name');
                                            data.addColumn('number', '<?= $question->title_in_report ?>');
                                            data.addColumn({
                                                type: 'string',
                                                role: 'annotation'
                                            });
                                            data.addRows(chartData<?= $question->id ?>)

                                            var view = new google.visualization.DataView(data);
                                            view.setColumns([0, 1, 2]);

                                            // Set Options
                                            const options = {
                                                title: "",
                                                colors: ['#efb10f'],
                                                legend: { position: 'none' },
                                                annotations: { alwaysOutside: true },
                                                bar: { groupWidth: "90%" },
                                                vAxis: { showTextEvery:1 },
                                                fontName: 'Mulish',
                                            };

                                            // Draw
                                            const chart = new google.visualization.BarChart(document.getElementById('cart<?= $question->id ?>'));
                                            chart.draw(view, options);
                                            google.visualization.events.addListener(chart, 'ready', function () {
                                                var imageURI = chart.getImageURI();
                                                document.getElementById('bar-cart<?= $question->id ?>').value = imageURI
                                            });
                                        }
                                    </script>
                                    <input type="hidden" id="bar-cart<?= $question->id ?>" name="bar<?= $question->id ?>">
                                    <?php
                                        $arrayAnswer = [];
                                        if($question->option_value){
                                            foreach($question->option_value as $row) {
                                                $arrayAnswer[] = $row->value;
                                            }
                                        }
                                        $answerOthersCount = 0;
                                        $answerOthers = "";
                                    ?>
                                    @foreach($question->answers as $answer)
                                        <script>
                                            <?php if(in_array($answer->answer, $arrayAnswer)) { ?>
                                                chartData<?= $question->id ?>.push(["<?php echo $answer->answer ?>", <?php echo $answer->count ?>, "<?php echo number_format(($answer->count/$form->totalRespon)*100, 2, ',', '.') .'%' ?>"])
                                            <?php } else {
                                                $answerOthersCount += 1;
                                                $answerOthers .= '"'.$answer->answer.'"' . ", ";
                                            } ?>
                                        </script>
                                    @endforeach

                                    @if($answerOthers)
                                    <div class="mb-2">
                                        <h4 class="mb-1">Jawaban Lainnya</h4>
                                        <p>Sebanyak {{ $answerOthersCount }} dari {{ $form->totalRespon }} menjawab lainnya:</p>
                                        <ul
                                        <?php if($answerOthersCount <= 10) { ?>
                                            class="per1"
                                        <?php } else if($answerOthersCount <= 20) { ?>
                                            class="per2"
                                        <?php } else { ?>
                                            class="per3"
                                        <?php } ?>
                                        >
                                            @for($i=0; $i<$answerOthersCount;++$i)
                                                <li>{{ $question->answers[$i]->answer }}</li>
                                            @endfor
                                        </ul>
                                    </div>
                                    @endif

                                    @if($question->tidakpuas)
                                    <div class="mb-2">
                                        <h4 class="mb-1">Jawaban Tidak Puas</h4>
                                        <ul>
                                            @foreach($question->tidakpuas as $item)
                                            <li>
                                                {{ $item }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    @if($question->sangattidakpuas)
                                    <div class="mb-2">
                                        <h4 class="mb-1">Jawaban Sangat Tidak Puas</h4>
                                        <ul>
                                            @foreach($question->sangattidakpuas as $item)
                                            <li>
                                                {{ $item }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    @if($question->tidaknyaman)
                                    <div class="mb-2">
                                        <h4 class="mb-1">Jawaban Tidak Nyaman</h4>
                                        <ul>
                                            @foreach($question->tidaknyaman as $item)
                                            <li>
                                                {{ $item }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    @if($question->sangattidaknyaman)
                                    <div class="mb-2">
                                        <h4 class="mb-1">Jawaban Sangat Tidak Nyaman</h4>
                                        <ul>
                                            @foreach($question->sangattidaknyaman as $item)
                                            <li>
                                                {{ $item }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                                @endif

                                @if($question->model_in_report == "pie_chart")
                                <div>
                                    <div id="cart<?= $question->id ?>" style="width:100%;height:650px;" class="mb-2"></div>
                                    <script>
                                        var chartData<?= $question->id ?> = [];
                                        var chartColor<?= $question->id ?> = [];
                                        google.charts.setOnLoadCallback(drawChart<?= $question->id ?>);

                                        function drawChart<?= $question->id ?>() {
                                            // Set Data
                                            var formatPercent = new google.visualization.NumberFormat({
                                                pattern: '#,##0.0%'
                                            });

                                            var formatShort = new google.visualization.NumberFormat({
                                                pattern: 'short'
                                            });
                                            const data = new google.visualization.DataTable();
                                            data.addColumn('string', 'Name');
                                            data.addColumn('number', '<?= $question->title_in_report ?>');
                                            data.addColumn({
                                                type: 'string',
                                                role: 'annotation'
                                            });
                                            data.addRows(chartData<?= $question->id ?>)

                                            var view = new google.visualization.DataView(data);
                                            view.setColumns([0, 1, 2]);

                                            // Set Options
                                            <?php if(str_contains(strtolower($question->title_in_report), 'paket') || str_contains(strtolower($question->title), 'paket')) { ?>
                                            const options = {
                                                title: "",
                                                is3D: true,
                                                fontName: 'Mulish',
                                                colors: chartColor<?= $question->id ?>
                                            };
                                            <?php } else { ?>
                                            const options = {
                                                title: "",
                                                fontName: 'Mulish',
                                                is3D: true
                                            };
                                            <?php } ?>

                                            // Draw
                                            const chart = new google.visualization.PieChart(document.getElementById('cart<?= $question->id ?>'));
                                            chart.draw(view, options);
                                            google.visualization.events.addListener(chart, 'ready', function () {
                                                var imageURI = chart.getImageURI();
                                                document.getElementById('image-cart<?= $question->id ?>').value = imageURI
                                            });
                                        }
                                    </script>
                                    <input type="hidden" id="image-cart<?= $question->id ?>" name="image<?= $question->id ?>">
                                    <?php
                                        $arrayAnswer = [];
                                        if($question->option_value){
                                            foreach($question->option_value as $row) {
                                                $arrayAnswer[] = $row->value;
                                            }
                                        }
                                        $answerOthersCount = 0;
                                        $answerOthers = "";
                                    ?>
                                    @foreach($question->answers as $answer)
                                        <script>
                                            <?php if(in_array($answer->answer, $arrayAnswer)) { ?>
                                                chartData<?= $question->id ?>.push(["<?php echo $answer->answer ?>", <?php echo $answer->count ?>, "<?php echo number_format(($answer->count/$form->totalRespon)*100, 2, ',', '.') .'%' ?>"])
                                                <?php
                                                $color = "f0b10e";
                                                if(str_contains(strtolower($answer->answer), 'sapphire')) {
                                                    $color = "348bcd";
                                                }
                                                if(str_contains(strtolower($answer->answer), 'lebih hemat')) {
                                                    $color = "d3ab50";
                                                }
                                                if(str_contains(strtolower($answer->answer), 'ruby')) {
                                                    $color = "cd1615";
                                                }
                                                ?>
                                                chartColor<?= $question->id ?>.push("<?= $color ?>")
                                            <?php } else {
                                                $answerOthersCount += 1;
                                                $answerOthers .= '"'.$answer->answer.'"' . ", ";
                                            } ?>
                                        </script>
                                    @endforeach

                                    @if($answerOthers)
                                    <div class="mb-2">
                                        <h4 class="mb-1">Jawaban Lainnya</h4>
                                        <p>Sebanyak {{ $answerOthersCount }} dari {{ $form->totalRespon }} menjawab lainnya:</p>
                                        <ul
                                        <?php if($answerOthersCount <= 10) { ?>
                                            class="per1"
                                        <?php } else if($answerOthersCount <= 20) { ?>
                                            class="per2"
                                        <?php } else { ?>
                                            class="per3"
                                        <?php } ?>
                                        >
                                            @for($i=0; $i<$answerOthersCount;++$i)
                                                <li>{{ $question->answers[$i]->answer }}</li>
                                            @endfor
                                        </ul>
                                    </div>
                                    @endif

                                    @if($question->tidakpuas)
                                    <div class="mb-2">
                                        <h4 class="mb-1">Jawaban Tidak Puas</h4>
                                        <ul>
                                            @foreach($question->tidakpuas as $item)
                                            <li>
                                                {{ $item }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    @if($question->sangattidakpuas)
                                    <div class="mb-2">
                                        <h4 class="mb-1">Jawaban Sangat Tidak Puas</h4>
                                        <ul>
                                            @foreach($question->sangattidakpuas as $item)
                                            <li>
                                                {{ $item }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    @if($question->tidaknyaman)
                                    <div class="mb-2">
                                        <h4 class="mb-1">Jawaban Tidak Nyaman</h4>
                                        <ul>
                                            @foreach($question->tidaknyaman as $item)
                                            <li>
                                                {{ $item }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    @if($question->sangattidaknyaman)
                                    <div class="mb-2">
                                        <h4 class="mb-1">Jawaban Sangat Tidak Nyaman</h4>
                                        <ul>
                                            @foreach($question->sangattidaknyaman as $item)
                                            <li>
                                                {{ $item }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="page_break"></div>
                        @endif
                    @endforeach

                @endforeach
                <div class="fixed right-0 bottom-24"><button class="btn btn-download" type="submit">Download</button></div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // function download(){
        //     var url = ''+'&'+$('#getImage').serialize();
        //     location.href = url;
        // }
    </script>
</body>
</html>


