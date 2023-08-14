<?php

namespace App\Models\Academics;

use App\ImportList;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ResultsImport extends Model
{
    protected $guarded =['id'];
    protected $table = "importedResults";

  public function user()
  {
    return $this->belongsTo(User::class);
  }


  public function list()
  {
      return $this->belongsTo(ImportList::class, 'list_id', 'id');
  }



}
