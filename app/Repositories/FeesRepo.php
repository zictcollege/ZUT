<?php

namespace App\Repositories;

use App\Models\Fees;

class FeesRepo
{
    public function create($data)
    {
        return Fees::create($data);
    }

    public function getAll($order = 'id')
    {
        return Fees::orderBy($order)->get();
    }

    public function getPeriodType($data)
    {
        return Fees::where($data)->get();
    }

    public function update($id, $data)
    {
        return Fees::find($id)->update($data);
    }

    public function find($id)
    {
        return Fees::find($id);
    }
}
