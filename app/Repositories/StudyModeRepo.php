<?php

namespace App\Repositories;

use App\Models\Academics\studyModes;

class StudyModeRepo
{

    public function create($data)
    {
        return studyModes::create($data);
    }

    public function getAll($order = 'name')
    {
        return studyModes::orderBy($order)->get();
    }

    public function getStudyMode($data)
    {
        return studyModes::where($data)->get();
    }

    public function update($id, $data)
    {
        return studyModes::find($id)->update($data);
    }

    public function find($id)
    {
        return studyModes::find($id);
    }


}
