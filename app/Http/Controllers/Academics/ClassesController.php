<?php

namespace App\Http\Controllers\Academics;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Http\Requests\Classes\Classes;
use App\Models\Academics\CourseLevels;
use App\Models\Academics\Programs;
use App\Models\Admissions\ProgramCourses;
use App\Repositories\Classesrepo;
use App\Support\ClassEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class ClassesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $classrepo;

    public function __construct(Classesrepo $classesrepo)
    {
        $this->middleware(TeamSA::class, ['except' => ['destroy',] ]);
        $this->middleware(SuperAdmin::class, ['only' => ['destroy',] ]);

        $this->classrepo = $classesrepo;
        // $this->user = $user;
    }
    public function index()
    {
        return view('pages.academics.classes.index');
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
    public function store(Classes $req)
    {
        $data = $req->only(['instructorID', 'courseID', 'academicPeriodID']);

        $validator = Validator::make($data, [
            'instructorID' => 'required',
            'courseID' => 'required',
            'academicPeriodID' => 'required',
        ]);

        if ($validator->fails()) {
            return false;
        }

        $instructorID = $req->input('instructorID');
        $courseID = $req->input('courseID');
        $academicPeriodID = $req->input('academicPeriodID');

        $exists = DB::table('ac_classes')
            ->where('instructorID', $instructorID)
            ->where('courseID', $courseID)
            ->where('academicPeriodID', $academicPeriodID)
            ->exists();

        if ($exists) {
            $validator->errors()->add('instructorID', 'The combination of instructorID, courseID, and academicPeriodID already exists.');
            return false;
        }

        $this->classrepo->create($data);
        return Qs::jsonStoreOk();
    }

    /**
     * Display the specified resource.
     */
    public function customShow(string $period,string $program)
    {
        $period = Qs::decodeHash($period);
        $program = Qs::decodeHash($program);
        $class = $this->prepareStudentListdata($period, $program);
        //dd($class);
        return view('pages.academics.classes.index',compact('class'));
    }
    public function show(string $id)
    {
        $id = Qs::decodeHash($id);
        $class = \App\Models\Academics\Classes::dataMiniSorted($id, 1);
        //dd($class);
        return view('pages.academics.classes.show',compact('class'));
        return \App\Models\Academics\Classes::dataMiniSorted($id, 1);
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
        $this->classrepo->find($id)->delete();
        return back()->with('flash_success', __('msg.delete_ok'));
    }
    public function prepareStudentListdata($academicPeriodID, $programID)
    {
        $classes              = [];

        $studentsUnPaid       = [];
        $knownCourseIDS       = [];
        $knownProgramCourses  = [];
        $studentsCount        = 0;
        $totalStudents        = 0;
        $students             = ClassEnrollment::viewFullProgramEnrollmentsByProgramID($programID, $academicPeriodID);
        $program              = Programs::dataMini($programID);



        foreach ($students as $student) {
            $progressionYears[] = $student['progression']['currentLevelName'];
        }

        if (!empty($progressionYears)) {
            $progressionYearsUnique = array_unique($progressionYears);
            sort($progressionYearsUnique);

            foreach ($progressionYearsUnique as $py) {

                foreach ($students as $thisStudent) {
                    if ($py == $thisStudent['progression']['currentLevelName']) {
                        $levelStudents[] = $thisStudent;
                    }
                }
                // find classes runing on this level
                $courseLevel         = CourseLevels::where('name', $py)->get()->first();

                if (!empty($courseLevel)) {
                    $knownProgramCourses = ProgramCourses::where('level_id', $courseLevel->id)->where('programID', $programID)->get();
                }

                // check for these classes created and running in the provided academic period
                $knownCourseIDS = [];
                foreach ($knownProgramCourses as $knownProgramCourse) {
                    $knownCourseIDS[] = $knownProgramCourse->courseID;
                }

                if ($knownCourseIDS) {
                    // check for the created classes under the provided academic period
                    $acClasses  =  \App\Models\Academics\Classes::whereIn('courseID', $knownCourseIDS)->where('academicPeriodID', $academicPeriodID)->get();
                    if ($acClasses) {
                        foreach ($acClasses as $acClass) {
                            $classes[] = \App\Models\Academics\Classes::dataMiniByProgram($acClass->id, 0, null, $programID);
                        }
                        $totalStudents  = count($levelStudents);
                    }

                    $studentsPaid         = [];
                    $studentsUnPaid       = [];
                    if ($levelStudents) {
                        foreach ($levelStudents as $levelStudent) {
                            if ($levelStudent['paymentPlanData'] && $levelStudent['paymentPlanData']['canAttendClass'] == 1) {
                                $studentsPaid[] = $levelStudent;
                            } else {
                                $studentsUnPaid[] = $levelStudent;
                            }
                        }
                    }
                    $classes = [];
                    $levels[] = [
                        'name'            => $py,
                        'students'        => $levelStudents,
                        'studentsPaid'    => $studentsPaid,
                        'studentsUnPaid'  => $studentsUnPaid,
                        'classes'         => $classes,
                        'total'           => $totalStudents,
                    ];

                    $classes = [];
                    $acClass = [];
                    unset($studentsPaid, $studentsUnPaid,$levelStudents);
                    unset($classes, $acClasses, $totalStudents);
                }
            }
            unset($levelStudents, $classes, $acClasses, $knownCourseIDS);
        }

        $studentsCount = count($students);


        $data = [
            'progressionYears'  => $progressionYearsUnique,
            'program'           => $program,
            'levels'            => $levels,
            'studentsCount'     => $studentsCount,
            'students'          => $students,
        ];

        return $data;
    }
}
