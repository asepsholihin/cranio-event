<?php
namespace App\File\PDF;

use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use App\Models\ParticipantUmrohTrip;
use App\Models\ParticipantFile;
use DB;

class DocumentPassports
{
    private $pdf;
    private $data;

    public function __construct($umrohTripId, $request, $page, $limit)
    {
        App::setLocale('id');
        
        $umrohTrip = DB::table('umroh_trips')->select('title')->find($umrohTripId);
        $queryParticipantIds = ParticipantUmrohTrip::join('participant', 'participant_umroh_trips.participant_id', '=', 'participant.id')
        ->select('participant_id','participant.name', 'participant.name_in_passport','participant_umroh_trips.no_urut')
        ->where('participant_umroh_trips.umroh_trip_id', $umrohTripId);
        if(!empty($request['packageUmrohTripId'])) {
            $queryParticipantIds->where('participant_umroh_trips.package_umroh_trip_id', $request['packageUmrohTripId']);
        }
        if(!empty($request['booking'])) {
            $queryParticipantIds->where('participant_umroh_trips.order_umroh_trip_id', $request['booking']);
        }
        if(!empty($request['q'])) {
            $queryParticipantIds->where('participant.name', 'like', '%'.$request['q'].'%');
        }
        if(!empty($request['busGroup'])) {
            $queryParticipantIds->where('participant_umroh_trips.group_bus', $request['busGroup']);
        }
        $participants = $queryParticipantIds->orderBy('participant_umroh_trips.no_urut', 'ASC')->skip(($page - 1) * $limit)->take($limit)->get();

        foreach($participants as $participant) {
            $files = ParticipantFile::join('participant', 'participant_files.participant_id', '=', 'participant.id')
            ->select(['participant.ji_code','participant.name', 'participant_files.file_path', 'participant_files.title'])
            ->join('participant_umroh_trips', 'participant_umroh_trips.participant_id', 'participant.id')
            ->where('participant.id', $participant->participant_id)
            ->where('participant_umroh_trips.umroh_trip_id', $umrohTripId)
            ->where('participant_files.title', 'Passport')
            ->where('participant_files.file_type', 'like', '%image%')
            ->whereNotNull('participant_files.file_path')
            ->orderBy('participant_files.id', 'ASC')
            ->limit(2)
            ->get();

            $participant->files = $files;
        }

        $data = [
            'umrohTrip' => $umrohTrip,
            'participants' => $participants,
            'page' => $page
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.document_passports', $data);
        $this->pdf->setOption('encoding', 'utf-8')
            ->setOption('enable-local-file-access', true)
            ->setOption('page-width', '29.7cm')
            ->setOption('page-height', '21cm')
            ->setOption('margin-top', '1cm')
            ->setOption('margin-left', '1cm')
            ->setOption('margin-right', '1cm')
            ->setOption('margin-bottom', '1cm')
            ->setTimeout(3600);
    }

    public function download()
    {
        return $this->pdf->download('DOKUMEN_PASSPORT_'.strtoupper($this->data['umrohTrip']->title).'.pdf');
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
        return view('pdf.document_passports', $this->data)->render();
    }
}
