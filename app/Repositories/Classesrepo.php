<?php

namespace App\Repositories;

use App\Models\Classes;

class Classesrepo
{
    public function create($data)
    {
        return Classes::create($data);
    }

    public function getAll($order = 'id')
    {
        return Classes::orderBy($order,'asc')->get();
    }

    public function update($id, $data)
    {
        return Classes::find($id)->update($data);
    }

    public function find($id)
    {
        return Classes::find($id);
    }
}
