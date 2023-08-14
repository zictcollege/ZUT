<?php

namespace App\Repositories;

use App\Models\Academics\Courses;
use Illuminate\Support\Facades\DB;

class CoursesRepo
{
    public function create($data)
    {
        return Courses::create($data);
    }

    public function getAll($order = 'code')
    {
        return Courses::orderBy($order,'asc')->get();
    }
    public function getProgramCourses($id)
    {
        $courses = DB::table('ac_programCourses')
            ->where('ac_programCourses.programID', '=', $id)
            ->join('ac_courses', 'ac_courses.id', '=', 'ac_programCourses.courseID')
            ->select(
                'ac_courses.id as id',
                'ac_courses.name as name',
                'ac_courses.code as code'
            )->orderBy('level_id','asc')
            ->get();
        //return Courses::orderBy($order,'asc')->get();
        return $courses;
    }

    public function getPeriodType($data)
    {
        return Courses::where($data)->get();
    }

    public function update($id, $data)
    {
        return Courses::find($id)->update($data);
    }

    public function find($id)
    {
        return Courses::find($id);
    }
}
