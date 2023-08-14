<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\Student;
use App\Models\Academics\AcademicPeriods;
use App\Models\Academics\Classes;
use App\Models\Admissions\UserProgram;
use App\Models\Admissions\UserStudyModes;
use App\Models\Enrollment;
use App\Repositories\StudentRecords;
use App\Support\General;
use App\Support\Progression;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller

{
    /**
     * Display a listing of the resource.
     */
    protected $studentrecordsRepo;

    public function __construct(StudentRecords $studentrecordsRepo)
    {
        $this->middleware(Student::class, ['except' => ['destroy',] ]);

        $this->studentrecordsRepo = $studentrecordsRepo;
        // $this->user = $user;
    }

    public function index()
    {
        $id = Auth::user();
        $data = $this->studentrecordsRepo->getAllwithOtherInfor($id->id);
        $programID = 4;
        $intakeID = 1;
        $studymodeID = 1;
        $typeID = 2;
        $today = Carbon::today()->toDateString();

        $courses = DB::table('ac_academicPeriods')
            ->join('ac_classes', 'ac_academicPeriods.id', '=', 'ac_classes.academicPeriodID')
            //->join('ac_enrollments', 'ac_enrollments.classID', '=', 'ac_classes.id')
            ->join('ac_courses', 'ac_classes.courseID', '=', 'ac_courses.id')
            ->join('ac_programCourses', 'ac_courses.id', '=', 'ac_programCourses.courseID')
            ->join('users', 'ac_classes.instructorID', '=', 'users.id')
            ->join('ac_programs', 'ac_programCourses.programID', '=', 'ac_programs.id')
            ->where('ac_programs.id', $programID)
            //->where('ac_programCourses.level_id', 1)
            ->where('ac_academicPeriods.intakeID', $intakeID)
            ->where('ac_academicPeriods.studymodeIDAllowed', $studymodeID)
            //->where('ac_academicPeriods.type', $typeID)
           // ->where('ac_enrollments.userID','=', 16230)
            ->where('ac_academicPeriods.acEndDate', '>=', $today)
            //->where('ac_academicPeriods.id', '=', 63)
            ->select('ac_academicPeriods.id','ac_academicPeriods.code')
            ->distinct()
            ->get();
        dd($courses);
        $data = DB::table('ac_classAssesments')
            ->join('ac_gradeBooks', 'ac_classAssesments.id', '=', 'ac_gradeBooks.classAssessmentID')
            ->join('ac_classes', 'ac_classAssesments.classID', '=', 'ac_classes.id')
            ->join('ac_courses', 'ac_classes.courseID', '=', 'ac_courses.id')
            ->where('ac_gradeBooks.userID','=',16230)
           // ->where('ac_gradeBooks.grade','>',50)
            ->select('ac_classAssesments.*', 'ac_gradeBooks.*', 'ac_classes.*', 'ac_courses.*')
            ->get();
        dd(RegistrationController::classesAttended(16230,4,0,0,0,73));
        $courses = DB::table('ac_academicPeriods')
            ->join('ac_classes', 'ac_academicPeriods.id', '=', 'ac_classes.academicPeriodID')
            ->join('ac_courses', 'ac_classes.courseID', '=', 'ac_courses.id')
            ->join('ac_programCourses', 'ac_courses.id', '=', 'ac_programCourses.courseID')
            ->join('users', 'ac_classes.instructorID', '=', 'users.id')
            ->join('ac_programs', 'ac_programCourses.programID', '=', 'ac_programs.id')
            ->where('ac_programs.id', $programID)
            ->where('ac_programCourses.level_id', 1)
            ->where('ac_academicPeriods.intakeID', $intakeID)
            ->where('ac_academicPeriods.studymodeIDAllowed', $studymodeID)
            ->where('ac_academicPeriods.type', $typeID)
            ->where('ac_academicPeriods.acEndDate', '>=', $today)
            //->where('ac_academicPeriods.id', '=', 63)
            ->select('ac_courses.code', 'ac_courses.name','ac_academicPeriods.id','ac_classes.id')
            ->get();
        foreach ($courses as $course) {
            $enrollment = DB::table('ac_enrollments')
                ->where('userID', $id->id)
                ->where('classID', $course->id)
                ->first();
            if ($enrollment) {
                $course->enrolled = true;
            } else {
                $course->enrolled = false;
            }
        }
        return view('pages.support_team.students.show', compact('data'));
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
    public static function score($marks)
    {
        //  $marks = 0;
        $grade = '';
        //$marks =  $mark[1];

        // Grading 1 = UNZA;
        // Grading 2 = Genral

        foreach ($marks as $mark) {
            if ($mark->gradeType == 0) {
                if ($mark->grade == 0) {
                    $mark->gradevv = 'Not Examined';
                }
                if ($mark->grade == -1) {
                    $mark->gradevv = 'Exempted';
                }
                if ($mark->grade == -2) {
                    $mark->gradev = 'Withdrew with Permission';
                }
                if ($mark->grade == -3) {
                    $mark->gradev = 'Disqualified';
                } else if ($mark->grade == 0) {
                    $mark->gradev = 'NE';
                } else if ($mark->grade >= 1 && $mark->grade <= 39) {
                    $mark->gradev = 'D';
                } else if ($mark->grade >= 40 && $mark->grade <= 49) {
                    $mark->gradev = 'D+';
                } else if ($mark->grade >= 50 && $mark->grade <= 55) {
                    $mark->gradev = 'C';
                } else if ($mark->grade >= 56 && $mark->grade <= 61) {
                    $mark->gradev = 'C+';
                } else if ($mark->grade >= 62 && $mark->grade <= 67) {
                    $mark->gradev = 'B';
                } else if ($mark->grade >= 68 && $mark->grade <= 75) {
                    $mark->gradev = 'B+';
                } else if ($mark->grade >= 76 && $mark->grade <= 85) {
                    $mark->gradev = 'A';
                } else if ($mark->grade >= 86 && $mark->grade <= 100) {
                    $mark->gradev = 'A+';
                }
            } else if ($mark->gradeType == 1) {
                if ($mark->grade == 0) {
                    $mark->gradev = 'Not Examined';
                }
                if ($mark->grade == -1) {
                    $mark->gradev = 'Exempted';
                }
                if ($mark->grade == -2) {
                    $mark->gradev = 'Withdrew with Permission';
                }
                if ($mark->grade == -3) {
                    $mark->gradev = 'Disqualified';
                }
                if ($mark->grade == -4) {
                    $mark->gradev = 'Deferred';
                }
                if ($mark->grade == -5) {
                    $mark->gradev = 'Changed Mode of Study';
                } else if ($mark->grade == 0) {
                    $mark->gradev = 'NE';
                } else if ($mark->grade >= 1 && $mark->grade <= 29) {
                    $mark->gradev = 'D';
                } else if ($mark->grade >= 30 && $mark->grade <= 39) {
                    $mark->gradev = 'D+';
                } else if ($mark->grade >= 40 && $mark->grade <= 45) {
                    $mark->gradev = 'C';
                } else if ($mark->grade >= 46 && $mark->grade <= 55) {
                    $mark->gradev = 'C+';
                } else if ($mark->grade >= 56 && $mark->grade <= 65) {
                    $mark->gradev = 'B';
                } else if ($mark->grade >= 66 && $mark->grade <= 75) {
                    $mark->gradev = 'B+';
                } else if ($mark->grade >= 76 && $mark->grade <= 85) {
                    $mark->gradev = 'A';
                } else if ($mark->grade >= 86 && $mark->grade <= 100) {
                    $mark->gradev = 'A+';
                }
            }
        }
        return $marks;

    }
    //register
    public function classesAttended($userID,$program,$intake,$studyMode,$typeID,$academic)
    //public function classesAttended(Request $request)
    {



        /* try {*/

        # Find if student is degree student and add the value to the academic period data function
        //$userProgram = UserProgram::where('programID', request('programID'))->where('userID', request('userid'))->get()->first();
        $userProgram = UserProgram::where('programID', $program)->where('userID', $userID)->get()->first();

        $certification = $userProgram->program->qualification->name;

        switch ($certification) {
            case 'Degree':
                $is_degree_student = 1;
                break;
            default:
                $is_degree_student = 0;
                break;
        }


        $enrollments       = Enrollment::where('userID',$userID)->get();



        if ($certification == 'Degree' || $certification == 'Diploma') {


           // $academicPeriod    = AcademicPeriods::data(request('academicPeriodID'), 0, request('userid'), $is_degree_student);
            $academicPeriod    = AcademicPeriods::data($academic, 0, $userID, $is_degree_student);
        } else {
            $academicPeriod    = AcademicPeriods::data($academic, 1, $userID, $is_degree_student, $program);
        }
        $classes = [];
        foreach ($enrollments as $enrollment) {
            $class         = Classes::data($enrollment->classID, 0, $userID);
            $classes[]     = $class;
        }

        //$previousEnrollment = Enrollment::where('userID', request('userid'))->get()->last();
        $previousEnrollment = Enrollment::where('userID', $userID)->get()->last();
        $comments           = Progression::comments($userID, $previousEnrollment->class->academicPeriod->id);
        $progression        = Progression::checkProgression($comments);


        $oldAcademicPeriodIds = [];
        # Find the last academic period that the student registered for and return null if the selected has been registered for
        foreach ($enrollments as $enrollment) {
            $oldAcademicPeriodIds[] = $enrollment->class->academicPeriod->id;
        }
        $has_registered =0;

        foreach ($oldAcademicPeriodIds as $oldAcademicPeriodID) {


            $timeFactor = AcademicPeriods::find($academic);

            # $addTen= strtotime($timeFactor->lateRegistrationDate)+10;



            #add 10 days to late registration

            $addTen = date('Y-m-d', strtotime($timeFactor->lateRegistrationDate. ' + 10 days'));

            if($addTen < date("Y-m-d")){
                $has_registered = 5;
            }else {
                if ($oldAcademicPeriodID == $academic) {
                    # User has registered for this academic period before
                    $has_registered = 1;
                } else {
                    $has_registered = 0;
                }

            }

        }
        # Find next classes
        $nextClasses   = AcademicPeriods::nextClasses($userID, $academic);

        $failedClasses = $comments['coursesFailed'];
        $passedClasses = $comments['coursesPassed'];



        $userMode           = UserStudyModes::where('userID', $userID)->get()->last();
        $semesterStatus     = General::checkStudentsSemester($userID);



        //$academicPeriod['next_classes'] = Progression::classCalcualtor($nextClasses, $failedClasses, $passedClasses);

        if((int)$progression['progression'] > 2){


            // $academicPeriod['next_classes'] = Progression:: classCheck(request('academicPeriodID'),$failedClasses, $passedClasses);

            $academicPeriod['next_classes'] = array_merge($failedClasses, $passedClasses);


            //$academicPeriod['next_classes'] = Progression::classCalcualator($nextClasses, $failedClasses, $passedClasses);

            return $data = [
                'selectedAcademicPeriod' => $academicPeriod,
                'classes'                => $classes,
                'resultsdata'            => $comments,
                'progression'            => $progression,
                'has_registered'         => $has_registered,
                'covid_affected'         => 0,
                'studyModeID'            => $userMode->studyModeID,
                'semesterStatus'         => $semesterStatus,
            ];

        }else{
            $academicPeriod['next_classes'] = Progression::classCalcualtor($nextClasses, $failedClasses, $passedClasses);

            return $data = [
                'selectedAcademicPeriod' => $academicPeriod,
                'classes'                => $classes,
                'resultsdata'            => $comments,
                'progression'            => $progression,
                'has_registered'         => $has_registered,
                'covid_affected'         => 0,
                'studyModeID'            => $userMode->studyModeID,
                'semesterStatus'         => $semesterStatus,
            ];

        }
    }
}
