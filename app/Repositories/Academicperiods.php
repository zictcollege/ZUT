<?php

namespace App\Repositories;

class Academicperiods
{
    public function create($data)
    {
        return \App\Models\Academics\AcademicPeriods::create($data);
    }

    public function getAll($order = 'created_at')
    {
        return \App\Models\Academics\AcademicPeriods::orderByDesc($order)->get();
    }
    public function getAllopen($order = 'created_at')
    {
        //return \App\Models\AcademicPeriods::has('types')->whereDate('acEndDate', '>', now())->orderByDesc($order)->get();
        $academicPeriods = \App\Models\Academics\AcademicPeriods::with('periodType', 'studyMode')
            ->whereDate('acEndDate', '>=', now())
            ->orderByDesc($order)
            ->get();

        return $academicPeriods;

    }
    public function getAllClosed($order = 'created_at')
    {
        return \App\Models\Academics\AcademicPeriods::with('periodType', 'studyMode')->whereDate('acEndDate', '<', now())->orderByDesc($order)->get();
    }

    public function getAcademicPeriod($data)
    {
        return \App\Models\Academics\AcademicPeriods::where($data)->get();
    }

    public function update($id, $data)
    {
        return \App\Models\Academics\AcademicPeriods::find($id)->update($data);
    }

    public function find($id)
    {
//        $academics = \App\Models\AcademicPeriods::with('periodType', 'studyMode','classes.course.programCourses.levels')
//            ->where('id', $id)
//            ->orderBy('code')
//            ->get();
        return \App\Models\Academics\AcademicPeriods::with('periodType', 'studyMode','intake')->find($id);
        //return $academics;
    }
}
