<?php

namespace App\Repositories;

use App\Models\Academics\period_types;

class PeriodTypeRepo
{
    public function create($data)
    {
        return period_types::create($data);
    }

    public function getAll($order = 'name')
    {
        return period_types::orderBy($order)->get();
    }

    public function getPeriodType($data)
    {
        return period_types::where($data)->get();
    }

    public function update($id, $data)
    {
        return period_types::find($id)->update($data);
    }

    public function find($id)
    {
        return period_types::find($id);
    }

}
