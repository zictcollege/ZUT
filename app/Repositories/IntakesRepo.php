<?php

namespace App\Repositories;

use App\Models\Academics\Intakes;

class IntakesRepo
{
    public function create($data)
    {
        return Intakes::create($data);
    }

    public function getAll($order = 'name')
    {
        return Intakes::orderBy($order)->get();
    }

    public function getPeriodType($data)
    {
        return Intakes::where($data)->get();
    }

    public function update($id, $data)
    {
        return Intakes::find($id)->update($data);
    }

    public function find($id)
    {
        return Intakes::find($id);
    }
}
