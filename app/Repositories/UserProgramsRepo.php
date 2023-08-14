<?php

namespace App\Repositories;

use App\Models\Admissions\UserProgram;

class UserProgramsRepo
{
    public function create($data)
    {
        return UserProgram::create($data);
    }

    public function getAll($order = 'name')
    {
        return UserProgram::get();
    }
    public function update($id, $data)
    {
        return UserProgram::find($id)->update($data);
    }

    public function find($id)
    {
        return UserProgram::find($id);
    }
}