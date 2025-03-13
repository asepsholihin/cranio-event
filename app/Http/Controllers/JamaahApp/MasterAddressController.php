<?php

namespace App\Http\Controllers\ParticipantApp;

use App\Http\Controllers\Controller;
use App\Models\MasterAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class MasterAddressController extends Controller
{
    public function __construct()
    {
        App::setLocale('id');
    }

    public function countries()
    {
        $countries = [
            ['country'=>'WNI'],
            ['country'=>'WNA']
        ];
        return response()->json($countries);
    }

    public function provinces()
    {
        return response()->json(MasterAddress::select('province')->where('province', 'like', '%' . request('keyword') . '%')->groupBy('province')->orderBy('province', 'ASC')->get());
    }

    public function cities()
    {
        return response()->json(MasterAddress::select('city')->where('city', 'like', '%' . request('keyword') . '%')->where('province', request('province'))->groupBy('city')->orderBy('city', 'ASC')->get());
    }

    public function districts()
    {
        return response()->json(MasterAddress::select('district')->where('district', 'like', '%' . request('keyword') . '%')->where('city', request('city'))->groupBy('district')->orderBy('district', 'ASC')->get());
    }

    public function subdistricts()
    {
        return response()->json(MasterAddress::select('subdistrict')->where('subdistrict', 'like', '%' . request('keyword') . '%')->where('district', request('district'))->groupBy('subdistrict')->orderBy('subdistrict', 'ASC')->get());
    }

    public function postalcodes()
    {
        $query = MasterAddress::select('postalcode')->where('subdistrict', request('subdistrict'));
        if(!empty(request('district'))) {
            $query->where('district', request('district'));
        }
        $postalCodes = $query->groupBy('postalcode')->orderBy('postalcode', 'ASC')->get();
        return response()->json($postalCodes);
    }
}
