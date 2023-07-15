<?php

namespace App\Repositories;

use App\Models\AssessmentTypes;

class AssessmentTypesRepo
{
    public function create($data)
    {
        return AssessmentTypes::create($data);
    }

    public function getAll($order = 'name')
    {
        return AssessmentTypes::orderBy($order,'asc')->get();
    }
    public function getPeriodType($data)
    {
        return AssessmentTypes::where($data)->get();
    }

    public function update($id, $data)
    {
        return AssessmentTypes::find($id)->update($data);
    }

    public function find($id)
    {
        return AssessmentTypes::find($id);
    }

}
