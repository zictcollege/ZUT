<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\RegistrationController;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Models\Academics\AcademicPeriods;
use App\Models\Academics\ClassAssessment;
use App\Models\Academics\Classes;
use App\Models\Academics\Courses;
use App\Models\Admissions\UserProgram;
use App\Models\Enrollment;
use App\Models\GradeBookImport;
use App\Models\User;
use App\Repositories\NationalitiesRepo;
use App\Repositories\PeriodFeesRepo;
use App\Repositories\StudentRecords;
use App\Repositories\UserRepo;
use App\Traits\User\Accounting;
use App\Traits\User\General;
use DB;
use Illuminate\Http\Request;

class StudentProfileController extends Controller
{
    use General;
    use Accounting;

    /**
     * Display a listing of the resource.
     */
    protected $studentrecordsRepo, $user;
    protected $nationalitiesRepo;

    public function __construct(UserRepo $user, StudentRecords $studentrecordsRepo)
    {
        //$this->middleware(TeamSA::class, ['except' => ['destroy',] ]);
        //$this->middleware(SuperAdmin::class, ['only' => ['destroy',] ]);
        //$this->middleware(TeamSA::class, ['except' => ['destroy',] ]);
        //$this->middleware(SuperAdmin::class, ['only' => ['destroy',] ]);

        $this->studentrecordsRepo = $studentrecordsRepo;
        $this->user = $user;
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id = Qs::decodeHash($id);
        $data = self::jsondata($id);
        $accounting = self::useraccounting($id);
        $userProgram = $data['enrolledPrograms'][0]['userProgramID'];
        //dd($accounting);
        $enrollments = StudentProfileController::studentAcademicData($id);
        //dd($enrollments);
        $dat = RegistrationController::classesAttended(16230, 4, 0, 0, 0, 73);
        //dd(RegistrationController::classesAttended(7727,19,0,0,0,73));//16230//7727=>19
        //$data = $this->studentrecordsRepo->getAllwithOtherInfor($id);
        return view('pages.support_team.students.show', compact('data', 'enrollments', 'accounting', 'dat'));
    }

