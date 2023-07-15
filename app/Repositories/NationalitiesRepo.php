<?php

namespace App\Repositories;

use App\Models\Nationalities;

class NationalitiesRepo
{
    public function create($data)
    {
        return Nationalities::create($data);
    }

    public function getAll($order = 'id')
    {
        return Nationalities::orderBy($order,'asc')->get();
    }

    public function update($id, $data)
    {
        return Nationalities::find($id)->update($data);
    }

    public function find($id)
    {
        return Nationalities::find($id);
    }

}
