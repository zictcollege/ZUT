<?php

namespace App\Models\Academics;

use App\Models\Admissions\ProgramCourses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLevels extends Model
{
    protected $table = 'ac_course_levels';
    use HasFactory;
    protected $fillable = ['name'];

    public function programCourses()
    {
        return $this->hasMany(ProgramCourses::class, 'level_id');
    }
    protected $guarded = ['id'];

    public static function data($id)
    {
        $level = CourseLevels::find($id);
        return [
            'id'     => $level->id,
            'key'    => $level->id,
            'name'   => $level->name,
        ];
    }

    public static function courses($levelID, $programID, $intakeID = NULL)
    {
        $_courses = [];

        if ($intakeID != NULL) {
            $courses = ProgramCourses::where('level_id', $levelID)->where('programID', $programID)->where('programIntakeID', $intakeID)->get();
        } else {
            $courses = ProgramCourses::where('level_id', $levelID)->where('programID', $programID)->get();
        }
        foreach ($courses as $course) {
            $_course    = Courses::data($course->courseID,$programID);
            $_courses[] = $_course;
        }
        return $_courses;
    }

}
