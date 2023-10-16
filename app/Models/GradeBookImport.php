<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeBookImport extends Model
{
    protected $table = "ac_gradebook_imports";
    protected $fillable = ['programID','academicPeriodID', 'studentID', 'assessmentID','title', 'code','key' , 'total' ,'status' ,'student_level_id','created_at','updated_at'];
    protected $guarded = ['id'];
}
