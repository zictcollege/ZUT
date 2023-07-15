<?php

namespace App\Repositories;

use App\Models\Programs;

class ProgramsRepo
{
    public function create($data)
    {
        return Programs::create($data);
    }

    public function getAll($order = 'name')
    {
        return Programs::with('department','qualification')->orderBy($order)->get();
    }
    public function getAllProgramQualification($qualificationId)
    {
        return Programs::where('qualification_id',$qualificationId)->orderBy('name','ASC')->get();
    }
    public function getPeriodType($data)
    {
        return Programs::where($data)->get();
    }

    public function update($id, $data)
    {
        return Programs::find($id)->update($data);
    }

    public function find($id)
    {
        return Programs::with('department','qualification')->find($id);
    }
}
