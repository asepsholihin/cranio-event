<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\StorageAttributes;
use Illuminate\Support\Str;
use DB;

class User extends Authenticatable implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPermissions, \OwenIt\Auditing\Auditable;

    const ACCESS_STATUS_ACTIVE = 1;
    const ACCESS_STATUS_IN_ACTIVE = 2;

    const PHOTO = 'photo';
    const DIR_PHOTO = 'users/profiles';
    const DIR_THUMBNAIL = 'thumbnail/';

    /*
    ROLE_ID 1 = Administrator
    ROLE_ID 2 = Staff
    ROLE_ID 3 = Manager
    ROLE_ID 4 = Vice President
    ROLE_ID 5 = Directors
    */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'access_status',
        'password',
        'profile_photo_path',
        'is_mitra',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
        'roles'
    ];

    protected $appends = [
        'permission_list',
        'role_list',
        'department_ids',
        'profile_thumbnail',
        'profile_photo'
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profilePhoto(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['profile_photo_path'] ?? null
            ),
        );
    }

    public function profileThumbnail(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => StorageAttributes::getTempUrl(
                $attributes['profile_photo_path'] ?? null,
                self::DIR_THUMBNAIL
            ),
        );
    }

    public function scopeTableSearch($query)
    {
        $keyword = request()->query('q', '');
        $query->leftjoin('department_user', 'department_user.user_id', 'users.id');
        $query->select('users.*');
        $query->whereNot(function ($query) {
            $query->where('users.id', Auth::user()->id)->orWhere('users.id', 1);
        })->where(function ($query) {
            $search = '%' . request()->query('q') .'%';
            $query->where(function($q) use($search) {
                $q->where('users.name', 'like', $search)
                ->orWhere('users.email', 'like', $search);
            });
        });

        if (!empty(request()->query('department'))) {
            $query->where('department_user.department_id', request()->query('department'));
        }
        $query->whereNotIn('users.id', [Auth::user()->id, 1]);

        return $query;
    }

    public function getPermissionListAttribute()
    {
        return $this->getAllPermissions()->pluck('name');
    }

    public function getRoleListAttribute()
    {
        return $this->getRoleNames();
    }

    public function getDepartmentIdsAttribute()
    {
        $departments = DepartmentUser::where('user_id', $this->id)->pluck('department_id')->toArray();
        return $departments;
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class);
    }

    public function setDepartment() {
        $departments = explode(",", request()->departments);
        // Delete Department
        DB::table('department_user')->where('user_id', $this->id)->whereNotIn('department_id', $departments)->delete();

        $department_user = DB::table('department_user')->where('user_id', $this->id)->whereIn('department_id', $departments)->get();
        foreach ($departments as $key => $department_id) {
            $user_department = DB::table('department_user')->where('user_id', $this->id)->where('department_id', $department_id)->first();
            if(!$user_department) {
                DB::table('department_user')->insert([
                    'user_id' => $this->id,
                    'department_id' => $department_id
                ]);
            }
        }
    }
}
