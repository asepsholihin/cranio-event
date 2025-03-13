<?php

namespace App\Exports;

use App\Models\FormAnswer;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SummarySurveyExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents, WithCustomStartCell
{
    const START_ROW = 2;

    private $form;
    private $questions;
    private $options;
    private $request;
    private $rowNumber;

    public function __construct($form, $request)
    {
        $this->request = $request;
        $this->form = $form;
        $this->questions = DB::table('form_questions')->whereNull('deleted_at')->select('id','question','type')->where('form_id', $this->form->id)->orderBy('order', 'ASC')->get();
        $this->options = array('checkbox','radio','option','scale');
        $this->rowNumber = 0;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 18]],
            2    => ['font' => ['bold' => true]],
            'A'  => ['alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            'B'  => ['alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
            'C'  => ['alignment' => ['vertical' => Alignment::VERTICAL_CENTER]],
        ];
    }

    public function headings(): array
    {
        $headingRow = [
            'No.',
            'Session ID',
            'Keberangkatan'
        ];

        $headingRowQuestions = [];
        foreach ($this->questions as $item) {
            $headingRowQuestions[] = $item->question;
        }

        $headingRow = array_merge($headingRow, $headingRowQuestions);
        return $headingRow;
    }

    /**
    * @var Participant $participant
    */
    public function map($participant): array
    {
        $this->rowNumber += 1;

        $headingRow = [
            $this->rowNumber,
            $participant->session_id,
            $participant->trip,
        ];

        $headingRowAnswers = [];
        foreach ($this->questions as $item) {
            $answers = DB::table('form_answers')->select('answer')->where('question_id', $item->id)->where('session_id', $participant->session_id)->get()->pluck('answer')->implode(',');
            $headingRowAnswers[] = $answers;
        }

        $headingRow = array_merge($headingRow, $headingRowAnswers);
        return $headingRow;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = FormAnswer::select('form_answers.session_id','umroh_trips.title as trip')
        ->leftjoin('umroh_trips', 'umroh_trips.id', 'form_answers.umroh_trip_id')
        ->join('form_questions', 'form_questions.id', 'form_answers.question_id')
        ->where('form_questions.form_id', $this->form->id)
        ->whereNull('form_questions.deleted_at')
        ->orderBy('form_answers.session_id', 'desc')
        ->groupByRaw('form_answers.session_id, umroh_trips.id');
        if($this->request->trip) {
            $query->whereIn('form_answers.umroh_trip_id', $this->request->trip);
        }
        if(!empty($this->request->date)) {
            $dateXplode = explode('to', $this->request->date);
            $exDateStart = str_replace('/','-',$dateXplode[0]);
            if(isset($dateXplode[1]))
                $exDateEnd = str_replace('/','-',$dateXplode[1]);
            $date = date('Y-m-d', strtotime($exDateStart));
            $start = date('Y-m-d 00:00:00', strtotime($exDateStart));
            $end = date('Y-m-d 24:00:00', strtotime($exDateEnd??$exDateStart));
            $query->whereBetween('form_answers.created_at', [$start, $end]);
        }
        return $query;
    }

     /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $phpSpreadSheet = $event->sheet->getDelegate();
                $phpSpreadSheet->getRowDimension('1')->setRowHeight(48);
                // $phpSpreadSheet->mergeCells('A1:C1')->setCellValue('A1', "Pertanyaan: {$this->question->question}");
                $phpSpreadSheet->getColumnDimension('C')->setAutoSize(false)->setWidth(100);
                $phpSpreadSheet->getStyle('C')->getAlignment()->setWrapText(true);

            },
        ];
    }
    
    public function startCell(): string
    {
        return 'A' . self::START_ROW;
    }
}
