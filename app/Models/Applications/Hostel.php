<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    protected $guarded = ['id'];

    public function rooms()
    {
      return $this->hasMany(Room::class);
    }

}
