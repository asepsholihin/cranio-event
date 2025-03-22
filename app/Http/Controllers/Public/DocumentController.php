<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\LogLetter;
use App\Models\ParticipantUmrohTrip;
use App\Models\Participant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\File\PDF\ViewLetter;
use Illuminate\Support\Facades\Storage;
use Image;

class DocumentController extends Controller
{
    public function detailDocument($slug)
    {
        $checkLetter = LogLetter::select('log_letters.*','participant.name')->join('participants', 'participant.id', 'log_letters.participant_id')
        ->where('slug', $slug)
        ->first();
        if (!$checkLetter) {
            return response()->json([
                'success' => false,
                'message'  => 'Dokumen tidak ditemukan',
            ], 422);
        }

        $participantUmrohTrip = ParticipantUmrohTrip::find($checkLetter->participant_umroh_trip_id);
        $checkLetter->document_html = (new ViewLetter($participantUmrohTrip, $participantUmrohTrip->umroh_trip_id, $checkLetter->letter_type))->html();


        return response()->json($checkLetter->toArray());
    }

    public function viewDocument($slug) 
    {
        $checkLetter = LogLetter::where('slug', $slug)->first();

        $participantUmrohTrip = ParticipantUmrohTrip::find($checkLetter->participant_umroh_trip_id);
        return (new ViewLetter($participantUmrohTrip, $participantUmrohTrip->umroh_trip_id, $checkLetter->letter_type))->stream();
    }

    public function postDocumentSign(Request $request)
    {
        $profilePhotoPath = null;
        $phoneNumber = request()->no_hp;
        
        if(!is_numeric($phoneNumber)) {
            return response()->json([
                'success' => false,
                'message'  => 'Nomor HP anda tidak valid',
            ], 422);
        }
        
        if (Str::startsWith($phoneNumber, '0')) {
            $phoneNumber = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $phoneNumber);
        } else {
            return response()->json([
                'success' => false,
                'message'  => 'Nomor HP anda tidak valid',
            ], 422);
        }

        $participant = Participant::where('no_hp', $phoneNumber)->first();
        if(!$participant) {
            return response()->json([
                'success' => false,
                'message'  => 'Nomor HP tidak ditemukan',
            ], 422);   
        }

        $imageMake = Image::make(file_get_contents($request->image));
        $img =  (string) $imageMake
                ->resize(500, null, function ($constraint) {$constraint->aspectRatio();})
                ->encode('webp');
            
        $profilePhotoPath =  LogLetter::DIR_SIGN . Str::uuid() . '.webp';
        Storage::put($profilePhotoPath, $img);
        
        LogLetter::where('slug', $request->slug)->update(['sign_evidence' => $profilePhotoPath]);

        return response()->json($request->all());

    }
}
