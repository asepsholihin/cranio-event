<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserPlatform;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WebSale;
use App\Models\DepartmentUser;
use App\Models\MasterOfficeUser;
use DB;

class UserPlatformSPAController extends Controller
{
    const SPA_PATH = '/user-platform';

    public function __construct()
    {
        $this->middleware('permission:user-platform-view|web-sales-view')->only(['queryUsers']);
        $this->middleware('permission:user-platform-view')->only(['index','show']);
        $this->middleware('permission:user-platform-add-or-edit')->only('store');
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

        return response()->json(array_merge(
            User::tableSearch()->with('departments:id,name')
                ->orderBy($orderBy, $sortBy)
                ->paginate($perPage)
                ->withQueryString()
                ->withPath(self::SPA_PATH)
                ->toArray(),
            ['permissions' => config('platform-permission')])
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreParticipantRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserPlatform $request)
    {
        $user = User::updateOrCreate(['id' => $request->get('id')], $request->except(['permission_checked','department_id','office_id']));
        $user->syncPermissions(explode(',', $request->get('permission_checked')));
        $salesMan = WebSale::where('user_id', $user->id)->first();
        if($salesMan) {
            $salesMan->sales_name = $user->name;
            $salesMan->save();
        }
        $user->setDepartment();
    }

    public function show($id)
    {
        $user = User::with(['departments:id,name'])->findOrFail($id);
        $permission = config('platform-permission');
        return response()->json(array_merge(
            $user->toArray(),
            ['permissions_list' => $permission, 'permission_checked'=> $user->getDirectPermissions()->pluck('name')])
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Participant  $participant
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function queryUserSales(Request $request)
    {
        $search = $request->get('q');
        $search = '%' . $search .'%';
        $result = User::select(['id','name'])
            //->where('department_id', 3)
            ->where('name', 'like', $search)
            ->get();
        return response()->json($result);
    }

    public function refineUserDepartmentOffice() {
        $users = User::select('id','department_id')->get();

        DB::transaction(function() use($users) {
            foreach ($users as $user) {
                if($user->department_id) {
                    DepartmentUser::updateOrCreate([
                        'user_id' => $user->id,
                        'department_id' => $user->department_id
                    ]);
                }
            }
        });
        return response()->json($users);
    }
}
