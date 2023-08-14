<?php

namespace App\Repositories;

use App\Models\Admissions\UserStudyModes;

class UserModesRepo
{
    public function create($data)
    {
        return UserStudyModes::create($data);
    }

    public function getAll($order = 'name')
    {
        return UserStudyModes::get();
    }
    public function update($id, $data)
    {
        return UserStudyModes::find($id)->update($data);
    }

    public function find($id)
    {
        return UserStudyModes::find($id);
    }
}