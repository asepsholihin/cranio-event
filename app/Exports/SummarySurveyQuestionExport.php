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

class SummarySurveyQuestionExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents, WithCustomStartCell
{
    const START_ROW = 2;

    private $questionId;
    private $question;
    private $options;
    private $request;
    private $rowNumber;

    public function __construct($questionId, $request)
    {
        $this->request = $request;
        $this->questionId = $questionId;
        $this->question = DB::table('form_questions')->select('question','type')->where('id', $this->questionId)->first();
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
        if(in_array($this->question->type, $this->options)) {
            return [
                'No',
                'Keberangkatan',
                'Jawaban',
                'Jumlah Jawaban Sama',
            ];
        } else {
            return [
                'No',
                'Keberangkatan',
                'Jawaban'
            ];
        }
    }

    /**
    * @var Participant $participant
    */
    public function map($participant): array
    {
        $this->rowNumber += 1;
        if(in_array($this->question->type, $this->options)) {
            return [
                $this->rowNumber,
                $participant->trip,
                $participant->answer,
                $participant->count,
            ];
        } else {
            return [
                $this->rowNumber,
                $participant->trip,
                $participant->answer,
            ];
        }
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = FormAnswer::leftjoin('umroh_trips', 'umroh_trips.id', '=', 'form_answers.umroh_trip_id');
        if (! empty($this->questionId)) {
            $query->where('form_answers.question_id', $this->questionId);
        }
        if(in_array($this->question->type, $this->options)) {
            $query->select(['form_answers.answer','umroh_trips.title as trip', DB::raw('count(answer) as count')]);
            $query->groupByRaw('form_answers.answer, umroh_trips.id');
            $query->orderByRaw('umroh_trips.departure_at desc, count desc');
        } else {
            $query->select('form_answers.*','umroh_trips.title as trip');
            $query->orderBy('form_answers.created_at', 'desc');
        }
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
                $phpSpreadSheet->mergeCells('A1:C1')->setCellValue('A1', "Pertanyaan: {$this->question->question}");
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
