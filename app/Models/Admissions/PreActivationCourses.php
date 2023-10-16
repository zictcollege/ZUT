<?php

namespace App\Models\Admissions;

use Illuminate\Database\Eloquent\Model;

class PreActivationCourses extends Model
{
    protected $table = "ac_pre_activation_courses";
    protected $guarded = ['id'];
}
