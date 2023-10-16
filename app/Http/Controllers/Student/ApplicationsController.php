<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationsController extends Controller
{
    use \App\Traits\User\General;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    //get functions
    public function ChangePrograms(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('pages.support_team.students.applications.change_program');
    }
    public function Exemption()
    {
        $users = self::jsondata(Auth::user()->id);
        //dd($users);
        return view('pages.support_team.students.applications.student_exemptions',compact('users'));
    }
    public function WithDef()
    {
        return view('pages.support_team.students.applications.withdraw_deferment');
    }
    public function ADCourses()
    {
        return view('pages.support_team.students.applications.a_d_courses');
    }
    public function ChangeStudyMode()
    {
        return view('pages.support_team.students.applications.change_study_mode');
    }
}
