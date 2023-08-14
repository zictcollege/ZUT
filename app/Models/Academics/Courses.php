<?php

namespace App\Models\Academics;

use App\Models\Admissions\ProgramCourses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    protected $table = 'ac_courses';
    use HasFactory;
    protected $fillable = ['code', 'name'];

    public function programCourses()
    {
        return $this->hasMany(ProgramCourses::class, 'courseID','id');
    }
    // Define the relationship with ac_classes table
    public function classes()
    {
        return $this->hasMany(Classes::class, 'courseID');
    }

    protected $primaryKey = 'id';

    public function levels()
    {
        return $this->hasOne(CourseLevels::class, ProgramCourses::class, 'level_id', 'courseID');
    }

    public function level()
    {
        return $this->hasOneThrough(Courses::class, CourseLevels::class, 'id', 'level_id');
    }
    public static function data($id, $programID = 0)
    {

        $status = '';
        $course  = Courses::find($id);
        $_course = [];
        $status = 'Inactive';


        if ($programID != 0) {
            $levelsCourse = ProgramCourses::where('programID', $programID)->where('courseID', $course->id)->first();
            if ($levelsCourse->active == 1) {
                $status = 'Active';
            }
        }

        if (!empty($course)) {
            $prerequisiteRow = Prerequisite::where('courseID', $course->id)->first();

            if (!empty($prerequisiteRow)) {

                $prerequisite  = Courses::find($prerequisiteRow->prerequisiteID);
                $_course = [
                    'key'                   => $course->id,
                    'code'                  => $course->code,
                    'name'                  => $course->name,
                    'status'                => $status,
                    'prerequisite'          => $prerequisite,
                    'prerequisiteCode'      => $prerequisite->code,
                    'prerequisiteID'        => $prerequisite->id,
                    'prerequisiteName'      => $prerequisite->name,
                    'prerequisiteFullName'  => $prerequisite->code . ' - ' . $prerequisite->name,
                ];
            } else {
                $_course = [
                    'key'               => $course->id,
                    'code'              => $course->code,
                    'name'              => $course->name,
                    'status'            => $status,
                ];
            }
        }

        return $_course;
    }

}
