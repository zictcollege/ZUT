<?php

namespace App\Models\Results;
use Illuminate\Support\Facades\DB;
//use App\StudentResults;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
  public static function insertImport($data){



        DB::table('student_results')->insert($data);
       //DB::table('StudentResults')->insert($data);

      }


}
