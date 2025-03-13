<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\ParticipantCRM;
use App\Models\Participant;
use Illuminate\Http\Request;
use App\File\Image\MiladCard;
use App\File\Image\MiladCardWithoutPhoto;
use App\File\PDF\Certificate;
use App\File\PDF\CertificateParticipant;
use App\Jobs\SendWhatsappMiladCard;
use App\Jobs\SendWhatsappMiladCardWithoutPhoto;
use Illuminate\Support\Facades\DB;
use App\Models\ParticipantFile;

class MiladCRMSPAController extends Controller
{
    const SPA_PATH = '/milad-crm';

    public function __construct()
    {
        $this->middleware('permission:milad-view')->only(['index', 'show', 'downloadMiladCard']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orderBy = request()->query('sortBy', 'id');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage', 10);

        $participantPaginatedData = ParticipantCRM::tableSearch()
        ->paginate($perPage)
        ->withQueryString()
        ->withPath(self::SPA_PATH);

        $participant = $participantPaginatedData->items();

        // foreach($participant as $row) {
        //     $row->last_trip = Participant::getLastTrip($row->id);
        // }

        return response()->json($participantPaginatedData);
    }

    public function changeOurPhoto(Request $request){
        $data = $this->getMiladCardWithPhoto($request->id, $request->image);
        return $data;
    }

    public function getMiladCardWithPhoto($id, $image = "")
    {
        $participant = Participant::find($id);
        $photo = (new MiladCard($participant, true, $image))->stream();
        return $photo;
    }

    public function getMiladCardWithoutPhoto($id)
    {
        $participant = Participant::find($id);
        $photo = (new MiladCard($participant, false))->stream();
        return $photo;
    }

    public function sendPostDownloadMiladCard(Request $request){
        $previewMode = $request->mode;
        $withImage = '';
        if($request->image){
            $withImage = $request->image;
        }
        $withPhoto = true;
        if($previewMode == "without_photo") {
            $withPhoto = false;
        }
        $participant = Participant::find($request->id);
        return (new MiladCard($participant, $withPhoto, $withImage))->download();
    }

    public function downloadMiladCard($id)
    {
        $previewMode = request()->mode;
        $withImage = request()->withImage;
        $withPhoto = true;
        if($previewMode == "without_photo") {
            $withPhoto = false;
        }
        $participant = Participant::find($id);
        return (new MiladCard($participant, $withPhoto, $withImage))->download();
    }

    public function sendMiladCard($id)
    {
        $previewMode = request()->mode;
        if ($previewMode == "without_photo") {
            SendWhatsappMiladCard::dispatch($id);
        } else {
            SendWhatsappMiladCardWithoutPhoto::dispatch($id);
        }
        return response()->json(['success' => true]);
    }

    public function sendPostMiladCard(Request $request){
        $previewMode = $request->mode;
        $id = $request->id;
        if ($previewMode == "without_photo") {
            $image = "";
            if(!empty($request->image)){
                $image = $request->image;
            }
            SendWhatsappMiladCard::dispatch($id, $image);
        } else {
            SendWhatsappMiladCardWithoutPhoto::dispatch($id);
        }
        return response()->json(['success' => true]);
    }

    public function activationRemiderMilad(Request $request)
    {
        $participant = ParticipantCRM::findOrFail($request->participant_id);
        $participant->activationReminderMilad();

        return response()->json(['success' => true]);
    }

    public function getImages($id){
        $photo = ParticipantFile::where('participant_id', $id)->where('title', "Foto Milad")->first()->file_path_url ?? '';
        $data = [];
        $data['background'] = $this->changeBaseCode(storage_path('private_assets/images/milad_card_umroh.png'));
        $data['images'] = $this->changeBaseCode($photo);
        $data['partOfLeft'] = $this->changeBaseCode(storage_path('private_assets/images/part-of-left.png'));
        $data['partOfBottom'] = $this->changeBaseCode(storage_path('private_assets/images/part-of-bottom.png'));
        $data['partOfRight'] = $this->changeBaseCode(storage_path('private_assets/images/part-of-right.png'));
        $data['status'] = true;
        if(empty($data['images'])){
            $data['status'] = false;
        }
        return response()->json($data);
    }

    public function changeBaseCode($image){
        $imagPath = $image;
        $imageUrl = $imagPath;
        try {
            // Use file_get_contents to get the image content
            $imageData = file_get_contents($imageUrl);
            // Encode the image data to base64
            $base64Image = base64_encode($imageData);
            // Get the image's MIME type (optional, for embedding in HTML or CSS)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $imageMime = finfo_buffer($finfo, $imageData);
            finfo_close($finfo);
            // Example of using the base64 image with data URL scheme
            $dataUrl = 'data:' . $imageMime . ';base64,' . $base64Image;
            // Output the result
            // return $dataUrl;
            $basecode = $dataUrl;
        } catch (\Throwable $th) {
            $basecode = $imageUrl;
        }
        return $basecode;
    }
}
