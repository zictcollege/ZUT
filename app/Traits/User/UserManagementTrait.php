<?php

namespace App\Traits\User;


use App\Models\User;
use App\Traits\User\General;
use Couchbase\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


trait UserManagementTrait
{

    public function getUsers()
    {

        $users = User::where('role_id', 2)->get();

        foreach ($users as $us) {
            $User    = User::jsondata($us->id);
            $Users[] = $User;
        }

        return $Users;
    }

    public function definedRoles()
    {
        $rawData = Role::all();
        $roles = [];

        foreach ($rawData as $role) {
            $roles[] = Role::data($role->id);
        }
        return $roles;
    }
    public function definedRolesAndPermissions()
    {
        $rawData = Role::all();
        $permissions = Permission::all();
        $roles = [];

        foreach ($rawData as $role) {
            $roles[] = Role::data($role->id);
        }

        return [
            'roles'         => $roles,
            'permissions'   => $permissions,
        ];
    }


    public function userRoles($userid)
    {

        $user = User::find($userid);
        return $user;
    }

    public function roles()
    {
        return Role::where('name', '!=', 'student')->where('name', '!=', 'guest')->get()->all();
    }

    public function assignRoles(Request $request)
    {
        $this->Validate($request, array(
            'role'              => 'required',
        ));
        $user = User::find(request('userid'));
        $user->syncRoles(request('role'));
        return $user->getRoleNames();
    }


    public function getRoles($userid)
    {
    }

    public function changePassword(Request $request)
    {


        $this->Validate($request, array(
            'password'              => 'required|min:6|confirmed',
            'force_password_change' => 'required',
        ));

        try {
            DB::beginTransaction();
            # Find user
            $user = User::find(request('userid'));
            $user->password = bcrypt(request('password'));
            $user->force_password_reset = request('force_password_change');
            $user->save();

            DB::commit();

            $user->notify(new PasswordChanged($user, request('password')));
        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);
        }
    }
    public function changePasswordUser(Request $request)
    {


        $this->Validate($request, array(
            'password'              => 'required|min:6|confirmed',
            'force_password_change' => 'required',
        ));

        try {
            DB::beginTransaction();
            # Find user
            $user = User::find(request('userid'));
            $user->password = bcrypt(request('password'));
            $user->force_password_reset = false;
            $user->save();

            DB::commit();
        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    public function addDefinedRole(Request $request)
    {

        $this->Validate($request, array(
            'name'         => 'required|unique:roles',
        ));

        Role::create([
            'name'  => request('name'),
            'guard_name'    => 'web',
        ]);
    }

    public function addDefinedPermission(Request $request)
    {

        $this->Validate($request, array(
            'name'         => 'required|unique:permissions',
        ));

        Permission::create([
            'name'  => request('name'),
            'guard_name'    => 'web',
        ]);
    }
    public function attachPermission(Request $request)
    {

        $this->Validate($request, array(
            'permissionIDs'         => 'required',
        ));

        foreach (request('permissionIDs') as $permissionID) {
            $role = Role::find(request('role')['id']);
            $role->givePermissionTo($permissionID);
        }

        return Role::data($role->id);
    }
    public function removePermissions(Request $request)
    {
        foreach (request('selectedPermissions') as $permission) {
            $selectedPermision = RolePermission::where('permission_id', $permission)->where('role_id', request('role')['id']);
            if ($selectedPermision) {
                $selectedPermision->delete();
            }
        }
        return Role::data(request('role')['id']);
    }
}
