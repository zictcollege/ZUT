<?php

namespace App\Repositories;

use App\Models\Academics\ClassAssessment;
use App\Models\Results\ImportList;

class ClassAssessmentsRepo
{
    public function create($data)
    {
        return ClassAssessment::create($data);
    }

    public function getAll()
    {
        return ClassAssessment::with('classes','assessments')->get();
    }

    public function getPeriodType($data)
    {
        return ClassAssessment::where($data)->get();
    }

    public function update($id, $data)
    {
        return ClassAssessment::find($id)->update($data);
    }
    public function addResults($data)
    {
        return ImportList::create($data);;
    }
    public function updateImportlistResults($id, $data)
    {
        return ImportList::find($id)->update($data);
    }

    public function find($id)
    {
        return ClassAssessment::with('classes','assessments')->find($id);
    }

}
