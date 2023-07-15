<?php

namespace App\Repositories;

use App\Models\Towns;

class TownsRepo
{
    public function create($data)
    {
        return Towns::create($data);
    }

    public function getAll($order = 'id')
    {
        return Towns::orderBy($order,'asc')->get();
    }

    public function getAlls($state_id)
    {
        return Towns::where('state_id',$state_id)->orderBy('name','asc')->get();
    }
    public function update($id, $data)
    {
        return Towns::find($id)->update($data);
    }

    public function find($id)
    {
        return Towns::find($id);
    }

}
