<?php

namespace App\Http\Controllers;

use App\File\Image\BarcodeParticipant;
use App\Http\Requests\StoreParticipantRequest;
use App\Models\Participant;
use App\Imports\ParticipantImport;
use App\Imports\ParticipantAddressImport;
use App\Models\ParticipantFile;
use App\Models\ParticipantUmrohTrip;
use App\Models\PackageUmrohTrip;
use App\Models\UmrohTrip;
use App\Models\OrderUmrohTrip;
use App\Models\InvoiceUmrohTrip;
use App\Models\MasterAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Image;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Exports\ParticipantExport;
use App\Exports\ParticipantAddressExport;
use App\Models\ParticipantCRM;
use DB;
use App\Jobs\RefineParticipantByOffice;
use Carbon\Carbon;

class ParticipantSPAController extends Controller
{
    const SPA_PATH = '/participant';

    public function __construct()
    {
        $this->middleware('permission:participant-view')->only(['index', 'show', 'barcode']);
        $this->middleware('permission:participant-add-or-edit')->only(['store', 'import']);
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
        return response()->json(
            Participant::tableSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    public function participantRawData()
    {
        $orderBy = request()->query('sortBy', 'id');
        $sortBy = request()->query('sortDesc') == 'true' ? 'desc' : 'asc';
        $perPage = request()->query('perPage', 100);
        return response()->json(
            Participant::tableRawSearch()
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreParticipantRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreParticipantRequest $request)
    {
        $request->merge(['created_from' => Participant::CREATED_FROM_SPA]);
        $participant = Participant::updateOrCreate(['id' => $request->get('id') ?? 0], $request->except(['photo']));

        $participantCRM = ParticipantCRM::where('nik', $participant->kitas_number ?? $participant->nik)->first();
        if($participantCRM) {
            $participantCRM->update([
                'participant_id' => $participant->id,
                'name' =>  $participant->name,
                'participant_level' => $participant->participant_level,
                'no_hp' => $participant->no_hp,
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
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Participant  $participant
     * @return \Illuminate\Http\Response
     */
    public function show(Participant $participant)
    {
        return response()->json($participant->toArray());
    }

    public function destroy(Participant $participant)
    {
        $participant->delete();
    }

    public function queryParticipant(Request $request)
    {
        $request->validate(['q' => 'required_without:bookingOrder']);

        if ($request->has('bookingOrder')) {
            return $this->suggestParticipantBookingOrder($request);
        }

        $search = $request->get('q');
        if (Str::startsWith($search, '0')) {
            $search = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $search);
        }

        $search = '%' . $search . '%';
        $query = Participant::select(['id', 'name', 'email','whatsapp', 'profile_photo_path'])
        ->where('email',  $request->get('q'))
        ->orWhere('name', 'like', $search)
        ->orWhere('whatsapp', 'like', $search)
        ->orWhere('nik', 'like', $search);
        $result = $query->limit(30)->get();

        return response()->json($result);
    }

    private function suggestParticipantBookingOrder(Request $request)
    {
        $packageName = PackageUmrohTrip::select('name')->first($request->packageId)->name;
        $result = Participant::select(['id', 'name', 'whatsapp', 'profile_photo_path', DB::raw("0 as crm")])
            ->where('suggest_booking_order',  $request->bookingOrder)
            ->where('suggest_package',  $packageName)
            ->where('suggest_room',  $request->roomType)
            ->orderBy('updated_at')
            ->limit(10)
            ->get();
        return response()->json($result);
    }

    public function barcode(Participant $participant)
    {
        return (new BarcodeParticipant($participant))->download();
    }

    public function files($id)
    {
        $participantFiles = Participant::select(['id', 'name', 'profile_photo_path'])->with('files')->findOrFail($id);
        return response()->json($participantFiles);
    }

    public function uploadVerificationFile(Request $request)
    {
        $request->validate([
            'participant_id' => 'required',
            'title' => 'required',
            'file_upload.*' => 'required|file|mimes:jpg,png,pdf',
        ]);

        if ($request->hasfile('file_upload')) {
            // ParticipantFile::where('participant_id', $request->participant_id)->where('title', $request->title)->delete();
            $files = [];
            foreach ($request->file('file_upload') as $file) {
                $filePath = $file->store(ParticipantFile::DIR_FILE);
                $request->merge(['file_path' => $filePath, 'file_type' => $file->getClientMimeType()]);
                ParticipantFile::create($request->except(['file_upload']));
            }
        }
    }

    public function deleteFile(Request $request)
    {
        $request->validate([
            'participant_id' => 'required',
            'title' => 'required',
        ]);

        ParticipantFile::where('participant_id', $request->participant_id)->where('title', $request->title)->delete();
        ParticipantUmrohTrip::where('participant_id', $request->participant_id)->where('umroh_trip_id', $request->umrohTripId)
            ->update($request->except(['title', 'umrohTripId']));
    }

    public function deleteSpecificFile(Request $request)
    {
        $request->validate([
            'participant_id' => 'required',
            'file_path' => 'required',
        ]);

        ParticipantFile::where('participant_id', $request->participant_id)->where('file_path', 'like', '%' . $request->file_path . '%')->delete();
    }

    public function uploadPasPhoto(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'file_upload' => 'required|file|mimes:jpg,png,webp',
        ]);

        $profilePhotoPath = $request->file('file_upload')->store(Participant::DIR_PHOTO);
        $img = Image::make($request->file('file_upload'))
            ->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->encode();
        Storage::put(Participant::DIR_THUMBNAIL . $profilePhotoPath, $img);
        Participant::select(['id', 'profile_photo_path'])->where('id', $request->id)->update(['profile_photo_path' => $profilePhotoPath]);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|mimes:xlsx|file|max:1024'
        ]);

        $file = $request->file('file');

        $import = new ParticipantImport();
        $import->import($file);

        if (count($import->failures()) > 0) {
            return response()->json(['message' => $this->importErrorParse($import->failures())], 422);
        }

        $result = [
            'error' => false,
            'message' => 'Import Participant Berhasil'
        ];
        return response()->json($result);
    }

    public function importParticipantAddress(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|mimes:xlsx|file|max:1024'
        ]);

        $file = $request->file('file');

        $import = new ParticipantAddressImport();
        $import->import($file);

        if (count($import->failures()) > 0) {
            return response()->json(['message' => $this->importErrorParse($import->failures())], 422);
        }

        $result = [
            'error' => false,
            'message' => 'Import Alamat Domisili Participant Berhasil'
        ];
        return response()->json($result);
    }

