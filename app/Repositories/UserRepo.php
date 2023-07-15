<?php

namespace App\Repositories;


use App\Models\User;
use App\Models\UserTypes;


class UserRepo {


    public function update($id, $data)
    {
        return User::find($id)->update($data);
    }

    public function delete($id)
    {
        return User::destroy($id);
    }

    public function create($data)
    {
        return User::create($data);
    }

    public function getUserByType($type)
    {
        return User::where(['user_type' => $type])->orderBy('name', 'asc')->get();
    }

    public function getAllTypes()
    {
        return UserTypes::all();
    }

    public function findType($id)
    {
        return UserTypes::find($id);
    }

    public function find($id)
    {
        return User::find($id);
    }

    public function getAll()
    {
        return User::orderBy('first_name', 'asc')->get();
    }

    public function getPTAUsers()
    {
        return User::where('user_type', '<>', 'student')->orderBy('first_name', 'asc')->get();
    }

    /********** STAFF RECORD ********/
    public function createStaffRecord($data)
    {
        //return StaffRecord::create($data);
    }

    public function updateStaffRecord($where, $data)
    {
        //return StaffRecord::where($where)->update($data);
    }

    /********** BLOOD GROUPS ********/
    public function getBloodGroups()
    {
        //return BloodGroup::orderBy('name')->get();
    }
}
