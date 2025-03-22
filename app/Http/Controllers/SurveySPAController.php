<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormQuestion;
use App\Models\FormAnswer;
use App\Http\Requests\StoreSurveyRequest;
use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SummarySurveyQuestionExport;
use App\Exports\SummarySurveyExport;
use Illuminate\Support\Str;
use DB;
use App\File\PDF\SurveyReport;
use App\File\PDF\SurveyMultipleReport;
use App\File\PDF\SurveyMultipleExportReport;
use App\Jobs\GenerateDocumentSurvey;

class SurveySPAController extends Controller
{
    const SPA_PATH = '/survey';

    public function __construct()
    {
        $this->middleware('permission:survey-view')->only(['index', 'show', 'querySurveys', 'barcode']);
        $this->middleware('permission:survey-add-or-edit')->only(['store', 'import']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orderBy = request()->query('sortBy', 'id');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage', 10);
        return response()->json(
            Form::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSurveyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSurveyRequest $request)
    {
        DB::transaction(function() use($request) {
            $uid = auth()->user()->id;
            $request->merge([
                'created_by' => $uid,
                'updated_by' => $uid
            ]);

            $survey = Form::updateOrCreate(['id' => $request->id], $request->except(['image']));

            $questions = json_decode($request->questions);
            foreach ($questions as $key => $question) {
                $option_value = ($question->option_value != null) ? json_encode($question->option_value) : null;
                if($question->type == "scale") {
                    $options = array();
                    for ($i=$question->scale_bottom; $i <= $question->scale_top; $i++) {
                        $options[] = [
                            'value' => $i
                        ];
                    }
                    $option_value = json_encode($options);
                }

                FormQuestion::updateOrCreate([
                    'id' => $question->id ?? 0,
                ],
                [
                    'form_id' => $survey->id,
                    'question' => $question->question,
                    'type' => $question->type,
                    'required' => $question->required ?? false,
                    'option_value' => $option_value,
                    'created_by' => $uid,
                    'updated_by' => $uid,
                    'order' => $key,
                    'has_other' => $question->has_other ?? false,
                    'scale_top' => $question->scale_top ?? null,
                    'scale_bottom' => $question->scale_bottom ?? null,
                    'label_scale_top' => $question->label_scale_top ?? null,
                    'label_scale_bottom' => $question->label_scale_bottom ?? null,
                    'section_id' => $question->section_id ?? null,
                    'view_in_report' => $question->view_in_report ?? false,
                    'title_in_report' => $question->title_in_report ?? null,
                    'model_in_report' => $question->model_in_report ?? null
                ]);
            }

            if($request->question_deleted_ids)
                FormQuestion::whereIn('id', json_decode($request->question_deleted_ids))->delete();
        });

        

        return response()->json($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function show(Form $survey)
    {
        $survey->questions = FormQuestion::where('form_id', $survey->id)->orderBy('order','asc')->get();
        foreach ($survey->questions as $value) {
            $value->option_value = json_decode($value->option_value);
        }
        return response()->json($survey->toArray());
    }

    public function destroy(Form $survey)
    {
        $survey->delete();
    }

    public function querySurveys(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = Form::select(['id', 'title'])->get();
        return response()->json($result);
    }

    public function summary($id, Request $request)
    {
        $survey = Form::select(['id', 'title', 'respons'])->where('id', $id)->first();
        $query = FormQuestion::where('form_id', $survey->id)->orderBy('order','asc');
        // $query->whereIn('id', [29,44]);
        if($request->questionId) {
            $query->where(function($q)use($request) {
                $q->whereIn('id', $request->questionId);
                // $q->orWhere('question', 'like', '%paket%');
            });
        }
        $survey->questions = $query->get();
        foreach ($survey->questions as $question) {
            if($question->section_id) {
                $question->section_options = json_decode(DB::table('form_sections')->select('options')->where('id', $question->section_id)->first()->options);
            }

            $question->option_value = json_decode($question->option_value);

            // TIDAK PUAS SANGAT TIDAK PUAS SECTION
            if($question->option_value) {
                foreach($question->option_value as $item) {
                    if(Str::contains(strtolower($question->question), 'paket')) {
                        $questionAboutPackage = $question->id;
                    }
                    if(in_array(strtolower($item->value), ['tidak puas','sangat tidak puas'])) {
                        $queryData = DB::table('form_answers')->select([
                            'answer',
                            'session_id',
                        ])
                        ->where('question_id', $question->id);
                        if($request->trip) {
                            $queryData->whereIn('form_answers.umroh_trip_id', $request->trip);
                        }
                        if(!empty($request->date)) {
                            $dateXplode = explode('to', request()->date);
                            $exDateStart = str_replace('/','-',$dateXplode[0]);
                            if(isset($dateXplode[1]))
                                $exDateEnd = str_replace('/','-',$dateXplode[1]);
                            $date = date('Y-m-d', strtotime($exDateStart));
                            $start = date('Y-m-d 00:00:00', strtotime($exDateStart));
                            $end = date('Y-m-d 24:00:00', strtotime($exDateEnd??$exDateStart));
                            $queryData->whereBetween('form_answers.created_at', [$start, $end]);
                        }
                        $queryData->groupByRaw('answer,session_id');
                        $answerData = $queryData->get();
                        $tidakpuas = array();
                        $sangattidakpuas = array();
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
                        }

                        $aggregate = [];
                        foreach($tidakpuas as $data) {
                            if(!isset($aggregate[$data['paket']])) $aggregate[$data['paket']] = 0;
                            $aggregate[$data['paket']] += $data['point'];
                        }
                        $output = [];
                        foreach($aggregate as $paket => $points) {
                            // $output[] = ['paket' => $paket, 'points' => $points];
                            $output[] = "{$paket} - {$points} Orang";
                        }
                        $question->tidakpuas = $output;

                        $aggregate = [];
                        foreach($sangattidakpuas as $data) {
                            if(!isset($aggregate[$data['paket']])) $aggregate[$data['paket']] = 0;
                            $aggregate[$data['paket']] += $data['point'];
                        }
                        $output = [];
                        foreach($aggregate as $paket => $points) {
                            //$output[] = ['paket' => $paket, 'points' => $points];
                            $output[] = "{$paket} - {$points} Orang";

                        }
                        $question->sangattidakpuas = $output;
                    }
                }
            }

            $queryAnswer = FormAnswer::select('form_answers.*', 'umroh_trips.title as trip')
            ->leftjoin('umroh_trips', 'umroh_trips.id', 'form_answers.umroh_trip_id')
            ->where('question_id', $question->id)
            ->orderBy('created_at', 'desc');
            if($request->trip) {
                $queryAnswer->whereIn('form_answers.umroh_trip_id', $request->trip);
            }
            if(!empty($request->date)) {
                $dateXplode = explode('to', request()->date);
                $exDateStart = str_replace('/','-',$dateXplode[0]);
                if(isset($dateXplode[1]))
                    $exDateEnd = str_replace('/','-',$dateXplode[1]);
                $date = date('Y-m-d', strtotime($exDateStart));
                $start = date('Y-m-d 00:00:00', strtotime($exDateStart));
                $end = date('Y-m-d 24:00:00', strtotime($exDateEnd??$exDateStart));
                $queryAnswer->whereBetween('form_answers.created_at', [$start, $end]);
            }
            $answers = $queryAnswer->get();

            if(in_array($question->type, ['checkbox','radio','option','scale'])) {
                $queryData = FormAnswer::select([
                    DB::raw('count(id) as count'),
                    'answer'
                ])
                ->where('question_id', $question->id);
                if($request->trip) {
                    $queryData->whereIn('form_answers.umroh_trip_id', $request->trip);
                }
                if(!empty($request->date)) {
                    $dateXplode = explode('to', request()->date);
                    $exDateStart = str_replace('/','-',$dateXplode[0]);
                    if(isset($dateXplode[1]))
                        $exDateEnd = str_replace('/','-',$dateXplode[1]);
                    $date = date('Y-m-d', strtotime($exDateStart));
                    $start = date('Y-m-d 00:00:00', strtotime($exDateStart));
                    $end = date('Y-m-d 24:00:00', strtotime($exDateEnd??$exDateStart));
                    $queryData->whereBetween('form_answers.created_at', [$start, $end]);
                }
                $chartData = $queryData->groupBy('answer')->get();
            }

            if($question->type == 'checkbox') {
                $data = array();
                $series = array();
                foreach ($chartData as $row) {
                    $data[] = $row->count;
                    $series[] = $row->answer;
                }
                $question->chartSeries = [
                    [
                        'data' => $data
                    ]
                ];

                $question->chartOptions = [
                    'chart' => [
                        'type' => 'bar',
                        'toolbar' => [
                            'show' => true
                        ]
                    ],
                    'plotOptions' => [
                        'bar' => [
                            'borderRadius' => 4,
                            'borderRadiusApplication' => 'end',
                            'horizontal' => true,
                        ]
                    ],
                    'xaxis' => [
                        'categories' => $series,
                    ]
                ];
            }

            if($question->type == 'scale') {
                $data = array();
                $series = array();
                foreach ($chartData as $row) {
                    $data[] = $row->count;
                    $series[] = $row->answer;
                }
                $question->chartSeries = [
                    [
                        'data' => $data
                    ]
                ];

                $question->chartOptions = [
                    'chart' => [
                        'type' => 'bar',
                        'toolbar' => [
                            'show' => true
                        ]
                    ],
                    'plotOptions' => [
                        'bar' => [
                            'borderRadius' => 4,
                            'borderRadiusApplication' => 'end',
                        ]
                    ],
                    'xaxis' => [
                        'categories' => $series,
                    ]
                ];
            }

            $answerOthers = [];
            if(in_array($question->type, ['radio','option'])) {
                $data = array();
                $series = array();
                $arrayAnswer = [];
                foreach($question->option_value as $row) {
                    $arrayAnswer[] = $row->value;
                }
                $otherCount = 0;
                foreach ($chartData as $row) {
                    if(in_array($row->answer, $arrayAnswer)) {
                        $data[] = $row->count;
                        $series[] = $row->answer;
                    } else {
                        $otherCount = $row->count;
                        $answerOthers[] = $row->answer;
                    }
                }
                if($otherCount > 0) {
                    // $series[] = 'Others';
                    // $data[] = $otherCount;
                }
                $question->chartSeries = $data;

                $question->chartOptions = [
                    'chart' => [
                        'type' => 'pie',
                        'toolbar' => [
                            'show' => true
                        ]
                    ],
                    'legend' => array(
                        'position' => 'bottom'
                    ),
                    'labels'=> $series
                ];
            }

            $question->answers = $answers;
            $question->answer_others = $answerOthers;
        }
        return response()->json($survey->toArray());
    }

    public function tripSearch(Request $request)
    {
        $trips = FormQuestion::select(['umroh_trips.id','umroh_trips.title'])
        ->where('form_id', $request->id)
        ->join('form_answers', 'form_questions.id', 'form_answers.question_id')
        ->join('umroh_trips', 'umroh_trips.id', 'form_answers.umroh_trip_id')
        ->groupBy('umroh_trips.id')
        ->orderBy('umroh_trips.departure_at', 'DESC')->get();
        return response()->json($trips->toArray());
    }

    public function exportSummaryQuestion(Request $request) {
        $question = FormQuestion::find($request->questionId);
        $form = Form::find($question->form_id);
        $fileName = $form->title ?? '';

        $storageKey = "Summary-" . $fileName . ".xlsx";
        return Excel::download(new SummarySurveyQuestionExport($question->id, $request), $storageKey);
    }

    public function exportSummary(Request $request) {
        GenerateDocumentSurvey::dispatch($request->form_id, (object)$request->all());
        return response()->json(['success' => true]);
    }

    public function updateAnswerSection(Request $request)
    {
        $answer = FormAnswer::find($request->id)->update($request->only(['section_id', 'section_option']));
    }

    public function updateAnswer(Request $request)
    {
        $answer = FormAnswer::find($request->id)->update($request->only(['answer']));
    }

    public function surveyReport($id, Request $request)
    {
        return (new SurveyReport($id, $request))->html();
    }

    public function surveyMultipleReport(Request $request)
    {
        return (new SurveyMultipleReport($request))->html();
    }

    public function downloadSurveyReport(Request $request){
        return (new SurveyMultipleExportReport($request))->download();
    }
}
