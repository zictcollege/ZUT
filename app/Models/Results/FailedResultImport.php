<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FailedResultImport extends Model
{

    protected $guarded = ['id'];
    protected $table = "importedResultsFailed";


}
