<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Models\Academics\AcademicPeriods;
use App\Models\Academics\Classes;
use App\Models\Admissions\UserProgram;
use App\Models\Enrollment;
use App\Repositories\PeriodFeesRepo;
use App\Repositories\StudentRecords;
use App\Traits\User\General;
use Illuminate\Http\Request;

class StudentProfileController extends Controller
{
    use General;
    /**
     * Display a listing of the resource.
     */
    protected $studentrecordsRepo;
    public function __construct(StudentRecords $studentrecordsRepo)
    {
        $this->middleware(TeamSA::class, ['except' => ['destroy',] ]);
        $this->middleware(SuperAdmin::class, ['only' => ['destroy',] ]);

        $this->studentrecordsRepo = $studentrecordsRepo;
        // $this->user = $user;
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
        dd(self::jsondataBasic($id));

        $data = $this->studentrecordsRepo->getAllwithOtherInfor($id);
        return view('pages.support_team.students.show',compact('data'));
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
    public function enrollmentData($id) // UserProgramID
    {
        $userProgram    = UserProgram::find($id);

        $user_id        = $userProgram->userID;

        return StudentProfileController::studentAcademicData($user_id);

        $programCourses = ProgramCourse::where('programID', $userProgram->programID)->get();
        foreach ($programCourses as $programCourse) {
            $courseIDs[] = $programCourse->courseID;
        }

        $classes = AcClass::wherein('courseID', $courseIDs)->get();
        foreach ($classes as $class) {
            $classIDs[] = $class->id;
        }
        $enrollments = Enrollment::whereIn('classID', $classIDs)->where('userID', $userProgram->userID)->get();
        foreach ($enrollments as $enrollment) {
            $attendedClassIDs[] = $enrollment->classID;
        }

        $myClasses = AcClass::wherein('id', $attendedClassIDs)->get()->unique('academicPeriodID');

        foreach ($myClasses as $rClass) {
            $apid    = $rClass->academicPeriodID;
            $apids[] = $apid;
        }

        $academicPeriods = AcademicPeriod::wherein('id', $apids)->orderBy('registrationDate', 'asc')->get();

        foreach ($academicPeriods as $ap) {
            $_ap = AcademicPeriod::data($ap->id, 0, $userProgram->userID);
            $_aps[] = $_ap;
        }

        if (empty($_aps)) {
            $_aps = [];
        }
        return $_aps;
    }
    public static function studentAcademicData($user_id)
    {

        $enrollments = Enrollment::where('userID', $user_id)->get();

        foreach ($enrollments as $enrollment) {
            $classID    = $enrollment->classID;
            $classIDs[] = $classID;
        }

        # Check though classes to find the academic period
        if (!empty($classIDs)) {
            $runningClasses = Classes::wherein('id', $classIDs)->get()->unique('academicPeriodID');

            foreach ($runningClasses as $rClass) {
                $apid    = $rClass->academicPeriodID;
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
