<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormQuestion;
use App\Models\FormAnswer;
use Illuminate\Http\Request;
use App\Exceptions\ErrorMessageException;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class SurveyController extends Controller
{
    public function __construct()
    {

    }

    public function saveResponse(Request $request)
    {
        $result = DB::transaction(function() use($request) {
            $survey = Form::find($request->survey_id);
            $questionRequired = DB::table('form_questions')->where('form_id', $survey->id)->where('required', true)->whereNull('deleted_at')->get();
            $survey->increment('respons');
            $umrohTripId = $request->trip ?? null;
            if($survey->required_umroh_trip) {
                if(!isset($request->trip)) {
                    throw new ErrorMessageException("Harap memilih keberangkatan");
                }
            }

            $uuid = Str::uuid()->toString();

            // Mapping Fields
            $questions = $request->except(['survey_id','trip']);
            $answer = array();

            $form_filled = array();
            foreach($questions as $key => $value) {
                $explode = explode('_', $key, 3);
                if(isset($explode[1])) {
                    $form_filled[] = $explode[1];
                }
            }
            
            foreach($questionRequired as $key => $value) {
                if(!in_array($value->id, $form_filled)) {
                    throw new ErrorMessageException("Harap mengisi pertanyaan bertanda bintang (*)");
                }
            }

            foreach($questions as $key => $value) {
                $update = array();
                $explode = explode('_', $key, 3);
                if($explode[0] == "id") {
                    $update = [
                        'question_id' => $explode[1],
                        'answer' => $value
                    ];
                }
                if(in_array($explode[0], array("checkbox", "radio"))) {
                    $answer_question = str_replace('_', ' ', $explode[2]);
                    if($explode[2] == "other") {
                        $answer_question = $value;
                    }
                    $update = [
                        'question_id' => $explode[1],
                        'answer' => $answer_question
                    ];
                }

                $update = array_merge($update, [
                    'umroh_trip_id' => $umrohTripId,
                    'session_id' => $uuid,
                    'name' => '',
                    'no_hp' => ''
                ]);
                if(isset($update['question_id'])) {
                    $checkValidation = FormQuestion::find($explode[1]);
                    if($checkValidation->required) {
                        if(empty($update['answer'])) {
                            throw new ErrorMessageException("Harap mengisi pertanyaan bertanda bintang (*)");
                        }
                    }
                    $answer[] = FormAnswer::create($update);
                }
            }

            return $answer;
        });

        return response()->json([
            'success' => true,
            'message'  => 'Jazakumullah khairan, sudah berkenan meluangkan waktu untuk mengisi survey.'
        ], 200);
    }
    
    public function show($slug)
    {
        $survey = Form::select(['id','title','description','image_url','slug','required_umroh_trip','packages'])->where('status', 1)->where('slug', $slug)->first();
        if(!$survey) {
            return response()->json([
                'success' => false,
                'message'  => 'Survey tidak tersedia',
            ], 422);
        }

        if($survey->required_umroh_trip) {
            $query = DB::table('umroh_trips')->select('umroh_trips.id','umroh_trips.title','umroh_trips.departure_at')
            ->whereDate('departure_at', '>=', Carbon::now()->subDays(30))
            ->whereDate('departure_at', '<', Carbon::now());
            if($survey->packages) {
                $query->join('package_umroh_trips', 'package_umroh_trips.umroh_trip_id', 'umroh_trips.id')
                ->whereIn('package_umroh_trips.name', explode(',', $survey->packages));
            }

            $survey->trips = $query->get();
        }
        $survey->questions = FormQuestion::where('form_id', $survey->id)->orderBy('order','asc')->get();
        foreach ($survey->questions as $value) {
            $value->option_value = json_decode($value->option_value);
        }
        return response()->json($survey->toArray());
    }
}
