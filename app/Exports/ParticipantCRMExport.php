<?php

namespace App\Exports;

use App\Models\Participant;
use App\Models\ParticipantCRM;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class ParticipantCRMExport implements FromQuery, ShouldAutoSize, WithHeadings, WithStyles, WithMapping, WithEvents, WithColumnFormatting
{
    private $request;
    private $rowNumber;

    public function __construct($request)
    {
        $this->request = $request;
        $this->rowNumber = 0;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'NIK',
            'Gender',
            'Tanggal Lahir',
            'Whatsapp',
            'Domisili (Kota)',
            'Pekerjaan',
            'Keberangkatan',
            'Tahun',
            'Package',
            'Jumlah Pax dalam Akun',
            'Transaksi Terakhir (Nilai Paket)',
        ];
    }

    /**
     * @var Participant $participant
     */
    public function map($participant): array
    {
        $this->rowNumber += 1;

        $gender = '';
        if($participant->gender == 1) {
            $gender = 'Laki-laki';
        } else if($participant->gender == 2) {
            $gender = 'Perempuan';
        }

        $frontTitle = ($participant->front_title)?$participant->front_title.".":"";
        $backTitle = ($participant->back_title)?", ".$participant->back_title:"";
        $fullname =  $frontTitle ." ". ucwords(strtolower($participant->name)) . $backTitle;
        $originPhone = substr($participant->no_hp, 2);

        $last_package_price = 0;
        $totalPax = 0;
        $historyUmroh = explode(',', $participant->history_participant_umroh_trips);
        $ids = [];
        for($i=0; $i<count($historyUmroh); $i++)
        {
            if($historyUmroh[$i]!=''){
                $ids[] = $historyUmroh[$i];
            }
        }
        $otherTrips = [];
        $transactionHistory = [];
        if(count($ids) > 0) {
            $otherTrips = DB::table('participant_umroh_trips')->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')->join('package_umroh_trips', 'package_umroh_trips.id', 'participant_umroh_trips.package_umroh_trip_id')
            ->select('umroh_trips.title', 'umroh_trips.departure_at', 'package_umroh_trips.name as package_name')
            ->whereIn('participant_umroh_trips.id', $ids)->get();

            if(end($historyUmroh)) {
                $participantUmrohTrip = DB::table('participant_umroh_trips')
                ->select('package_umroh_trip_id','room_type','order_umroh_trip_id')
                ->where('id', end($historyUmroh))->first();

                if($participantUmrohTrip) {
                    $order = DB::table('order_umroh_trips')
                    ->select('total_pax_trip')
                    ->where('id', $participantUmrohTrip->order_umroh_trip_id)->first();
                    if($order) {
                        $totalPax = $order->total_pax_trip;
                    }

                    $packageUmrohTrip = DB::table('package_umroh_trips')
                    ->select('room_single_price','room_double_price','room_triple_price','room_quad_price','room_queen_price')
                    ->where('id', $participantUmrohTrip->package_umroh_trip_id)->first();
                    if($participantUmrohTrip->room_type == "single") {
                        $last_package_price = $packageUmrohTrip->room_single_price;
                    }
                    if($participantUmrohTrip->room_type == "double") {
                        $last_package_price = $packageUmrohTrip->room_double_price;
                    }
                    if($participantUmrohTrip->room_type == "triple") {
                        $last_package_price = $packageUmrohTrip->room_triple_price;
                    }
                    if($participantUmrohTrip->room_type == "quad") {
                        $last_package_price = $packageUmrohTrip->room_quad_price;
                    }
                    if($participantUmrohTrip->room_type == "queen") {
                        $last_package_price = $packageUmrohTrip->room_queen_price;
                    }
                }
            }
        }else{
            $transactionHistory = DB::table('participant_crm_transaction_backdate_histories')
            ->select('package_name', 'umroh_trip_name', 'total_transaction', 'year')
            ->where('participant_crm_id', $participant->id)->get();
        }

        $parseOtherTrip = "";
        $yearOtherTrip = "";
        $packageOtherTrip = "";
        if(count($otherTrips) > 0){
            foreach ($otherTrips as $key => $value) {
                $parseOtherTrip .= ($key+1) . "." . $value->title . "\r\n";
                $yearOtherTrip .= date('Y', strtotime($value->departure_at)) . "\r\n";
                $packageOtherTrip .= $value->package_name . "\r\n";
            }
        }else{
            if(count($transactionHistory) > 0){
                foreach ($transactionHistory as $key => $value) {
                    $parseOtherTrip .= ($key+1) . "." . $value->umroh_trip_name . "\r\n";
                    $yearOtherTrip .= $value->year . "\r\n";
                    $packageOtherTrip .= $value->package_name . "\r\n";
                    $last_package_price += $value->total_transaction;
                }
            }else{
                $parseOtherTrip = "1." . $participant->latest_trip_name;
                $yearOtherTrip = $participant->latest_trip_year;
                $packageOtherTrip = $participant->latest_trip_package;
            }
        }
        $parseOtherTrip = rtrim($parseOtherTrip);
        if($parseOtherTrip) {
            $parseOtherTrip = $parseOtherTrip;
        }
        // YEAR LAST TRIP
        $yearOtherTrip = rtrim($yearOtherTrip);
        if($yearOtherTrip) {
            $yearOtherTrip = $yearOtherTrip;
        }
        // PACKAGE LAST TRIP
        $packageOtherTrip = rtrim($packageOtherTrip);
        if($packageOtherTrip) {
            $packageOtherTrip = $packageOtherTrip;
        }

        return [
            $this->rowNumber,
            $fullname,
            "'".$participant->nik,
            $gender,
            $participant->birth_date,
            "'".$participant->no_hp,
            $participant->city,
            $participant->job,
            $parseOtherTrip,
            // LAST TRIP YEAR
            $yearOtherTrip,
            // PACKAGE LAST TRIP
            $packageOtherTrip,
            $totalPax,
            // TRANSACTION JAMAAH
            $last_package_price,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        $query = ParticipantCRM::select(
            'participant_crm.name',
            'participant_crm.nik',
            'participant_crm.birth_date',
            'participant_crm.no_hp',
            'participant_crm.latest_trip_name',
            'participant_crm.latest_trip_package',
            'participant_crm.gender',
            'participant_crm.city',
            'participant_crm.job',
            'participant_crm.latest_trip_year'
        );
        if (! empty($this->request['umrohTripId'])) {
            $query->where('latest_trip_id', 'like', '%' . $this->request['umrohTripId'] . '%');
        }
        if (! empty($this->request['trip'])) {
            $query->select(['participant_crm.*', 'participant.name','participant_crm.participant_id',  DB::raw('
                (CASE
                    WHEN parent_account > 0 THEN \'a\'
                    ELSE \'z\' END
                ) AS pemilik_akun'
            ), 'participant.front_title', 'participant.back_title', 'participant.profile_photo_path', 'umroh_trips.title as trip_name', 'package_umroh_trips.name as trip_package']);
            $query->join('participants', 'participant.id', 'participant_crm.participant_id');
            $query->leftjoin('participant_umroh_trips', 'participant_umroh_trips.participant_id', 'participant.id');
            $query->leftjoin('umroh_trips', 'participant_umroh_trips.umroh_trip_id', 'umroh_trips.id');
            $query->leftjoin('package_umroh_trips', 'participant_umroh_trips.package_umroh_trip_id', 'package_umroh_trips.id');
            $query->where(function($q) {
                $q->where('umroh_trips.title', 'like', '%' . $this->request['trip'] . '%');
                $q->orwhere('participant_crm.latest_trip_name', 'like', '%' . $this->request['trip'] . '%');
            });
        } else {
            $query->select(['participant_crm.*', DB::raw('
                (CASE
                    WHEN parent_account > 0 THEN \'a\'
                    ELSE \'z\' END
                ) AS pemilik_akun'
            ), 'participant.front_title', 'participant.back_title', 'participant_crm.latest_trip_name as trip_name', 'latest_trip_package as trip_package']);
            $query->join('participants', 'participant.id', 'participant_crm.participant_id');
        }
        if (! empty($this->request['city'])) {
            $query->where('city', 'like', '%' . $this->request['city'] . '%');
        }
        if (! empty($this->request['province'])) {
            $query->where('province', 'like', '%' . $this->request['province'] . '%');
        }
        if (! empty($this->request['package'])) {
            $query->where('latest_trip_package', 'like', '%' . $this->request['package'] . '%');
        }
        if (! empty($this->request['gender'])) {
            $query->where('participant_crm.gender', 'like', '%' . $this->request['gender'] . '%');
        }
        if (! empty($this->request['totalAccount'])) {
            $query->where('parent_account', $this->request['totalAccount']);
        }
        if (! empty($this->request['parentAccount'])) {
            $query->where('parent_account', '>=', 1);
        }
        if (! empty($this->request['hasPhone'])) {
            $query->where(function($q) {
                $q->whereNotNull('no_hp');
                $q->orWhere('no_hp', '!=', '');
            });
        }
        if (! empty($this->request['hasInstagram'])) {
            $query->where(function($q) {
                $q->whereNotNull('instagram');
                $q->orWhere('instagram', '!=', '');
            });
        }
        if (! empty($this->request['hasLinkedIn'])) {
            $query->where(function($q) {
                $q->whereNotNull('linkedin');
                $q->orWhere('linkedin', '!=', '');
            });
        }
        if (! empty($this->request['needMerge'])) {
            $query->where(function($q) {
                $q->where('need_merge', 1);
            });
        }
        if (! empty($this->request['oldData'])) {
            $query->whereNull('last_booking_order_no');
        }
        if (!empty($this->request['totalTrip'])) {
            $totalTrip = $this->request['totalTrip'];
            if ($totalTrip <= 5)
                $query->where('total_trip', $totalTrip);
            if ($totalTrip > 5)
                $query->where('total_trip', '>', 5);
        }
        if (!empty($this->request['age'])) {
            $age = $this->request['age'];
            if ($age == 1) {
                $query->whereRaw("EXTRACT(year FROM age(current_date,participant_crm.birth_date)) < 18");
            }
            if ($age == 2) {
                $query->whereRaw("EXTRACT(year FROM age(current_date,participant_crm.birth_date)) >= 18");
                $query->whereRaw("EXTRACT(year FROM age(current_date,participant_crm.birth_date)) <= 25");
            }
            if ($age == 3) {
                $query->whereRaw("EXTRACT(year FROM age(current_date,participant_crm.birth_date)) > 25");
                $query->whereRaw("EXTRACT(year FROM age(current_date,participant_crm.birth_date)) <= 35");
            }
            if ($age == 4) {
                $query->whereRaw("EXTRACT(year FROM age(current_date,participant_crm.birth_date)) > 35");
                $query->whereRaw("EXTRACT(year FROM age(current_date,participant_crm.birth_date)) <= 60");
            }
            if ($age == 5) {
                $query->whereRaw("EXTRACT(year FROM age(current_date,participant_crm.birth_date)) > 60");
            }
        }
        if (! empty($this->request['job'])) {
            $query->where('participant_crm.job', 'like', '%' . $this->request['job'] . '%');
        }
        if (! empty($this->request['year'])) {
            $query->where('participant_crm.latest_trip_year', $this->request['year']);
        }
        if (!empty($this->request['totalTransaction'])) {
            $totalTransaction = $this->request['totalTransaction'];
            if ($totalTransaction == 1) {
                $query->where('total_transaction', '<=', 100000000);
            }
            if ($totalTransaction == 2) {
                $query->where('total_transaction', '>', 100000000);
                $query->where('total_transaction', '<=', 200000000);
            }
            if ($totalTransaction == 3) {
                $query->where('total_transaction', '>', 200000000);
                $query->where('total_transaction', '<=', 300000000);
            }
            if ($totalTransaction == 4) {
                $query->where('total_transaction', '>', 300000000);
                $query->where('total_transaction', '<=', 400000000);
            }
            if ($totalTransaction == 5) {
                $query->where('total_transaction', '>', 400000000);
                $query->where('total_transaction', '<=', 500000000);
            }
            if ($totalTransaction == 6) {
                $query->where('total_transaction', '>', 500000000);
            }
        }
        if (!empty($this->request['startDate']) && !empty($this->request['endDate'])) {
            $startDate = date('m-d', strtotime($this->request['startDate']));
            $endDate = date('m-d', strtotime($this->request['endDate']));
            $query->where(function ($query) use ($startDate, $endDate) {
                $query->whereRaw("TO_CHAR(birth_date,'MM-DD') BETWEEN '{$startDate}' AND '{$endDate}'");
            });
        }
        if (!empty($this->request['startDate']) && empty($this->request['endDate'])) {
            $startDate = date('m-d', strtotime($this->request['startDate']));
            $endDate = date('m-d', strtotime($this->request['endDate']));
            $query->where(function ($query) use ($startDate, $endDate) {
                $query->whereRaw("TO_CHAR(birth_date,'MM-DD') = '{$startDate}'");
            });
        }
        if (!empty($this->request['date'])) {
            $date = Carbon::now();
            $start = $date->startOfMonth()->format('Y-m-d H:i:s');
            $end = $date->endOfMonth()->format('Y-m-d H:i:s');
            if($this->request['date']) {
                $dateXplode = explode('to', $this->request['date']);
                $start = date('Y-m-d', strtotime($dateXplode[0]));
                $end = date('Y-m-d', strtotime($dateXplode[1]??$dateXplode[0]));
            }
            // $query->rightjoin('participants', 'participant.id', 'participant_crm.participant_id');
            $query->join('participant_umroh_trips', 'participant_umroh_trips.participant_id', 'participant.id');
            $query->join('umroh_trips', 'participant_umroh_trips.umroh_trip_id', 'umroh_trips.id');
            $query->whereBetween('umroh_trips.departure_at', [$start, $end]);
        }
        $query->orderBy('participant_crm.latest_trip_year', 'desc');

        return $query;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $phpSpreadSheet = $event->sheet->getDelegate();
                $phpSpreadSheet->getColumnDimension('AW')->setVisible(false);
                $phpSpreadSheet->getColumnDimension('AW')->setAutoSize(false)->setWidth(0);
                $phpSpreadSheet->getStyle('I:K')->getAlignment()->setWrapText(true);
                $phpSpreadSheet->getStyle('A:L')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $phpSpreadSheet->getStyle('C:F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $phpSpreadSheet->getStyle('L:N')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
