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

        .right {
            text-align: right;
            float: right;
        }

        .signature {
            text-align: right;
            float: right;
            font-size: 12pt;
        }

        .address {
            float: right;
            margin-top: 5px;
        }

        .left {
            text-align: left;
            float: left;
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

        h1,
        h2,
        h3,
        h4,
        h5,
        p {
            margin-top: 8px;
            margin-bottom: 8px;
        }
        .wrap-page {
            /* width: 21cm; */
            width: 100%;
            margin: 12px;
            padding: 24px;
            border: 1px solid #ccc;
        }
        @page {
            size: A4;
            margin: 0;
        }
        @media print {
            html, body {
                height: 21cm;
                width: 29.7cm;
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
            padding:20px;
        }
        .title{
            font-size:16px;
        }
        .title-lg{
            font-size: 25px;
        }
        .bg-flet{
            width: 100%;
            border:1px solid gray;
            padding:10px;
        }
        .mt-2{
            margin-top:20px;
        }
    </style>
    <title>Survey Report</title>
</head>
<body>
<div class="book">
    <div class="page">
        <div class="subpage">
            <div class="main">
                <div class="bg-flet">
                    <h3>Ulasan Perjalanan</h3>
                    <h1>Participant Umrah</h1>
                    <h2>Jejak Imani</h2>
                    <h4>{{ $titleTrip }}</h4>
                </div>
                <div class="bg-flet mt-2">
                    <h3 class="mb-2">Ragam Bahasan</h3>
                    @foreach($forms as $key => $form)
                    <div class="mb-2">
                        <h1>{{ sprintf("%02d", $key+1) }} {{ $form->title_in_report }}</h1>
                        <p>Resume dari review dan insight yang diisi oleh {{ number_format($form->totalRespon, 0, ",", ".") }} participant dari {{ $totalTrip }} keberangkatan</p>
                    </div>
                    @endforeach
                </div>
                @foreach($forms as $form)
                    <div class="bg-flet mt-2">
                        <h3 class="mb-2">{{ $form->title_in_report }}</h3>
                        <p><a class="link" href='{{ "https://www.jejakimani.com/survey/" . $form->slug }}' target="_blank">{{ "https://www.jejakimani.com/survey/" . $form->slug }}</a></p>
                        <p>Periode Survey {{ $tanggalPeriode }} (total {{ $totalParticipant }} participant), sebanyak {{ $form->totalRespon }} Participant ({{ number_format(($form->totalRespon/$totalParticipant)*100, 2, ",", ".") }}%) memberikan review mengenai {{ $form->title_in_report }}</p>
                    </div>
                    @foreach($form->questions as $question)
                    @if($question->view_in_report)
                        <div class="bg-flet mt-2">
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
                                    @if(!empty($question->image))
                                        <img src="<?= $question->image ?>"  style="width:100%; height:150px" alt="">
                                    @endif
                                    @if($question->sections)

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
                                    @if(!empty($question->image))
                                        <img src="<?= $question->image ?>"  style="width:100%" alt="">
                                    @endif
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
                                    @if(!empty($question->image))
                                        <img src="<?= $question->image ?>"  style="width:100%" alt="">
                                    @endif
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
                    @endif
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
</div>
</body>
</html>


