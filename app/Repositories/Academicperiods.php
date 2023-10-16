<?php

namespace App\Repositories;

use App\Models\Results\ImportList;

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
    public static function getAllOpened($order = 'created_at')
    {
        //return \App\Models\AcademicPeriods::has('types')->whereDate('acEndDate', '>', now())->orderByDesc($order)->get();
        $academicPeriods = \App\Models\Academics\AcademicPeriods::join('ac_classes','ac_classes.academicPeriodID','=','ac_academicPeriods.id')
        ->where('instructorID','=',\Auth::user()->id)
            ->whereDate('acEndDate', '>=', now())
            ->orderByDesc($order)
            ->distinct('ac_academicPeriods.academicPeriodID')
            ->get();
        if (\Auth::user()->user_type == 'instructor'){
//            return ImportList::join('ac_academicPeriods','ac_academicPeriods.id','=','ac_gradebook_imports.academicPeriodID')
//            ->join('ac_classes', 'ac_classes.academicPeriodID', '=', 'ac_academicPeriods.id')
//                ->where('instructorID', '=', \Auth::user()->id)
//                ->whereDate('ac_academicPeriods.acEndDate', '>=', now())
//                ->select('ac_academicPeriods.id','ac_academicPeriods.code') // Select the column to be distinct
//                ->distinct() // Apply distinct to the selected column
//                ->orderByDesc($order)
//                ->get();
//            return  \App\Models\Academics\AcademicPeriods::where('instructorID','=',\Auth::user()->id)
//                ->whereDate('acEndDate', '>=', now())
//                ->distinct('ac_academicPeriods.code')
//            ->join('ac_classes','ac_classes.academicPeriodID','=','ac_academicPeriods.id')
//                //->where('instructorID','=',\Auth::user()->id)
//                //->whereDate('acEndDate', '>=', now())
//                ->orderByDesc($order)
//                //->distinct('ac_academicPeriods.code')
//                ->get();

           return \App\Models\Academics\AcademicPeriods::select('code', 'ac_academicPeriods.id')
               ->where('instructorID', '=', \Auth::user()->id)
               ->whereDate('acEndDate', '>=', now())
               ->join('ac_classes', 'ac_classes.academicPeriodID', '=', 'ac_academicPeriods.id')
               ->distinct()
               ->orderByDesc($order)
               ->get();

// $distinctAcademicPeriods now contains a collection of distinct 'code' and 'id' values

        }else {
//            return ImportList::join('ac_academicPeriods','ac_academicPeriods.id','=','ac_gradebook_imports.academicPeriodID')
//                ->whereDate('ac_academicPeriods.acEndDate', '>=', now())
//                ->select('ac_academicPeriods.id','ac_academicPeriods.code') // Select the column to be distinct
//                ->distinct() // Apply distinct to the selected column
//                ->orderByDesc($order)
//                ->get();
            return  \App\Models\Academics\AcademicPeriods::whereDate('acEndDate', '>=', now())
                ->orderByDesc($order)
                ->distinct('ac_academicPeriods.id')
                ->get();

        }
    }

    public static function getAllReadyPublish($order = 'created_at')
    {
        //return \App\Models\AcademicPeriods::has('types')->whereDate('acEndDate', '>', now())->orderByDesc($order)->get();
        $academicPeriods = \App\Models\Academics\AcademicPeriods::join('ac_classes','ac_classes.academicPeriodID','=','ac_academicPeriods.id')
            ->where('instructorID','=',\Auth::user()->id)
            ->whereDate('acEndDate', '>=', now())
            ->orderByDesc($order)
            ->distinct('ac_academicPeriods.academicPeriodID')
            ->get();
        if (\Auth::user()->user_type == 'instructor'){
            return ImportList::join('ac_academicPeriods','ac_academicPeriods.id','=','ac_gradebook_imports.academicPeriodID')
            ->join('ac_classes', 'ac_classes.academicPeriodID', '=', 'ac_academicPeriods.id')
                ->where('instructorID', '=', \Auth::user()->id)
                ->where('student_level_id', '>', 0)
                ->whereDate('ac_academicPeriods.acEndDate', '>=', now())
                ->select('ac_academicPeriods.id','ac_academicPeriods.code') // Select the column to be distinct
                ->distinct() // Apply distinct to the selected column
                ->orderByDesc($order)
                ->get();

        }else {
            return ImportList::join('ac_academicPeriods','ac_academicPeriods.id','=','ac_gradebook_imports.academicPeriodID')
                ->whereDate('ac_academicPeriods.acEndDate', '>=', now())
                ->where('student_level_id', '>', 0)
                ->select('ac_academicPeriods.id','ac_academicPeriods.code') // Select the column to be distinct
                ->distinct() // Apply distinct to the selected column
                ->orderByDesc($order)
                ->get();

        }
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
