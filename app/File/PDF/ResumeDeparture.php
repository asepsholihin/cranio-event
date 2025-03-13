<?php
namespace App\File\PDF;

use App\Models\ParticipantUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\PackageUmrohTrip;
use Illuminate\Support\Str;
use SnappyImage;
use PDF;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ResumeDeparture
{
    private $umrohTrip;
    private $pdf;
    private $data;

    public function __construct($umrohTripId)
    {
        App::setLocale('id');

        $umrohTrip = UmrohTrip::findOrFail($umrohTripId);
        $this->umrohTrip = $umrohTrip;
        $packages = PackageUmrohTrip::where('umroh_trip_id', $umrohTripId)
        ->select(['package_umroh_trips.*',
            DB::raw('(CASE 
                WHEN package_umroh_trips.name iLIKE \'%Sapphire Plus%\' THEN \'a\' 
                WHEN package_umroh_trips.name iLIKE \'%Sapphire%\' THEN \'b\' 
                WHEN package_umroh_trips.name iLIKE \'%Ruby%\' THEN \'c\' 
                WHEN package_umroh_trips.name iLIKE \'%Onyx%\' THEN \'d\' 
                WHEN package_umroh_trips.name iLIKE \'%Yaqin%\' THEN \'e\' 
                ELSE \'f\' END
            ) AS package_type'),
        ])
        ->orderBy('package_type', 'ASC')->get();
        $participant = ParticipantUmrohTrip::join('participant', 'participant.id', 'participant_umroh_trips.participant_id')
        ->select([
            DB::raw('COUNT(*) as total_participant'),
            DB::raw('SUM(CASE WHEN gender = 1 THEN 1 ELSE 0 END) as total_male'),
            DB::raw('SUM(CASE WHEN gender = 2 THEN 1 ELSE 0 END) as total_female'),
            DB::raw('SUM(CASE WHEN gender = 1 AND DATE_PART(\'YEAR\', AGE(CURRENT_DATE, birth_date)) >= 60 THEN 1 ELSE 0 END) as total_male_old'),
            DB::raw('SUM(CASE WHEN gender = 2 AND DATE_PART(\'YEAR\', AGE(CURRENT_DATE, birth_date)) >= 60 THEN 1 ELSE 0 END) as total_female_old'),
            DB::raw('SUM(CASE WHEN gender = 1 AND DATE_PART(\'YEAR\', AGE(CURRENT_DATE, birth_date)) < 17 THEN 1 ELSE 0 END) as total_male_child'),
            DB::raw('SUM(CASE WHEN gender = 2 AND DATE_PART(\'YEAR\', AGE(CURRENT_DATE, birth_date)) < 17 THEN 1 ELSE 0 END) as total_female_child'),
        ])
        ->whereNot('participant_umroh_trips.role_type', 3)
        ->where('umroh_trip_id', $umrohTripId)->first();

        // Participant Nakes
        $participantNakes = ParticipantUmrohTrip::join('participant', 'participant.id', 'participant_umroh_trips.participant_id')
        ->select(['front_title','name','back_title'])
        ->where('umroh_trip_id', $umrohTripId)
        ->whereNot('participant_umroh_trips.role_type', 3)
        ->where(function($q) {
            $q->whereRaw('job like any (array[\'%dokter%\', \'%perawat%\', \'%psi%\', \'%nurse%\', \'%suster%\', \'%nakes%\', \'%bidan%\'])')
            ->orWhere('is_nakes', 1)
            ->orWhere('is_doctor', 1);
        })
        ->get();

        // Participant TNI/Polri
        $participantTniPolri = ParticipantUmrohTrip::join('participant', 'participant.id', 'participant_umroh_trips.participant_id')
        ->select(['front_title','name','back_title'])
        ->where('umroh_trip_id', $umrohTripId)
        ->whereNot('participant_umroh_trips.role_type', 3)
        ->where(function($q) {
            $q->whereRaw('job like any (array[\'%tni%\', \'%polri%\', \'%polisi%\', \'%tentara%\'])')
            ->orWhere('is_tni_polri', 1);
        })
        ->get();

        // Participant Province
        $participantProvince = ParticipantUmrohTrip::join('participant', 'participant.id', 'participant_umroh_trips.participant_id')
        ->select(['ktp_province', DB::raw('count(ktp_province) as total')])
        ->where('umroh_trip_id', $umrohTripId)
        ->whereNot('participant_umroh_trips.role_type', 3)
        ->groupBy('ktp_province')
        ->get();

        $destinations = "(MADINAH - MAKKAH - JEDDAH)";
        if ($umrohTrip->location_destination_id) {
            $location_ids = Str::replace('[', '', $umrohTrip->location_destination_id);
            $location_ids = Str::replace(']', '', $location_ids);
            $location_ids = explode(',', $location_ids);
            $destinations = "";
            foreach ($location_ids as $value) {
                if($value != "") {
                    $city = DB::table('master_cities')->select(['name'])->where('id', $value)->first()->name ?? '';
                    $destinations .= $city . " - ";
                }
            }
            $destinations = "(". rtrim($destinations, " - ") . ")";
        }

        $data = [
            'umrohTrip' => $umrohTrip,
            'destinations' => $destinations,
            'packages' => $packages,
            'participant' => $participant,
            'participantNakes' => $participantNakes,
            'participantTniPolri' => $participantTniPolri,
            'participantProvince' => $participantProvince
        ];
        $this->data = $data;

        $this->pdf = PDF::loadView('pdf.resume_departure', $data);
        $this->pdf->setOption('enable-local-file-access', true)
        ->setOption('page-size', 'A4')
        ->setOption('margin-top', '2cm')
        ->setOption('margin-left', '2cm')
        ->setOption('margin-right', '2cm')
        ->setOption('margin-bottom', '2cm');
    }

    public function download()
    {   
        return $this->pdf->download("PAKET INFO KEBERANGKATAN ".strtoupper($this->umrohTrip->title).'.pdf');
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
        return view('pdf.resume_departure', $this->data)->render();
    }
}