    private function importErrorParse($errors)
    {
        $messages = [];
        foreach ($errors as $error) {
            $messageErr = implode(', ', $error->errors());
            $no = $error->values()[0];
            $val = $error->values()[$error->attribute()];
            $messages[] = "#{$no}: ({$val}) {$messageErr}";
        }

        return implode('<br/><br/>', $messages);
    }

    public function downloadExampleImport()
    {
        $path = storage_path('example/format-import-data-participant.xlsx');
        $headers = ['Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        return response()->download($path, 'format-import-data-participant.xlsx', $headers);
    }

    public function createAccessLogin(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'email' => ['required', 'email', Rule::unique('participants')->ignore($request->id)],
            'password' => 'required',
        ]);

        $request->merge(['password' => Hash::make($request->password)]);
        Participant::where('id', $request->id)->update($request->all());
    }
    public function createNameInCertificate(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name_in_certificate' => 'required',
        ]);

        Participant::where('id', $request->id)->update($request->all());
    }

    public function exportParticipant(Request $request)
    {
        $storageKey = "Export-Participant-" . hrtime(true) . ".xlsx";
        return Excel::download(new ParticipantExport(), $storageKey);
    }

    public function exportParticipantAddress(Request $request)
    {
        $umrohTrip = UmrohTrip::find($request->umrohTripId);
        $fileName = $umrohTrip->title ?? '';

        $storageKey = "JAMAAH-ALAMAT-EXPORT-" . $fileName . "-" . date('d-m-Y') . ".xlsx";
        return Excel::download(new ParticipantAddressExport($request->umrohTripId), $storageKey);
    }

    public function participantForBooking(Request $request)
    {
        $request->validate(['q' => 'required_without:bookingOrder']);

        if ($request->has('bookingOrder')) {
            return $this->suggestParticipantBookingOrder($request);
        }

        $search = $request->get('q');
        if (Str::startsWith($search, '0')) {
            $search = Str::replaceFirst('0', Participant::PREFIX_PHONE_NUMBER, $search);
        }

        $search = '%' . $search . '%';
        $participant = Participant::select(['id', 'name', 'nik', 'no_hp', 'birth_date', 'profile_photo_path', 'home_address', DB::raw("0 as crm")])
            ->where(function($q) use($search) {
                $q->where('name', 'like', $search)
                ->orWhere('nik', 'like', $search)
                ->orWhere('no_hp', 'like', $search);
            })
            ->orderBy('created_at', 'asc')
            ->limit(50)
            ->get()->toArray();

        $result = $participant;
        return response()->json($result);
    }

    public function refineParticipantByOffice()
    {
        RefineParticipantByOffice::dispatch();

        return response()->json(['status'=>'processing in background']);
    }

    public function refineMasterAddress(Request $request)
    {
        // $master_address = DB::table('master_address')->select('id','province','city','district','subdistrict')->get();

        // $data = array();
        // foreach ($master_address as $value) {
        //     $province = null;
        //     $city = null;
        //     $district = null;
        //     $subdistrict = null;
        //     if($value->province != '' && $value->province != '-') {
        //         $province = strtoupper(rtrim($value->province, " "));
        //     }
        //     if($value->city != '' && $value->city != '-') {
        //         $city = strtoupper(rtrim($value->city, " "));
        //     }
        //     if($value->district != '' && $value->district != '-') {
        //         $district = strtoupper(rtrim($value->district, " "));
        //     }
        //     if($value->subdistrict != '' && $value->subdistrict != '-') {
        //         $subdistrict = strtoupper(rtrim($value->subdistrict, " "));
        //     }

        //     $split = explode(' ', $city);
        //     if(count($split) > 0) {
        //         if($split[0] == 'KABUPATEN') {
        //             $city = 'KAB. ' . $split[1];
        //         }
        //     }
        //     if(count($split) > 2) {
        //         if($split[0] == 'KABUPATEN') {
        //             $city = 'KAB. ' . $split[1] .' '. $split[2];
        //         }
        //     }
        //     if(count($split) > 3) {
        //         if($split[0] == 'KABUPATEN') {
        //             $city = 'KAB. ' . $split[1] .' '. $split[2] .' '. $split[3];
        //         }
        //     }

        //     DB::table('master_address')->where('id', $value->id)->update([
        //         'province' => $province,
        //         'city' => $city,
        //         'district' => $district,
        //         'subdistrict' => $subdistrict,
        //     ]);
        // }
        // return response()->json($data);

        // $master_address = DB::table('master_address')->select('id','province','city','district','subdistrict')->where('district', 'like', '%(%')->get();

        // $data = array();
        // foreach ($master_address as $value) {
        //     $district = $value->district;
        //     $split = explode(' (', $value->district);
        //     if(count($split) > 0) {
        //         $district = $split[0];
        //     }

        //     $update = [
        //         'district' => $district,
        //     ];
        //     $data = $update;
        //     DB::table('master_address')->where('id', $value->id)->update($update);
        // }

        // $master_address = DB::table('master_address')->select('id','province','city','district','subdistrict')->where('subdistrict', 'like', '%(%')->get();

        // $data = array();
        // foreach ($master_address as $value) {
        //     $subdistrict = $value->subdistrict;
        //     $split = explode(' (', $value->subdistrict);
        //     if(count($split) > 0) {
        //         $subdistrict = $split[0];
        //     }

        //     $update = [
        //         'subdistrict' => $subdistrict,
        //     ];
        //     $data = $update;
        //     DB::table('master_address')->where('id', $value->id)->update($update);
        // }

        // return response()->json($data);
    }

    public function refineParticipantAddress(Request $request)
    {
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 5000;
        $participant = DB::table('participants')->select('id','ktp_province','home_province','ktp_city','ktp_kecamatan','ktp_kelurahan','home_city','home_kecamatan','home_kelurahan')->skip(($page - 1) * $limit)->take($limit)->get();

        $data = array();
        foreach ($participant as $value) {
            $ktp_province = null;
            $ktp_city = null;
            $ktp_kecamatan = null;
            $ktp_kelurahan = null;
            $home_province = null;
            $home_city = null;
            $home_kecamatan = null;
            $home_kelurahan = null;
            if($value->ktp_province != '' && $value->ktp_province != '-') {
                $ktp_province = strtoupper(rtrim($value->ktp_province, " "));
            }
            if($value->home_province != '' && $value->home_province != '-') {
                $home_province = strtoupper(rtrim($value->home_province, " "));
            }
            if($value->ktp_city != '' && $value->ktp_city != '-') {
                $ktp_city = strtoupper(rtrim($value->ktp_city, " "));
            }
            if($value->home_city != '' && $value->home_city != '-') {
                $home_city = strtoupper(rtrim($value->home_city, " "));
            }
            if($value->ktp_kecamatan != '' && $value->ktp_kecamatan != '-') {
                $ktp_kecamatan = strtoupper(rtrim($value->ktp_kecamatan, " "));
            }
            if($value->home_kecamatan != '' && $value->home_kecamatan != '-') {
                $home_kecamatan = strtoupper(rtrim($value->home_kecamatan, " "));
            }
            if($value->ktp_kelurahan != '' && $value->ktp_kelurahan != '-') {
                $ktp_kelurahan = strtoupper(rtrim($value->ktp_kelurahan, " "));
            }
            if($value->home_kelurahan != '' && $value->home_kelurahan != '-') {
                $home_kelurahan = strtoupper(rtrim($value->home_kelurahan, " "));
            }

            $split = explode(' ', $ktp_city);
            if(count($split) > 0) {
                if($split[0] == 'KABUPATEN') {
                    $ktp_city = 'KAB. ' . $split[1];
                }
            }
            if(count($split) > 2) {
                if($split[0] == 'KABUPATEN') {
                    $ktp_city = 'KAB. ' . $split[1] .' '. $split[2];
                }
            }
            if(count($split) > 3) {
                if($split[0] == 'KABUPATEN') {
                    $ktp_city = 'KAB. ' . $split[1] .' '. $split[2] .' '. $split[3];
                }
            }
            $home_city = $ktp_city;

            $split = explode(' (', $ktp_kecamatan);
            if(count($split) > 0) {
                $ktp_kecamatan = $split[0];
                $home_kecamatan = $ktp_kecamatan;
            }

            DB::table('participants')->where('id', $value->id)->update([
                'ktp_province' => $ktp_province,
                'home_province' => $home_province,
                'ktp_city' => $ktp_city,
                'home_city' => $home_city,
                'ktp_kecamatan' => $ktp_kecamatan,
                'home_kecamatan' => $home_kecamatan,
                'ktp_kelurahan' => $ktp_kelurahan,
                'home_kelurahan' => $home_kelurahan
            ]);
        }
        return response()->json($data);
    }

    public function refineParticipantCity(Request $request)
    {
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 5000;
        $participant = Participant::select('id','ktp_province','ktp_city')->whereNotNull('ktp_city')->skip(($page - 1) * $limit)->take($limit)->get();

        $data = array();
        foreach ($participant as $value) {
            $split = explode(' ', $value->ktp_city);
            $splitCity = $value->ktp_city;
            if(count($split) > 0) {
                if($split[0] == 'KAB.') {
                    $splitCity = $split[1];
                } else if($split[0] == 'KOTA' && $split[1] == 'JAKARTA') {
                    $splitCity = $split[1] .' '. $split[2];
                } else if($split[0] == 'KOTA' && $split[1] == 'TANGGERANG') {
                    $splitCity = $split[1] .' '. $split[2];
                }  else if(count($split) > 1) {
                    $splitCity = $split[1];
                }
            }
            if(count($split) > 2) {
                $splitCity = $split[1] .' '. $split[2];
                $splitCity = rtrim($splitCity, " ");
            }
            if(count($split) > 3) {
                $splitCity = $split[1] .' '. $split[2] .' '. $split[3];
                $splitCity = rtrim($splitCity, " ");
            }
            if($split[0] == 'KOTA') {
                $splitCity = $value->ktp_city;
                $splitCity = rtrim($splitCity, " ");
            }
            if($split[0] == 'Kota') {
                $splitCity = $value->ktp_city;
                $splitCity = rtrim($splitCity, " ");
            }
            $master = MasterAddress::select('city')->where('province', $value->ktp_province)->where('city', 'like', '%'.$splitCity.'%')->first();

            $row['ktp_province'] = $value->ktp_province;
            $row['ktp_city'] = $value->ktp_city;
            $row['splitCity'] = $splitCity;
            $row['city'] = $master->city ?? '';

            if($master) {
                $value->update(['ktp_city' => $master->city]);
                $data[] = $row;
            }
        }
        return response()->json($data);
    }

    public function refineParticipantKecamatan(Request $request)
    {
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 5000;
        $participant = Participant::select('id','ktp_province','ktp_city','ktp_kecamatan')->whereNotNull('ktp_kecamatan')->skip(($page - 1) * $limit)->take($limit)->get();

        $data = array();
        foreach ($participant as $value) {
            $splitCity = $value->ktp_kecamatan;
            $master = MasterAddress::select('district')->where('province', $value->ktp_province)->where('city', $value->ktp_city)->where('district', 'like', '%'.$splitCity.'%')->first();

            $row['ktp_province'] = $value->ktp_province;
            $row['ktp_city'] = $value->ktp_city;
            $row['ktp_kecamatan'] = $value->ktp_kecamatan;
            $row['splitCity'] = $splitCity;
            $row['kecamatan'] = $master->district ?? '';
            if($master) {
                $value->update(['ktp_kecamatan' => $master->district]);
                $data[] = $row;
            }
        }

        return response()->json($data);
    }

    public function refineParticipantKelurahan(Request $request)
    {
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 5000;
        $participant = Participant::select('id','ktp_province','ktp_city','ktp_kecamatan','ktp_kelurahan')->whereNotNull('ktp_kelurahan')->skip(($page - 1) * $limit)->take($limit)->get();

        $data = array();
        foreach ($participant as $value) {
            $splitCity = $value->ktp_kelurahan;
            $master = MasterAddress::select('subdistrict')->where('province', $value->ktp_province)->where('city', $value->ktp_city)->where('district', $value->ktp_kecamatan)->where('subdistrict', 'like', '%'.$splitCity.'%')->first();

            $row['ktp_province'] = $value->ktp_province;
            $row['ktp_city'] = $value->ktp_city;
            $row['ktp_kecamatan'] = $value->ktp_kecamatan;
            $row['ktp_kelurahan'] = $value->ktp_kelurahan;
            $row['splitCity'] = $splitCity;
            $row['kelurahan'] = $master->subdistrict ?? '';
            if($master) {
                $value->update(['ktp_kelurahan' => $master->subdistrict]);
                $data[] = $row;
            }
        }

        return response()->json($data);
    }

    public function refineParticipantDomisiliCity(Request $request)
    {
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 5000;
        $participant = Participant::select('id','home_province','home_city')->whereNotNull('home_city')->skip(($page - 1) * $limit)->take($limit)->get();

        $data = array();
        foreach ($participant as $value) {
            $split = explode(' ', $value->home_city);
            $splitCity = $value->home_city;
            if(count($split) > 0) {
                if($split[0] == 'KAB.') {
                    $splitCity = $split[1];
                } else if($split[0] == 'KOTA' && $split[1] == 'JAKARTA') {
                    $splitCity = $split[1] .' '. $split[2];
                } else if($split[0] == 'KOTA' && $split[1] == 'TANGGERANG') {
                    $splitCity = $split[1] .' '. $split[2];
                }  else if(count($split) > 1) {
                    $splitCity = $split[1];
                }
            }
            if(count($split) > 2) {
                $splitCity = $split[1] .' '. $split[2];
                $splitCity = rtrim($splitCity, " ");
            }
            if(count($split) > 3) {
                $splitCity = $split[1] .' '. $split[2] .' '. $split[3];
                $splitCity = rtrim($splitCity, " ");
            }
            if($split[0] == 'KOTA') {
                $splitCity = $value->home_city;
                $splitCity = rtrim($splitCity, " ");
            }
            if($split[0] == 'Kota') {
                $splitCity = $value->home_city;
                $splitCity = rtrim($splitCity, " ");
            }
            $master = MasterAddress::select('city')->where('province', $value->home_province)->where('city', 'like', '%'.$splitCity.'%')->first();

            $row['home_province'] = $value->home_province;
            $row['home_city'] = $value->home_city;
            $row['splitCity'] = $splitCity;
            $row['city'] = $master->city ?? '';

            if($master) {
                $value->update(['home_city' => $master->city]);
                $data[] = $row;
            }
        }
        return response()->json($data);
    }

    public function refineParticipantDomisiliKecamatan(Request $request)
    {
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 5000;
        $participant = Participant::select('id','home_province','home_city','home_kecamatan')->whereNotNull('home_kecamatan')->skip(($page - 1) * $limit)->take($limit)->get();

        $data = array();
        foreach ($participant as $value) {
            $splitCity = $value->home_kecamatan;
            $master = MasterAddress::select('district')->where('province', $value->home_province)->where('city', $value->home_city)->where('district', 'like', '%'.$splitCity.'%')->first();

            $row['home_province'] = $value->home_province;
            $row['home_city'] = $value->home_city;
            $row['home_kecamatan'] = $value->home_kecamatan;
            $row['splitCity'] = $splitCity;
            $row['kecamatan'] = $master->district ?? '';
            if($master) {
                $value->update(['home_kecamatan' => $master->district]);
                $data[] = $row;
            }
        }

        return response()->json($data);
    }

    public function refineParticipantDomisiliKelurahan(Request $request)
    {
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 5000;
        $participant = Participant::select('id','home_province','home_city','home_kecamatan','home_kelurahan')->whereNotNull('home_kelurahan')->skip(($page - 1) * $limit)->take($limit)->get();

        $data = array();
        foreach ($participant as $value) {
            $splitCity = $value->home_kelurahan;
            $master = MasterAddress::select('subdistrict')->where('province', $value->home_province)->where('city', $value->home_city)->where('district', $value->home_kecamatan)->where('subdistrict', 'like', '%'.$splitCity.'%')->first();

            $row['home_province'] = $value->home_province;
            $row['home_city'] = $value->home_city;
            $row['home_kecamatan'] = $value->home_kecamatan;
            $row['home_kelurahan'] = $value->home_kelurahan;
            $row['splitCity'] = $splitCity;
            $row['kelurahan'] = $master->subdistrict ?? '';
            if($master) {
                $value->update(['home_kelurahan' => $master->subdistrict]);
                $data[] = $row;
            }
        }

        return response()->json($data);
    }

    public function updateDomisili(Request $request)
    {
        $participant = Participant::find($request->id);

        $participant->update([
            'home_province' => $request->home_province,
            'home_city' => $request->home_city,
            'home_kecamatan' => $request->home_kecamatan,
            'home_kelurahan' => $request->home_kelurahan,
            'home_postalcode' => $request->home_postalcode,
            'home_address' => $request->home_address
        ]);
    }

    public function updateData(Request $request)
    {
        $participant = Participant::find($request->id);
        $participant->update($request->all());
    }

    public function refineParticipantDuplicate(Request $request)
    {
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 10000;
        $participants = DB::table('participants')->select(['nik', DB::raw('count(id) as count')])
        ->whereNull('deleted_at')
        ->groupByRaw('nik')
        ->havingRaw('COUNT(id) > 1')
        ->skip(($page - 1) * $limit)->take($limit)
        ->get();

        $data = array();
        foreach ($participants as $value) {
            $participantDuplicates = DB::table('participants')->select(['id','name','nik'])->where('nik', $value->nik)->get();
            $key = 1;
            foreach ($participantDuplicates as $row) {
                $participantUmrohTrip = DB::table('participant_umroh_trips')->select(['umroh_trips.title'])
                ->join('umroh_trips', 'umroh_trips.id', 'participant_umroh_trips.umroh_trip_id')
                ->where('participant_id', $row->id)->first()->title ?? null;

                if($participantUmrohTrip == null) {
                    $row->delete = true;
                    DB::table('participants')->where('id', $row->id)->delete();
                }
                $row->participant_umroh_trip = $participantUmrohTrip;
                $row->key = $key++;
            }
            $value->duplicates = $participantDuplicates;
            $data[] = $value;
        }

        return response()->json($data);
    }

    public function medicalRecordParticipant(Request $request)
    {
        $search = '%' . $request->get('q') . '%';
        $participant = Participant::select(['participant.medical_record'])
            ->join('participant_umroh_trips', 'participant.id', 'participant_umroh_trips.participant_id')
            ->where('participant_umroh_trips.umroh_trip_id', $request->umrohTripId)
            ->where(function($q) use($search) {
                $q->where('medical_record', 'like', $search);
            })
            ->groupBy('medical_record')
            ->pluck('medical_record')->toArray();
        return response()->json($participant);
    }

    public function action(Request $request){
        $request->validate(['id' => 'required']);
        $participant = Participant::find($request->get('id'));

        if ($request->hasFile('file_evidence')) {
            $profilePhotoPath = $request->file('file_evidence')->store(Participant::DIR_EVIDENCE);
            $request->merge(['room_key_evidence' => $profilePhotoPath]);
        }

        if($request->set_room) {
            $request->merge([
                'received_at' => Carbon::now(),
                'given_by' => auth()->user()->id
            ]);
        }
        $participant->update($request->except('file_evidence'));
    }

    public function jobSearch(Request $request)
    {
        $jobs = DB::table('master_jobs')->select('id','name')->where('status', TRUE)->orderBy('id', 'ASC')->get();
        return response()->json($jobs);
    }

    public function refineParticipantJICode(Request $request)
    {
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 100000;
        $participant = Participant::withTrashed()->select('id','name','ji_code','created_at')
        ->whereYear('created_at', request()->year)
        ->orderBy('id', 'ASC')->skip(($page - 1) * $limit)->take($limit)->get();

        $data = array();
        foreach ($participant as $key => $value) {
            $yearFull = date('Y', strtotime($value->created_at));
            $yearShort = date('y', strtotime($value->created_at));
            $recordNumber = ($key+1) * $page;

            $jiCode = Participant::PREFIX_JI_CODE . $yearShort . str_pad($recordNumber, 5, 0, STR_PAD_LEFT);

            // DB::table('participants')->where('id', $value->id)->update([
            //     'ji_code' => $jiCode
            // ]);
            $value->ji_code = $jiCode;
            $value->save();
            $data[] = $value;
        }
        return response()->json($data);
    }

    public function chartGender(Request $request)
    {
        $query = Participant::select([
            DB::raw("SUM(CASE WHEN gender = 1 THEN 1 ELSE 0 END) AS men"),
            DB::raw("SUM(CASE WHEN gender = 2 THEN 1 ELSE 0 END) AS women"),
        ]);
        if (!empty(request()->query('gender'))) {
            $query->where('participants.gender', request()->query('gender'));
        }
        if (!empty(request()->query('poloSize'))) {
            $query->where('participants.polo_size', request()->query('poloSize'));
        }
        if (!empty(request()->query('date'))) {
            $dateXplode = explode('to', request()->query('date'));
            $start = date('Y-m-d', strtotime($dateXplode[0]));
            $end = date('Y-m-d', strtotime($dateXplode[1]??$dateXplode[0]));
            $query->whereBetween('participants.created_at', [$start, $end]);
        }
        $participant = $query->first();

        $data = array();

        $data['categories'] = array('Pria', 'Wanita');
        $data['data'] = array(intval($participant->men), intval($participant->women));

        return response()->json($data);
    }

    public function chartPoloSize(Request $request)
    {
        $query = Participant::select([
            DB::raw("SUM(CASE WHEN polo_size = 'S' THEN 1 ELSE 0 END) AS S"),
            DB::raw("SUM(CASE WHEN polo_size = 'M' THEN 1 ELSE 0 END) AS M"),
            DB::raw("SUM(CASE WHEN polo_size = 'L' THEN 1 ELSE 0 END) AS L"),
            DB::raw("SUM(CASE WHEN polo_size = 'XL' THEN 1 ELSE 0 END) AS XL"),
            DB::raw("SUM(CASE WHEN polo_size = 'XXL' THEN 1 ELSE 0 END) AS XXL"),
            DB::raw("SUM(CASE WHEN polo_size = 'XXXL' THEN 1 ELSE 0 END) AS XXXL"),
        ]);
        if (!empty(request()->query('gender'))) {
            $query->where('participants.gender', request()->query('gender'));
        }
        if (!empty(request()->query('poloSize'))) {
            $query->where('participants.polo_size', request()->query('poloSize'));
        }
        if (!empty(request()->query('date'))) {
            $dateXplode = explode('to', request()->query('date'));
            $start = date('Y-m-d', strtotime($dateXplode[0]));
            $end = date('Y-m-d', strtotime($dateXplode[1]??$dateXplode[0]));
            $query->whereBetween('participants.created_at', [$start, $end]);
        }
        $participant = $query->first();

        $data = array();

        $data['categories'] = array('S', 'M', 'L', 'XL', 'XXL', 'XXXL');
        $data['data'] = array(intval($participant->S), intval($participant->M), intval($participant->L), intval($participant->XL), intval($participant->XXL), intval($participant->XXXL));

        return response()->json($data);
    }
}
