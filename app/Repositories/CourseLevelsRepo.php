<?php

namespace App\Repositories;

use App\Models\Academics\CourseLevels;

class CourseLevelsRepo
{
    public function create($data)
    {
        return CourseLevels::create($data);
    }

    public function getAll($order = 'name')
    {
        return CourseLevels::orderBy($order,'asc')->get();
    }

    public function getPeriodType($data)
    {
        return CourseLevels::where($data)->get();
    }

    public function update($id, $data)
    {
        return CourseLevels::find($id)->update($data);
    }

    public function find($id)
    {
        return CourseLevels::find($id);
    }
}
