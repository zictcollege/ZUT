<?php

namespace App\Models\Accounting;


use Illuminate\Database\Eloquent\Model;

class Statement extends Model
{
    protected $table = "statements";
    protected $guarded = ['id'];

}
