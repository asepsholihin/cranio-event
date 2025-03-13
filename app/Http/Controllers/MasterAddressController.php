<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterAddress;

class MasterAddressController extends Controller
{
    const SPA_PATH = '/address';

    public function __construct()
    {
        //$this->middleware('permission:participant-view');
    }

    public function provinces()
    {
        return response()->json(MasterAddress::select('province')->groupBy('province')->orderBy('province', 'ASC')->get());
    }

    public function cities()
    {
        return response()->json(MasterAddress::select('city')->where('province', request('province'))->groupBy('city')->orderBy('city', 'ASC')->get());
    }

    public function districts()
    {
        return response()->json(MasterAddress::select('district')->where('city', request('city'))->groupBy('district')->orderBy('district', 'ASC')->get());
    }

    public function subdistricts()
    {
        return response()->json(MasterAddress::select('subdistrict')->where('district', request('district'))->groupBy('subdistrict')->orderBy('subdistrict', 'ASC')->get());
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

    public function queryCities(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search . '%';
        $result = MasterAddress::select(['city'])
            ->where('city', 'like', $search)
            ->groupBy('city')->orderBy('city', 'ASC')
            ->limit(10)
            ->get();
        return response()->json($result);
    }
}
