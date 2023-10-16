<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamRegisteredCourses extends Model
{
    //
    protected $table = 'ac_examination_registration_courses';
    protected $guarded = ['id'];
    
}
