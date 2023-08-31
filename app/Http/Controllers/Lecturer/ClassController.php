<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        // View assigned classes and class lists
        
    }

    public function showStudents($class_id)
    {
        // View class lists
        // Fetch the class data using $class_id
        $class =Class::find($class_id);

        // Check if the class exists
        if (!$class) {
            abort(404); // Class not found
        }

        // Get the list of students for the class
        $students = $class->students;

        // Load the 'classes.students' Blade view and pass data to it
        return view('classes.students', compact('class', 'students'));
        
    }

    public function showTests($class_id)
    {
        // View due dates for tests
        
    }

    public function showAssignments($class_id)
    {
        // View due dates for assignments
        
    }

    public function showLabs($class_id)
    {
        // View due dates for labs
        
    }
}
