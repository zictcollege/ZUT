<?php

namespace App\Models;

use App\Models\Academics\AcademicPeriods;
use App\Models\Academics\Classes;
use App\Models\Academic\AcademicPeriod;
use App\Models\Academic\AcClass;
use App\Models\Academic\ClassAssessment;
use App\Models\Academic\ProgramCourse;
use App\Models\Admissions\ProgramCourses;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $table = "ac_enrollments";
    protected $guarded = ['id'];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'classID', 'id');
        
    }

    public function assesment() {
        return $this->hasOne(Academics\ClassAssessment::class,'classID','classID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }
    public static function data($id) // UserProgramID
    {
        $userProgram    = Admissions\UserProgram::find($id);
        $programCourses = ProgramCourses::where('programID', $userProgram->programID)->get();

        foreach ($programCourses as $programCourse) {
            $courseIDs[] = $programCourse->courseID;
        }

        $classes = Classes::wherein('courseID', $courseIDs)->get();
        foreach ($classes as $class) {
            $classIDs[] = $class->id;
        }
        $enrollments = Enrollment::wherein('classID', $classIDs)->where('userID', $userProgram->userID)->get();
        foreach ($enrollments as $enrollment) {
            $attendedClassIDs[] = $enrollment->classID;
        }

        $myClasses = Classes::wherein('id', $attendedClassIDs)->get()->unique('academicPeriodID');

        foreach ($myClasses as $class) {
            $academicPeriod = AcademicPeriods::find($class->academicPeriodID);
            $_ap            = AcademicPeriods::data($academicPeriod->id, 0, $userProgram->userID);
            $_aps[]         = $_ap;
        }

        if (empty($_aps)) {
            $_aps = [];
        }
        return $_aps;
    }

}
