<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Inisiatif\WhatsappQontakPhp\Client;
use Inisiatif\WhatsappQontakPhp\Credential;
use Inisiatif\WhatsappQontakPhp\Message\Body;
use Inisiatif\WhatsappQontakPhp\Message\Button;
use Inisiatif\WhatsappQontakPhp\Message\Header;
use Inisiatif\WhatsappQontakPhp\Message\Message;
use Inisiatif\WhatsappQontakPhp\Message\Receiver;
use Inisiatif\WhatsappQontakPhp\Message\Language;

use App\Models\Participant;
use App\Models\ParticipantCRM;
use App\Models\ParticipantUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\OrderUmrohTrip;
use App\Models\InvoiceUmrohTrip;
use App\Models\PackageUmrohTrip;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\GenerateDocument\ErrorJobMail;
use Carbon\Carbon;
use DB;


class RefineParticipantCRM implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        App::setLocale('id');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $participantUmrohTrips = ParticipantUmrohTrip::select(['participant_umroh_trips.*'])
        ->join('participant', 'participant.id', 'participant_umroh_trips.participant_id')
        ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
        ->whereNull('umroh_trips.deleted_at')
        ->whereNull('participant.deleted_at')
        ->whereYear('participant_umroh_trips.created_at', '>', 2022)
        ->whereNot('participant_umroh_trips.umroh_trip_id', 1)
        ->whereIn('participant_umroh_trips.role_type', array(1,4))
        ->orderBy('participant_umroh_trips.created_at', 'ASC')
        ->get();

        $data = array();
        foreach ($participantUmrohTrips as $participantUmrohTrip) {
            $participant = Participant::withTrashed()->find($participantUmrohTrip->participant_id);
            $umrohTrip = UmrohTrip::withTrashed()->find($participantUmrohTrip->umroh_trip_id);
            
            $frontTitle = ($participant->front_title != null && $participant->front_title != '-') ? $participant->front_title . " " : "";
            $backTitle = ($participant->back_title != null && $participant->back_title != '-') ?  " " . $participant->back_title : "";
            $name = $participant->name;
            if($participant->name_in_certificate) {
                $name = $participant->name_in_certificate;
            }
            $originPhone = substr($participant->no_hp, 2);
            $parent_account = OrderUmrohTrip::where('umroh_trip_id', $participantUmrohTrip->umroh_trip_id)->where('no_hp', 'like', '%' . $originPhone . '%')->where('name', 'like', '%' . $participant->name . '%')->count();
            $price_per_pax = $participantUmrohTrip->price_per_pax ?? 0;
            $convertion = 1;
            if($umrohTrip->currency == 'USD') {
                $convertion = 15000;
            }
            $total_discount = ($participantUmrohTrip->discount > 0) ? $participantUmrohTrip->discount : 0;
            $total_transaction = (($price_per_pax - $total_discount) * $convertion);
            $latest_trip_id = $umrohTrip->id ?? null;
            $latest_trip_name = $umrohTrip->title ?? '';
            $latest_trip_package = PackageUmrohTrip::find($participantUmrohTrip->package_umroh_trip_id)->name ?? null;
            $latest_trip_year = date('Y', strtotime($umrohTrip->departure_at));

            $participantcrm = ParticipantCRM::where('nik', $participant->kitas_number ?? $participant->nik)->first();

            $params = [
                'participant_id' => $participantUmrohTrip->participant_id,
                'name' => $name,
                'participant_level' => $participant->participant_level,
                'no_hp' => $participant->no_hp,
                'nik' => $participant->nik,
                'email' => $participant->email,
                'birth_date' => $participant->birth_date,
                'address' => $participant->home_address,
                'city' => $participant->home_city,
                'province' => $participant->home_province,
                'gender' => $participant->gender,
                'job' => $participant->job,
                'instagram' => $participant->instagram,
                'linkedin' => $participant->linkedin_url,
                'twitter' =>  null,
                'interest' =>  null,
                'total_transaction' => ($participantcrm->total_transaction ?? 0) + $total_transaction,
                'total_trip' => ($participantcrm->total_trip ?? 0) + 1,
                'latest_trip_id' => $umrohTrip->id,
                'latest_trip_name' => $umrohTrip->title,
                'latest_trip_package' => $latest_trip_package,
                'latest_trip_year' => $latest_trip_year,
                'parent_account' => $parent_account,
                'last_booking_order_no' => $participantUmrohTrip->booking_order_no,
                'history_participant_umroh_trips' => $participantcrm->history_participant_umroh_trips ? $participantcrm->history_participant_umroh_trips. ',' : null . $participantUmrohTrip->id
            ];

            ParticipantCRM::updateOrCreate(
                ['nik' => $participant->nik],
                $params
            );

            $data[] = $params;
        }
    }

    public function failed($e)
    {
        
    }
}
