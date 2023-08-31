<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvigilatorController extends Controller
{
    //
    public function setInvigilator(Request $request)
    {
        $invigilator = Invigilator::create([
            'name' => $request->input('name'),
        ]);

        return response()->json(['message' => 'Invigilator set successfully.', 'data' => $invigilator], 201);
    }

    public function setClassroom(Request $request)
    {
        $classroom = Classroom::create([
            'name' => $request->input('name'),
        ]);

        return response()->json(['message' => 'Classroom set successfully.', 'data' => $classroom], 201);
    }
    
    public function setCourse(Request $request)
    {
        $course = Course::create([
            'name' => $request->input('name'),
        ]);

        return response()->json(['message' => 'Course set successfully.', 'data' => $course], 201);
    }
}
