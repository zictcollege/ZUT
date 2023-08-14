<?php

namespace App\Models\Results;

use App\FailedResultImport;
use App\Models\Academics\ResultsImport;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ImportList extends Model
{
    protected $guarded =['id'];
    protected $table = "ImportLists";

    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public function rows()
    {
      return $this->hasMany(ResultsImport::class,'list_id','id');
    }
    public function failedImports() {
      return $this->hasMany(FailedResultImport::class,'list_id','id');
    }



}
