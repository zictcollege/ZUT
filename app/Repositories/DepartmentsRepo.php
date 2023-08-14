<?php

namespace App\Repositories;

use App\Models\Academics\Departments;

class DepartmentsRepo
{
    public function create($data)
    {
        return Departments::create($data);
    }

    public function getAll($order = 'name')
    {
        return Departments::orderBy($order)->get();
    }

    public function getPeriodType($data)
    {
        return Departments::where($data)->get();
    }

    public function update($id, $data)
    {
        return Departments::find($id)->update($data);
    }

    public function find($id)
    {
        return Departments::with('programs')->find($id);
    }
}
