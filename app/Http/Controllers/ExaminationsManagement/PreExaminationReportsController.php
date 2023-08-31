<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PreExaminationReportsController extends Controller
{
    //Function to get exam eligibility based on a variable threshold
    public function getExamEligibility($threshold)
    {
        $eligibleExaminations = Examination::where('eligible_students', '>=', $threshold)->get();
        $ineligibleExaminations = Examination::where('eligible_students', '<', $threshold)->get();

        return view('exam_eligibility', compact('eligibleExaminations', 'ineligibleExaminations'));
    }
     

    // Function to get the number of students who can write/cannot write per cohort
    // You will need to define the "Cohort" model and its relationship with the "Course" model to implement this.

    // Function to get the number of students who can write/cannot write per programme
    // You will need to define the "Programme" model and its relationship with the "Course" model to implement this.

    // Function to get the number of students who can write/cannot write per course
    // You can use the "getExamEligibility" function for this purpose.

    // Function to get examination lists per course
    public function getExaminationListsByCourse($courseId)
    {
        $examinationList = Examination::where('course_id', $courseId)->get();

        // Assuming you have defined the "Course" and "Invigilator" models for Course and Invigilator information.

        return view('examination_lists', compact('examinationList'));
    }

    // Function to get student name, ID, Course code, course name, and invigilator for a specific examination
    public function getExaminationDetails($examinationId)
    {
        $examination = Examination::findOrFail($examinationId);

        // Assuming you have defined the "Student" model for student information.

        $students = $examination->course->students;

        // Assuming you have defined the "Invigilator" model for invigilator information.

        $invigilator = $examination->course->invigilator;

        return view('examination_details', compact('examination', 'students', 'invigilator'));
    }
}
