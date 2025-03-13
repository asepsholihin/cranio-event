<?php
namespace App\File\PDF;

use App\Models\Form;
use App\Models\FormQuestion;
use App\Models\FormAnswer;
use Illuminate\Support\Str;
use SnappyImage;
use PDF;
use File;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class SurveyMultipleExportReport
{
    private $pdf;
    private $data;
    private $export;

    public function __construct($request)
    {
        App::setLocale('id');

        $totalQuestion = 0;
        $totalRespon = 0;
        $totalParticipant = 0;

        $this->export = $request['export'];
        $formIds = explode(",", $request['formIds']);
        $tripIds = explode(",", $request['trip']);
        if($request['date']) {
            $dateXplode = explode('to', $request['date']);
            $exDateStart = str_replace('/','-',$dateXplode[0]);
            if(isset($dateXplode[1]))
                $exDateEnd = str_replace('/','-',$dateXplode[1]);
            $date = date('Y-m-d', strtotime($exDateStart));
            $start = date('Y-m-d 00:00:00', strtotime($exDateStart));
            $end = date('Y-m-d 24:00:00', strtotime($exDateEnd??$exDateStart));
            $tripIds = DB::table('umroh_trips')->whereNull('deleted_at')->where('taken_seats', '>', 0)->whereBetween('umroh_trips.departure_at', [$start, $end])->get()->pluck('id');
        }

        $forms = Form::whereIn('forms.id', $formIds)->get();
        foreach ($forms as $key => $form) {
            $questions = FormQuestion::where('form_id', $form->id)->orderBy('order','asc')->get();
            foreach ($questions as $question) {
                $totalQuestion += 1;

                $image = $request['image'.$question->id];
                $bar = $request['bar'.$question->id];
                if($image){
                    $question->image = $image;
                }
                if($bar){
                    $question->image = $bar;
                }
                $question->option_value = json_decode($question->option_value);

                // TIDAK PUAS SANGAT TIDAK PUAS SECTION
                if($question->option_value) {
                    foreach($question->option_value as $item) {
                        if(Str::contains(strtolower($question->question), 'paket')) {
                            $questionAboutPackage = $question->id;
                        }
                        if(in_array(strtolower($item->value), ['tidak puas','sangat tidak puas','tidak nyaman','sangat tidak nyaman'])) {
                            $queryData = DB::table('form_answers')->select([
                                'answer',
                                'session_id',
                            ])
                            ->where('question_id', $question->id)
                            ->whereIn('umroh_trip_id', $tripIds);

                            $queryData->groupByRaw('answer,session_id');
                            $answerData = $queryData->get();
                            $tidakpuas = array();
                            $sangattidakpuas = array();
                            $tidaknyaman = array();
                            $sangattidaknyaman = array();
                            foreach ($answerData as $answerValue) {
                                if(strtolower($answerValue->answer) == 'tidak puas') {
                                    $querytidakpuas = DB::table('form_answers')->select(['answer','umroh_trips.title'])
                                    ->join('umroh_trips', 'umroh_trips.id', 'form_answers.umroh_trip_id');
                                    if(isset($questionAboutPackage)) {
                                        $querytidakpuas->where('question_id', $questionAboutPackage);
                                    }
                                    $answerSession = $querytidakpuas->where('session_id', $answerValue->session_id)->first();
                                    $tidakpuas[] = [
                                        'paket' => (isset($questionAboutPackage)) ? $answerSession->title ." - ". $answerSession->answer : "",
                                        'point' => 1
                                    ];
                                }
                                if(strtolower($answerValue->answer) == 'sangat tidak puas') {
                                    $querysangattidakpuas = DB::table('form_answers')->select(['answer','umroh_trips.title'])
                                    ->join('umroh_trips', 'umroh_trips.id', 'form_answers.umroh_trip_id');
                                    if(isset($questionAboutPackage)) {
                                        $querysangattidakpuas->where('question_id', $questionAboutPackage);
                                    }
                                    $answerSession = $querysangattidakpuas->where('session_id', $answerValue->session_id)->first();
                                    $sangattidakpuas[] = [
                                        'paket' => (isset($questionAboutPackage)) ? $answerSession->title ." - ". $answerSession->answer : "",
                                        'point' => 1
                                    ];
                                }
                                if(strtolower($answerValue->answer) == 'tidak nyaman') {
                                    $querytidaknyaman = DB::table('form_answers')->select(['answer','umroh_trips.title'])
                                    ->join('umroh_trips', 'umroh_trips.id', 'form_answers.umroh_trip_id');
                                    if(isset($questionAboutPackage)) {
                                        $querytidaknyaman->where('question_id', $questionAboutPackage);
                                    }
                                    $answerSession = $querytidaknyaman->where('session_id', $answerValue->session_id)->first();
                                    $tidaknyaman[] = [
                                        'paket' => (isset($questionAboutPackage)) ? $answerSession->title ." - ". $answerSession->answer : "",
                                        'point' => 1
                                    ];
                                }
                                if(strtolower($answerValue->answer) == 'sangat tidak nyaman') {
                                    $querysangattidaknyaman = DB::table('form_answers')->select(['answer','umroh_trips.title'])
                                    ->join('umroh_trips', 'umroh_trips.id', 'form_answers.umroh_trip_id');
                                    if(isset($questionAboutPackage)) {
                                        $querysangattidaknyaman->where('question_id', $questionAboutPackage);
                                    }
                                    $answerSession = $querysangattidaknyaman->where('session_id', $answerValue->session_id)->first();
                                    $sangattidaknyaman[] = [
                                        'paket' => (isset($questionAboutPackage)) ? $answerSession->title ." - ". $answerSession->answer : "",
                                        'point' => 1
                                    ];
                                }
                            }

                            $aggregate = [];
                            foreach($tidakpuas as $data) {
                                if(!isset($aggregate[$data['paket']])) $aggregate[$data['paket']] = 0;
                                $aggregate[$data['paket']] += $data['point'];
                            }
                            $output = [];
                            foreach($aggregate as $paket => $points) {
                                $output[] = "{$paket} - {$points} Orang";
                            }
                            $question->tidakpuas = $output;
                            // ----
                            $aggregate = [];
                            foreach($sangattidakpuas as $data) {
                                if(!isset($aggregate[$data['paket']])) $aggregate[$data['paket']] = 0;
                                $aggregate[$data['paket']] += $data['point'];
                            }
                            $output = [];
                            foreach($aggregate as $paket => $points) {
                                $output[] = "{$paket} - {$points} Orang";
                            }
                            $question->sangattidakpuas = $output;
                            // ----
                            $aggregate = [];
                            foreach($tidaknyaman as $data) {
                                if(!isset($aggregate[$data['paket']])) $aggregate[$data['paket']] = 0;
                                $aggregate[$data['paket']] += $data['point'];
                            }
                            $output = [];
                            foreach($aggregate as $paket => $points) {
                                $output[] = "{$paket} - {$points} Orang";
                            }
                            $question->tidaknyaman = $output;
                            // ----
                            $aggregate = [];
                            foreach($sangattidaknyaman as $data) {
                                if(!isset($aggregate[$data['paket']])) $aggregate[$data['paket']] = 0;
                                $aggregate[$data['paket']] += $data['point'];
                            }
                            $output = [];
                            foreach($aggregate as $paket => $points) {
                                $output[] = "{$paket} - {$points} Orang";
                            }
                            $question->sangattidaknyaman = $output;
                        }
                    }
                }

                if(in_array($question->type, ['checkbox','radio','option','scale'])) {
                    $queryData = FormAnswer::select([
                        DB::raw('count(id) as count'),
                        'answer'
                    ])
                    ->where('question_id', $question->id)
                    ->whereIn('umroh_trip_id', $tripIds);
                    $data = $queryData->orderByRaw('count DESC')->groupBy('answer')->get();


                    $chartData = [];
                    foreach ($question->option_value as $item) {
                        $count = DB::table('form_answers')->select([
                            DB::raw('count(id) as count')
                        ])
                        ->where('question_id', $question->id)
                        ->where('answer', $item->value)
                        ->whereIn('umroh_trip_id', $tripIds)
                        ->groupBy('answer')->first()->count ?? 0;
                        $chartData[] = array(
                            'count' => $count,
                            'answer' => $item->value
                        );
                    }
                    $question->answers = $data;//json_decode(json_encode($chartData));
                }
                if(in_array($question->type, ['text','textarea'])) {
                    $queryData = FormAnswer::select([
                        'answer',
                        'umroh_trip_id',
                        'section_option'
                    ])
                    ->where('question_id', $question->id)
                    ->whereIn('umroh_trip_id', $tripIds);
                    $data = $queryData->orderBy('section_option', 'DESC')->get();

                    if($question->section_id) {
                        $querySection = FormAnswer::select([
                            DB::raw('count(id) as count'),
                            'section_option'
                        ])
                        ->whereNotNull('section_option')
                        ->where('question_id', $question->id)
                        ->whereIn('umroh_trip_id', $tripIds);
                        $sections = $querySection->orderByRaw('count DESC')->groupBy('section_option')->get();

                        $question->sections = $sections;

                        foreach ($data as $value) {
                            $value->umroh_trip = DB::table('umroh_trips')->where('id', $value->umroh_trip_id)->first()->title ?? '';
                        }
                    }

                    $question->answers = $data;
                }
            }
            $totalRespon = DB::table('form_answers')->select('session_id')
            ->join('form_questions', 'form_questions.id', 'form_answers.question_id')
            ->whereIn('umroh_trip_id', $tripIds)->where('form_questions.form_id', $form->id)->groupBy('session_id')->get()->count();

            $form->totalRespon = $totalRespon;
            $form->questions = $questions;
        }

        $umrohTrips = DB::table('umroh_trips')->select('id','title','departure_at','return_at')->whereIn('id', $tripIds)->orderBy('departure_at', 'ASC')->get();

        $titleTrips = "";
        $tanggalPeriode = "";
        foreach ($umrohTrips as $key => $value) {
            if(count($umrohTrips) > 1) {
                $titleTrips = $umrohTrips[0]->title . " hingga " . $umrohTrips[count($umrohTrips) - 1]->title;
                $tanggalPeriode = Carbon::parse($umrohTrips[0]->departure_at)->isoFormat('D MMMM Y') . " s/d " . Carbon::parse($umrohTrips[count($umrohTrips) - 1]->departure_at)->isoFormat('D MMMM Y');
            } else {
                $titleTrips = $umrohTrips[0]->title;
                $tanggalPeriode = Carbon::parse($umrohTrips[0]->departure_at)->isoFormat('D MMMM Y');
            }

            $totalParticipant += DB::table('participant_umroh_trips')->where('umroh_trip_id', $value->id)->count();
        }
        $data = [
            'umrohTrips' => $umrohTrips,
            'totalTrip' => count($tripIds),
            'titleTrip' => $titleTrips,
            'tanggalPeriode' => $tanggalPeriode,
            'totalParticipant' => $totalParticipant,
            'totalQuestion' => $totalQuestion,
            'forms' => $forms
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.survey_multiple_export_report', $data);
        $this->pdf->setOption('enable-local-file-access', true)
        ->setOption('page-size', 'A4')
        ->setOption('orientation', 'Landscape')
        ->setOption('margin-top', '0cm')
        ->setOption('margin-left', '0cm')
        ->setOption('margin-right', '0cm')
        ->setOption('margin-bottom', '0cm');
    }

    public function download()
    {
        return $this->pdf->download("SURVEY_REPORT_".$this->data['titleTrip'].'.pdf');
    }

    public function output()
    {
        return $this->pdf->output();
    }

    public function stream()
    {
        return $this->pdf->stream();
    }

    public function html()
    {
        return view('pdf.survey_multiple_export_report', $this->data)->render();
    }
}
