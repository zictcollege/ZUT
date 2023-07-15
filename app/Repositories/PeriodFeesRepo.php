<?php

namespace App\Repositories;

class PeriodFeesRepo
{
    public function create($data)
    {
        return \App\Models\PeriodFees::create($data);
    }

    public function getAll($order = 'name')
    {
        return \App\Models\PeriodFees::orderBy($order)->get();
    }

    public function getPeriodFees($data)
    {
        return \App\Models\PeriodFees::where('academicPeriodID',$data)->with('academicPeriods','fees','studymode')->get();
    }

    public function update($id, $data)
    {
        return \App\Models\PeriodFees::find($id)->update($data);
    }

    public function find($id)
    {
        return \App\Models\PeriodFees::find($id);
    }
}