    public function profile($user_id)
    {
        $user_id = Qs::decodeHash($user_id);
        if (!$user_id) {
            return back();
        }

        $data['user'] = $this->user->find($user_id);

        return view('pages.support_team.users.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function MyFinances()
    {
        $accounting = self::useraccounting(\Auth::user()->id);
        return view('pages.support_team.students.finances', compact('accounting'));
    }

    public function MyResults()
    {
        $exams = StudentProfileController::studentAcademicData(\Auth::user()->id);
        //dd($exams);
        return view('pages.support_team.students.exams.exam_results', compact('exams'));
    }

    public function ExamRegistration()
    {
        $user = self::jsondata(\Auth::user()->id);
        return view('pages.support_team.students.exams.exam_registration', compact('user'));
    }

    public function MyCAResults()
    {
        $user = \Auth::user();

        $userdata = self::jsondata($user->id);
        $aid = $userdata['currentAPID'];
        $pid = $userdata['currentProgram']['id'];
        $studentid = $user->student_id;
        //dd($level);
        $grouped = DB::table('ac_gradebook_imports')
            ->join('ac_assessmentTypes', 'ac_assessmentTypes.id', '=', 'ac_gradebook_imports.assessmentID')
            ->join('ac_programCourses', 'ac_gradebook_imports.programID', '=', 'ac_programCourses.programID')
            ->join('ac_course_levels', 'ac_programCourses.level_id', '=', 'ac_course_levels.id')
            ->join('ac_academicPeriods', 'ac_academicPeriods.id', '=', 'ac_gradebook_imports.academicPeriodID')
            ->join('ac_programs', 'ac_programs.id', '=', 'ac_gradebook_imports.programID')
            ->select(
                'ac_gradebook_imports.academicPeriodID as academicPeriod',
                'ac_academicPeriods.code as academicPeriodcode',
                'ac_gradebook_imports.programID as program_id',
                'ac_programs.name as program_name',
                'ac_programs.code as program_code',
                'ac_gradebook_imports.code as code',
                'ac_gradebook_imports.title as title',
                'ac_gradebook_imports.total as total',
                'ac_gradebook_imports.id as id',
                'ac_gradebook_imports.assessmentID as assessmentID',
                'ac_assessmentTypes.name as assessment_name',
                'ac_gradebook_imports.key as key',
                'ac_gradebook_imports.status as status',
                'ac_course_levels.id as level_id',
                'ac_course_levels.name as level_name'
            )
            ->where('ac_gradebook_imports.academicPeriodID', '=', $aid)
            ->where('ac_gradebook_imports.programID', '=', $pid)
            ->where('ac_gradebook_imports.studentID', '=', $studentid)
            ->where('ac_programCourses.programID', '=', $pid)
            ->get();
        $grouped = DB::table('ac_gradebook_imports')
            ->join('ac_assessmentTypes', 'ac_assessmentTypes.id', '=', 'ac_gradebook_imports.assessmentID')
            ->join('ac_programCourses', 'ac_gradebook_imports.programID', '=', 'ac_programCourses.programID')
            ->join('ac_course_levels', 'ac_programCourses.level_id', '=', 'ac_course_levels.id')
            ->join('ac_academicPeriods', 'ac_academicPeriods.id', '=', 'ac_gradebook_imports.academicPeriodID')
            ->join('ac_programs', 'ac_programs.id', '=', 'ac_gradebook_imports.programID')
            ->select(
                'ac_gradebook_imports.academicPeriodID as academicPeriod',
                'ac_academicPeriods.code as academicPeriodcode',
                'ac_gradebook_imports.programID as program_id',
                'ac_programs.name as program_name',
                'ac_programs.code as program_code',
                'ac_gradebook_imports.code as code',
                'ac_gradebook_imports.title as title',
                'ac_gradebook_imports.total as total',
                'ac_gradebook_imports.id as id',
                'ac_gradebook_imports.assessmentID as assessmentID',
                'ac_assessmentTypes.name as assessment_name',
                'ac_gradebook_imports.key as key',
                'ac_gradebook_imports.status as status',
                'ac_course_levels.id as level_id',
                'ac_course_levels.name as level_name',
                'ac_gradebook_imports.studentID as student_id', // Add this line to select student ID
            )
            //->where('ac_gradebook_imports.academicPeriodID', '=', $aid)
            ->where('ac_gradebook_imports.programID', '=', $pid)
            ->where('ac_gradebook_imports.studentID', '=', $studentid)
            ->where('ac_programCourses.programID', '=', $pid)
            ->groupBy(
                'ac_gradebook_imports.academicPeriodID',
                'ac_academicPeriods.code',
                'ac_gradebook_imports.programID',
                'ac_programs.name',
                'ac_programs.code',
                'ac_gradebook_imports.code',
                'ac_gradebook_imports.title',
                'ac_gradebook_imports.total',
                'ac_gradebook_imports.id',
                'ac_gradebook_imports.assessmentID',
                'ac_assessmentTypes.name',
                'ac_gradebook_imports.key',
                'ac_gradebook_imports.status',
                'ac_course_levels.id',
                'ac_course_levels.name',
                'ac_gradebook_imports.studentID' // Group by student ID
            )
            ->get();

        $results = [];

        foreach ($grouped as $row) {
            $academicPeriod = $row->academicPeriod;
            $programId = $row->program_id;
            $academic = [
                'academic' => $row->academicPeriod,
                'program' => $row->program_id,
            ];

            $progression = self::checkProgression($user->id, $row->program_id);
            $yearOfStudy = $progression['currentLevelName'];

            // Check if the student's year of study matches the level on request
            if ($yearOfStudy != $row->level_name) {
                continue; // Skip this student
            }

            if (!isset($results[$academic['academic']])) {
                $results[$academic['academic']] = [
                    'academic' => $row->academicPeriod,
                    'program' => $row->program_id,
                    'program_name' => $row->program_name,
                    'level_name' => $yearOfStudy,
                    'program_code' => $row->program_code,
                    'academicperiodname' => $row->academicPeriodcode,
                    'students' => [],
                ];
            }

            $studentId = $row->student_id;

            if (!isset($results[$academic['academic']]['students'][$studentId])) {
                $results[$academic['academic']]['students'][$studentId] = [
                    'student_id' => $studentId,
                    'courses' => [],
                    'classes' => \App\Models\Academics\AcademicPeriods::myclasses($user->id, $row->academicPeriod),
                ];
            }

            $courseCode = $row->code;

            if (!isset($results[$academic['academic']]['students'][$studentId]['courses'][$courseCode])) {
                $course = [
                    'code' => $row->code,
                    'title' => $row->title,
                    'CA' => 0,
                    'outOf' => $this->getTotalAssess($studentId, $row->program_id, $row->academicPeriod, $row->code),
                    'assessments' => [],
                ];

                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode] = $course;
            }

            // Check if the course code is already present in the classes array and remove it
            foreach ($results[$academic['academic']]['students'][$studentId]['classes'] as $index => $class) {
                if (isset($class['course_code']) && $class['course_code'] == $row->code) {
                    unset($results[$academic['academic']]['students'][$studentId]['classes'][$index]);
                } else {
                    $results[$academic['academic']]['students'][$studentId]['courses'][$class['course_code']] = [
                        'code' => $class['course_code'],
                        'title' => $class['course_name'],
                        'CA' => 0,
                        'outOf' => $this->getTotalAssess($studentId, $row->program_id, $row->academicPeriod, $row->code),
                        'assessments' => [],
                    ];
                }
            }

            $assess = $row->assessment_name;

            if (!isset($results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['assessments'][$assess])) {
                $assessments = [
                    'total' => $row->total,
                    'id' => $row->id,
                    'assessID' => $row->assessmentID,
                    'assessment_name' => $row->assessment_name,
                    'key' => $row->key,
                    'status' => $row->status,
                ];

                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['CA'] += $row->total;

                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['assessments'][$assess] = $assessments;
            }
        }


        //dd($results);
        return view('pages.support_team.students.exams.ca_results', compact('results'));
    }

    public function getTotalAssess($student_id, $programID, $academicPeriodID, $code)
    {
        // check if user has registered for this academic period.
        $user = User::where('student_id', $student_id)->get()->first();
        if ($user) {
            $lastEnrollment = Enrollment::where('userID', $user->id)->get()->last();

            if ($lastEnrollment) {

                $lastEnrolledClass = Classes::where('id', $lastEnrollment->classID)->get()->first();
                if ($lastEnrolledClass) {
                    # Add results to gradebook
                    $course = Courses::where('code', $code)->get()->first();
                    if ($course) {

                        $class = Classes::where('courseID', $course->id)->where('academicPeriodID', $academicPeriodID)->get()->first();
                        // check if class has assesments
                        $assessments = ClassAssessment::where('classID', $class->id)->get();
                        $totalAssessmentScore = 0;

                        foreach ($assessments as $assessment) {
                            if ($assessment->assesmentID != 1){
                                $totalAssessmentScore += $assessment->total;
                            }
                        }
                        return $totalAssessmentScore;
                    }
                }
            }
        }
    }

    public function GetStudentCAResultsl()
    {
        $user = Auth::user();

        $userdata = General::jsondata($user->id);
        dd($userdata);
        $pid = $request->query('pid');
        $level = $request->query('level');
        $aid = Qs::decodeHash($aid);
        $pid = Qs::decodeHash($pid);
        $level = Qs::decodeHash($level);
        //dd($level);
        $grouped = DB::table('ac_gradebook_imports')
            ->join('users', 'users.student_id', '=', 'ac_gradebook_imports.studentID')
            ->join('ac_assessmentTypes', 'ac_assessmentTypes.id', '=', 'ac_gradebook_imports.assessmentID')
            ->join('ac_programCourses', 'ac_gradebook_imports.programID', '=', 'ac_programCourses.programID')
            ->join('ac_course_levels', 'ac_programCourses.level_id', '=', 'ac_course_levels.id')
            ->join('ac_academicPeriods', 'ac_academicPeriods.id', '=', 'ac_gradebook_imports.academicPeriodID')
            ->join('ac_programs', 'ac_programs.id', '=', 'ac_gradebook_imports.programID')
            ->join('ac_classes', 'ac_classes.academicPeriodID', '=', 'ac_gradebook_imports.academicPeriodID')
            ->join('ac_classAssesments', 'ac_classes.id', '=', 'ac_classAssesments.classID')
            ->select(
                'ac_gradebook_imports.academicPeriodID as academicPeriod',
                'ac_academicPeriods.code as academicPeriodcode',
                'ac_gradebook_imports.programID as program_id',
                'ac_programs.name as program_name',
                'ac_programs.code as program_code',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.id as userID',
                'ac_gradebook_imports.code as code',
                'ac_gradebook_imports.title as title',
                'ac_gradebook_imports.total as total',
                'ac_gradebook_imports.studentID as student_id',
                'ac_gradebook_imports.id as id',
                'ac_gradebook_imports.assessmentID as assessmentID',
                'ac_assessmentTypes.name as assessment_name',
                'ac_gradebook_imports.key as key',
                'ac_gradebook_imports.status as status',
                'ac_classAssesments.total as outof',
                'ac_course_levels.id as level_id',
                'ac_course_levels.name as level_name'
            )
            ->where('ac_gradebook_imports.academicPeriodID', '=', $aid)
            ->where('ac_gradebook_imports.programID', '=', $pid)
            ->where('ac_programCourses.level_id', '=', $level)
            ->where('ac_programCourses.programID', '=', $pid)
            ->get();

        $results = [];
        foreach ($grouped as $row) {

            $academicPeriod = $row->academicPeriod;
            $programId = $row->program_id;
            $academic = [
                'academic' => $row->academicPeriod,
                'program' => $row->program_id,
            ];
            if (!isset($results[$academic['academic']])) {
                $results[$academic['academic']] = [
                    'academic' => $row->academicPeriod,
                    'program' => $row->program_id,
                    'program_name' => $row->program_name,
                    'level_name' => $row->level_name,
                    'program_code' => $row->program_code,
                    'academicperiodname' => $row->academicPeriodcode,
                    'students' => [],
                ];
            }
            $progression = self::checkProgression($row->userID, $row->program_id);
            $yearOfStudy = $progression['currentLevelName'];

            // Check if the student's year of study matches the level on request
            if ($yearOfStudy != $row->level_name) {
                continue; // Skip this student
            }
            $studentId = $row->student_id;
            if (!isset($results[$academic['academic']]['students'][$studentId])) {
                $results[$academic['academic']]['students'][$studentId] = [
                    'name' => $row->first_name . ' ' . $row->last_name,
                    'student_id' => $studentId,
                    'level' => $yearOfStudy,
                    'courses' => [],
                    'classes' => \App\Models\Academics\AcademicPeriods::myclasses($row->userID, $row->academicPeriod)
                ];
            }

            $courseCode = $row->code;
            if (!isset($results[$academic['academic']]['students'][$studentId]['courses'][$courseCode])) {
                $course = [
                    'code' => $row->code,
                    'title' => $row->title,
                    'CA' => 0,
                    'total' => 0,
                    'outOf' => 0,
                    'assessments' => [],
                ];

                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode] = $course;
            }
            // Check if the course code is already present in the classes array and remove it
            foreach ($results[$academic['academic']]['students'][$studentId]['classes'] as $index => $class) {
                if (isset($class['course_code']) && $class['course_code'] == $row->code) {
                    unset($results[$academic['academic']]['students'][$studentId]['classes'][$index]);
                } else {
                    $results[$academic['academic']]['students'][$studentId]['courses'][$class['course_code']] = [
                        'code' => $class['course_code'],
                        'title' => $class['course_name'],
                        'CA' => 0,
                        'total' => 0,
                        'outOf' => 0,
                        'assessments' => [],
                    ];
                }
            }


            $assess = $row->assessment_name;
            if (!isset($results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['assessments'][$assess])) {
                $assessments = [
                    'total' => $row->total,
                    'id' => $row->id,
                    'assessID' => $row->assessmentID,
                    'assessment_name' => $row->assessment_name,
                    'key' => $row->key,
                    'status' => $row->status,
                ];
                //$results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['CA'] += $row->total;
                if (!$row->assessID == 1) {
                    $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['outOf'] += $row->outof;
                }
                //$results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['total'] += $row->total;
                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['total'] += $row->total;

                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['assessments'][$assess] = $assessments;
            }
        }
        foreach ($results as &$academicPeriodData) {
            foreach ($academicPeriodData['students'] as &$studentData) {
                foreach ($studentData['courses'] as &$courseData) {
                    $totalScore = $courseData['CA'];
                    $courseData['grade'] = $this->calculateGrade($totalScore);
                    $studentData['commentData'] = $this->calculateComment($studentData['courses']);
                }
            }
        }
//        foreach ($results as &$academicPeriodData) {
//            foreach ($academicPeriodData['students'] as &$studentData) {
//                foreach ($studentData['courses'] as &$courseData) {
//                    $totalScore = $courseData['CA'];
//                    $courseData['grade'] = $this->calculateGrade($totalScore);
//                    $courseData['commentData'] = $this->calculateComment($courseData['courses']);
//                }
//            }
//        }


        //return $results;
        //$results = array_values($results);

        dd($results);
        return view('pages.academics.class_assessments.results_review_board', compact('results'));
        //return view('pages.academics.class_assessments.update_marks', compact('results'));
    }

