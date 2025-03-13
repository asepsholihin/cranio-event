<?php
namespace App\File\PDF;

use App\Models\Participant;
use App\Models\UmrohTrip;
use Illuminate\Support\Str;
use SnappyImage;
use PDF;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;

class PasPhoto
{
    private $pdf;
    private $umrohTripId;
    private $umrohTrip;
    private $data;

    public function __construct($umrohTripId, $busGroup=null)
    {
        App::setLocale('id');
        $this->umrohTripId = $umrohTripId;

        $this->umrohTrip = UmrohTrip::findOrFail($umrohTripId);
        $queryParticipant = Participant::
        select([
            'participant.*',
            'participant_umroh_trips.no_urut',
            DB::raw('
            (CASE 
                WHEN participant_umroh_trips.role_type = 3 THEN \'Mutawwif\'
                ELSE \'\' END) AS crew'
            ),
        ])
        ->join('participant_umroh_trips', 'participant.id', '=', 'participant_umroh_trips.participant_id' )
        ->join('umroh_trips', 'umroh_trips.id', '=', 'participant_umroh_trips.umroh_trip_id' )
        ->where('participant_umroh_trips.umroh_trip_id', $umrohTripId);
        if($busGroup) {
            $queryParticipant->where('participant_umroh_trips.group_bus', $busGroup);
        }
        
        $participant = $queryParticipant->orderByRaw('no_urut ASC NULLS LAST')->get();
        $data = [
            'participants' => $participant,
            'umrohTrip' => $this->umrohTrip
        ];
        $this->data = $data;

        if($this->umrohTrip->category_id == 2) {
            $this->pdf = PDF::loadView('pdf.pas_photos_haji', $data);
        } else {
            $this->pdf = PDF::loadView('pdf.pas_photos', $data);
        }
        $this->pdf->setOption('enable-local-file-access', true)
        ->setOption('encoding', 'utf-8')
        ->setOption('margin-top', '.5cm')
        ->setOption('margin-left', '.5cm')
        ->setOption('margin-right', '.5cm')
        ->setOption('margin-bottom', '.5cm')
        ->setOption('orientation', 'Landscape');
    }

    public function html()
    {
        if($this->umrohTrip->category_id == 2)
            return view('pdf.pas_photos_haji', $this->data)->render();

        return view('pdf.pas_photos', $this->data)->render();
    }
    
    public function stream()
    {
        return $this->pdf->stream();
    }

    public function download()
    {
        return $this->pdf->download("Daftar_Foto_Participant_".$this->umrohTrip->title.'.pdf');
    }

    public function word()
    {
        $participants = $this->data['participants'];
        $umrohTrip = $this->data['umrohTrip'];

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        // Begin code
        $section = $phpWord->addSection();

        $headerStyle = ['name' => 'Mulish', 'size' => 12, 'bold' => true];

        // 1. Basic table

        $rows = ceil(count($participants)/5);
        $cols = 5;
        $title = strtoupper("Daftar Foto Participant " . $umrohTrip['title']);
        $header = $section->addHeader();
        $header->addText( $title. "<w:br/><w:br/>", $headerStyle, ['align'=>'center']);

        $tableStyle = array(
            'borderColor' => '555555',
            'borderSize'  => 6,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
            'cellMargin' => 100
        );

        $phpWord->addTableStyle('myTable', $tableStyle, null);
        $table = $section->addTable('myTable');

        $paragraphStyleName = 'pStyle';
        $phpWord->addParagraphStyle($paragraphStyleName, ['align'=>'center','spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(6)]);


        $offset = 0; 
        $num_columns = 5;
        while($slice = array_slice($participants->toArray(),$offset,$num_columns)){
            $offset += $num_columns;
            $table->addRow();
            foreach($slice as $n) {
                $noUrut = ($n['no_urut'])?$n['no_urut'].'. ':'';
                $namaParticipant = strtoupper($n['name_in_passport']??$n['name']);
                $cell = $table->addCell();
                $textrun = $cell->addTextRun($paragraphStyleName);
                $textrun->addText($n['crew'], ['name' => 'Mulish','bold' => true,'size'=>6], null);
                if($n['profile_thumbnail'] != '') {
                    $textrun->addImage($n['profile_thumbnail'],array('width'=>80,'height'=>120));
                } else {
                    $textrun->addShape(
                        'rect',
                        array(
                            'roundness' => 0,
                            'frame'     => array('width' => 80, 'height' => 120),
                            'fill'      => array('color' => '#FFFFFF'),
                            'outline'   => array('color' => '#FFFFFF', 'weight' => 1),
                            'shadow'    => null,
                        )
                    );
                }
                $textrun->addText("<w:br/>".$noUrut.$namaParticipant, ['name' => 'Mulish','bold' => true,'size'=>9], null);
            }
        }

        // Save file
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $filename = storage_path("app/Daftar_Foto_Participant_".$this->umrohTrip->title.'.docx');
        $objWriter->save($filename);
        return response()->download($filename)->deleteFileAfterSend(true);
    }
}
