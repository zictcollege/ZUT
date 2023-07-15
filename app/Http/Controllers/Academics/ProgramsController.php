<?php

namespace App\Http\Controllers\Academics;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Http\Requests\Programs\Program;
use App\Http\Requests\Programs\ProgramUpdate;
use App\Repositories\CourseLevelsRepo;
use App\Repositories\CoursesRepo;
use App\Repositories\DepartmentsRepo;
use App\Repositories\ProgramCoursesRepo;
use App\Repositories\ProgramsRepo;
use App\Repositories\QualificationsRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $programs,$depart,$qualification,$programCourse,$levels,$courses;
    public function __construct(ProgramsRepo $programs,DepartmentsRepo $depat,
                                QualificationsRepo $qualification,ProgramCoursesRepo $programCourse,
    CoursesRepo $courses,CourseLevelsRepo $levels
    )
    {
        $this->middleware(TeamSA::class, ['except' => ['destroy',] ]);
        $this->middleware(SuperAdmin::class, ['only' => ['destroy',] ]);

        $this->programs = $programs;
        $this->depart = $depat;
        $this->qualification = $qualification;
        $this->programCourse = $programCourse;
        $this->levels = $levels;
        $this->courses = $courses;
    }
    public function index()
    {
        $program['programs'] = $this->programs->getAll();
        $program['departments'] = $this->depart->getAll();
        $program['qualifications'] = $this->qualification->getAll();
        return view('pages.academics.programs.index',$program);
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
    public function store(Program $req)
    {
        $data = $req->only(['code', 'name','departmentID','qualification_id','description']);
        $this->programs->create($data);

        return Qs::jsonStoreOk();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id = Qs::decodeHash($id);
        $myprogram['program'] = $someprograms = $this->programs->find($id);
        $myprogram['levels'] = $this->levels->getAll();
        $myprogram['newcourses'] = $this->courses->getAll();
        $myprogram['pcourses'] = $this->courses->getProgramCourses($id);
        /*
        $programs = DB::table('ac_programs')
            ->where('ac_programs.id', '=', $id)
            ->join('ac_programCourses', 'ac_programs.id', '=', 'ac_programCourses.programID')
            ->join('ac_course_levels', 'ac_course_levels.id', '=', 'ac_programCourses.level_id')
            ->join('ac_courses', 'ac_courses.id', '=', 'ac_programCourses.courseID')
            ->select(
                'ac_programs.id as program_id',
                'ac_course_levels.name as levelName',
                'ac_course_levels.id as level_id',
                'ac_courses.id as course_id',
                'ac_courses.name as course_name',
                'ac_courses.code'
            )
            ->orderBy('ac_programs.id')
            ->orderBy('ac_course_levels.id')
            ->get();

        $output = [];
        $currentProgramId = null;
        $currentLevelId = null;

        foreach ($programs as $program) {
            if ($program->program_id !== $currentProgramId) {
                $output[] = [
                    'program' => $program->program_id,
                    'levels' => []
                ];
                $currentProgramId = $program->program_id;
                $currentLevelId = null;
            }

            $programIndex = count($output) - 1;

            if ($program->level_id !== $currentLevelId) {
                $output[$programIndex]['levels'][] = [
                    'level' => $program->level_id,
                    'levelName' => $program->levelName,
                    'courses' => []
                ];
                $currentLevelId = $program->level_id;
            }

            $levelIndex = count($output[$programIndex]['levels']) - 1;
            $output[$programIndex]['levels'][$levelIndex]['courses'][] = [
                'course_id' => $program->course_id,
                'course_name' => $program->course_name,
                'code' => $program->code
            ];
        }*/
        $programs = DB::table('ac_programs')
            ->where('ac_programs.id', '=', $id)
            ->join('ac_programCourses', 'ac_programs.id', '=', 'ac_programCourses.programID')
            ->join('ac_course_levels', 'ac_course_levels.id', '=', 'ac_programCourses.level_id')
            ->join('ac_courses', 'ac_courses.id', '=', 'ac_programCourses.courseID')
            ->leftJoin('ac_prerequisites', 'ac_courses.id', '=', 'ac_prerequisites.courseID')
            ->leftJoin('ac_courses as prerequisites', 'ac_prerequisites.prerequisiteID', '=', 'prerequisites.id')
            ->select(
                'ac_programs.id as program_id',
                'ac_course_levels.name as levelName',
                'ac_course_levels.id as level_id',
                'ac_courses.id as course_id',
                'ac_courses.name as course_name',
                'ac_courses.code',
                'prerequisites.id as prerequisite_id',
                'prerequisites.code as prerequisite_code',
                'prerequisites.name as prerequisite_name'
            )
            ->orderBy('ac_programs.id')
            ->orderBy('ac_course_levels.id')
            ->get();
        $output = [];
        $currentProgramId = null;
        $currentLevelId = null;

        foreach ($programs as $program) {
            if ($program->program_id !== $currentProgramId) {
                $output[] = [
                    'program' => $program->program_id,
                    'levels' => []
                ];
                $currentProgramId = $program->program_id;
                $currentLevelId = null;
            }

            $programIndex = count($output) - 1;

            if ($program->level_id !== $currentLevelId) {
                $output[$programIndex]['levels'][] = [
                    'level' => $program->level_id,
                    'levelName' => $program->levelName,
                    'courses' => []
                ];
                $currentLevelId = $program->level_id;
            }

            $levelIndex = count($output[$programIndex]['levels']) - 1;

            // Check if the course has prerequisites
            $prerequisites = [];
            if ($program->prerequisite_id && $program->prerequisite_name) {
                $prerequisites[] = [
                    'prerequisite_id' => $program->prerequisite_id,
                    'prerequisite_name' => $program->prerequisite_name,
                    'prerequisite_code' => $program->prerequisite_code
                ];
            }

            $output[$programIndex]['levels'][$levelIndex]['courses'][] = [
                'course_id' => $program->course_id,
                'course_name' => $program->course_name,
                'code' => $program->code,
                'prerequisites' => $prerequisites
            ];
        }
        return !is_null($someprograms) ? view('pages.academics.programs.show',compact('myprogram', 'output'))
            : Qs::goWithDanger('pages.academics.programs.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $program['program'] = $programs = $this->programs->find($id);
        $program['departments'] = $this->depart->getAll();
        $program['qualifications'] = $this->qualification->getAll();
        return !is_null($programs) ? view('pages.academics.programs.edit', $program)
            : Qs::goWithDanger('pages.academics.programs.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProgramUpdate $req, string $id)
    {
        $data = $req->only(['code', 'name','departmentID','qualification_id','description']);
        $this->programs->update($id,$data);

        return Qs::jsonStoreOk();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->programs->find($id)->delete();
        return back()->with('flash_success', __('msg.delete_ok'));
    }
}
