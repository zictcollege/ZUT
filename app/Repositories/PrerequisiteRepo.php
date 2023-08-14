<?php

namespace App\Repositories;

use App\Models\Academics\Prerequisite;
use Illuminate\Support\Facades\DB;

class PrerequisiteRepo
{

    public function create($data)
    {
        return Prerequisite::create($data);
    }

    public function getAll($order = 'name')
    {
        $courses = DB::table('ac_prerequisites')
            ->join('ac_courses','ac_prerequisites.courseID','=','ac_courses.id')
            ->join('ac_courses as prerequisite','ac_prerequisites.prerequisiteID','=','prerequisite.id')
            ->select('ac_prerequisites.id as pid','ac_courses.id as id','prerequisite.id as prerequisite_id','prerequisite.code as prerequisite_code',
            'prerequisite.name as prerequisite_name','ac_courses.name as name','ac_courses.code as code')
            ->get();
        //return Prerequisite::orderBy($order)->get();
        return $courses;
    }

    public function update($id, $data)
    {
        return Prerequisite::find($id)->update($data);
    }

    public function find($id)
    {
        $courses = DB::table('ac_prerequisites')
            ->where('ac_prerequisites.id','=',$id)
            ->join('ac_courses','ac_prerequisites.courseID','=','ac_courses.id')
            ->join('ac_courses as prerequisite','ac_prerequisites.prerequisiteID','=','prerequisite.id')
            ->select('ac_prerequisites.id as pid','ac_courses.id as id','prerequisite.id as prerequisite_id','prerequisite.code as prerequisite_code',
                'prerequisite.name as prerequisite_name','ac_courses.name as name','ac_courses.code as code')
            ->first();
        //return Prerequisite::orderBy($order)->get();
        return $courses;
        //return Prerequisite::find($id);
    }
    public function findone($id)
    {
        return Prerequisite::find($id);
    }
}
