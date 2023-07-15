<?php

namespace App\Http\Controllers\Academics;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Http\Requests\ProgramCourses\ProgramCourse;
use App\Repositories\ProgramCoursesRepo;
use Illuminate\Http\Request;

class ProgramCoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $programCoursesRepo;
    public function __construct(ProgramCoursesRepo $programCoursesRepo)
    {
        $this->middleware(TeamSA::class, ['except' => ['destroy',] ]);
        $this->middleware(SuperAdmin::class, ['only' => ['destroy',] ]);


        $this->programCoursesRepo = $programCoursesRepo;
    }
    public function index()
    {
        //
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
    public function store(ProgramCourse $req)
    {
        $data = $req->only(['courseID', 'programID','level_id']);

        foreach ($data['courseID'] as $courseID) {
            $this->programCoursesRepo->create([
                'courseID' => $courseID,
                'programID' => $data['programID'],
                'level_id' => $data['level_id'],
            ]);
        }

        return Qs::jsonStoreOk();
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
    public function destroy(string $program,string $level,string $course)
    {
        $this->programCoursesRepo->findProgramlevelCoursesDelete($program,$level,$course)->delete();
        return back()->with('flash_success', __('msg.delete_ok'));
    }
}
