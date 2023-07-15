<?php

namespace App\Repositories;

use App\Models\States;

class StatesRepo
{
    public function create($data)
    {
        return States::create($data);
    }

    public function getAll($order = 'id')
    {
        return States::orderBy($order,'asc')->get();
    }
    public function getAlls($country_id)
    {
        return States::where('country_id',$country_id)->orderBy('name','asc')->get();
    }

    public function update($id, $data)
    {
        return States::find($id)->update($data);
    }

    public function find($id)
    {
        return States::find($id);
    }
}