    public function calculateGrade($total)
    {
        // Define your grade thresholds and corresponding values here
        if ($total == 0) {
            return 'Not Examined';
        }
        else if ($total == -1) {
            return 'Exempted';
        }
        else if($total == -2) {
            return 'Withdrew with Permission';
        }
        else if ($total == -3) {
            return 'Disqualified';
        } else if ($total == 0) {
            return 'NE';
        } else if ($total >= 1 && $total <= 39) {
            return 'D';
        } else if ($total >= 40 && $total <= 49) {
            return 'D+';
        } else if ($total >= 50 && $total <= 55) {
            return 'C';
        } else if ($total >= 56 && $total <= 61) {
            return 'C+';
        } else if ($total >= 62 && $total <= 67) {
            return 'B';
        } else if ($total >= 68 && $total <= 75) {
            return 'B+';
        } else if ($total >= 76 && $total <= 85) {
            return 'A';
        } else if ($total >= 86 && $total <= 100) {
            return 'A+';
        }
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

    public static function studentAcademicData($user_id)
    {

        $enrollments = Enrollment::where('userID', $user_id)->get();

        foreach ($enrollments as $enrollment) {
            $classID = $enrollment->classID;
            $classIDs[] = $classID;
        }

        # Check though classes to find the academic period
        if (!empty($classIDs)) {
            $runningClasses = Classes::wherein('id', $classIDs)->get()->unique('academicPeriodID');

            foreach ($runningClasses as $rClass) {
                $apid = $rClass->academicPeriodID;
                $apids[] = $apid;
            }

            $academicPeriods = AcademicPeriods::wherein('id', $apids)->orderBy('registrationDate', 'asc')->get();

            foreach ($academicPeriods as $ap) {
                $_ap = AcademicPeriods::data($ap->id, 0, $user_id);
                $_aps[] = $_ap;
            }
        }
        if (empty($_aps)) {
            $_aps = [];
        }

        return $_aps;
    }
}
