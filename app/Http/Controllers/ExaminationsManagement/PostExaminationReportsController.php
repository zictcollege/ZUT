<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostExaminationReportsController extends Controller
{
    public function create()
    {
        // Retrieve the list of courses to display in the form
        $courses = Course::all();

        return view('examinations.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'events_and_incidents' => 'nullable|string',
        ]);

        Examination::create($request->all());

        return redirect()->route('examinations.create')->with('success', 'Examination created successfully!');
    }

}
