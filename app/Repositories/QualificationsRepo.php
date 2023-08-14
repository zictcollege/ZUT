<?php

namespace App\Repositories;

use App\Models\Academics\Qualifications;

class QualificationsRepo
{
    public function create($data)
    {
        return Qualifications::create($data);
    }

    public function getAll($order = 'name')
    {
        return Qualifications::orderBy($order)->get();
    }

    public function getPeriodType($data)
    {
        return Qualifications::where($data)->get();
    }

    public function update($id, $data)
    {
        return Qualifications::find($id)->update($data);
    }

    public function find($id)
    {
        return Qualifications::find($id);
    }
}
