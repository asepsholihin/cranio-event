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

class SurveyReport
{
    private $pdf;
    private $data;

    public function __construct($formId, $request)
    {
        App::setLocale('id');

        $totalQuestion = 0;
        $totalRespon = 0;
        $totalParticipant = 0;

        $tripIds = explode(",", $request['trip']);

        if($request['date']) {
            $dateXplode = explode('to', $request['date']);
            $exDateStart = str_replace('/','-',$dateXplode[0]);
            if(isset($dateXplode[1]))
                $exDateEnd = str_replace('/','-',$dateXplode[1]);
            $date = date('Y-m-d', strtotime($exDateStart));
            $start = date('Y-m-d 00:00:00', strtotime($exDateStart));
            $end = date('Y-m-d 24:00:00', strtotime($exDateEnd??$exDateStart));
            $tripIds = DB::table('umroh_trips')->whereBetween('umroh_trips.departure_at', [$start, $end])->get()->pluck('id');
        }
        $form = Form::where('forms.id', $formId)->first();
        $questions = FormQuestion::where('form_id', $formId)->orderBy('order','asc')->get();
        foreach ($questions as $question) {
            $totalQuestion += 1;
            
            if(in_array($question->type, ['checkbox','radio','option','scale'])) {
                $queryData = FormAnswer::select([
                    DB::raw('count(id) as count'),
                    'answer'
                ])
                ->where('question_id', $question->id)
                ->whereIn('umroh_trip_id', $tripIds);
                $data = $queryData->orderByRaw('count DESC')->groupBy('answer')->get();

                $question->answers = $data;
            }
        }
        $totalRespon = DB::table('form_answers')->select('session_id')
        ->join('form_questions', 'form_questions.id', 'form_answers.question_id')
        ->whereIn('umroh_trip_id', $tripIds)->where('form_questions.form_id', $formId)->groupBy('session_id')->get()->count();
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
            'totalRespon' => $totalRespon,
            'form' => $form,
            'questions' => $questions
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.survey_report', $data);
        $this->pdf->setOption('enable-local-file-access', true)
        ->setOption('page-size', 'A4')
        ->setOption('margin-top', '0cm')
        ->setOption('margin-left', '0cm')
        ->setOption('margin-right', '0cm')
        ->setOption('margin-bottom', '0cm');
    }

    public function download()
    {   
        return $this->pdf->download("Sertifikat_".$this->participant->name."_".$this->umrohTrip->title.'.pdf');
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
        return view('pdf.survey_report', $this->data)->render();
    }
}
