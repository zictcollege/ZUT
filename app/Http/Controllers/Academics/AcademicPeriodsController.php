<?php

namespace App\Http\Controllers\Academics;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Http\Requests\AcademicPeriod\AcademicPeriodCreate;
use App\Http\Requests\AcademicPeriod\AcademicPeriodUpdate;
use App\Http\Requests\Fees\Fees;
use App\Http\Requests\PeriodFees\PeriodFees;
use App\Repositories\Academicperiods;
use App\Repositories\Classesrepo;
use App\Repositories\CoursesRepo;
use App\Repositories\FeesRepo;
use App\Repositories\PeriodFeesRepo;
use App\Repositories\PeriodTypeRepo;
use App\Repositories\StudyModeRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicPeriodsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $periodtypes,$studymode,$academic,$feesRepo,$fees,$classrepo,$coursesRepo;

    public function __construct(PeriodTypeRepo $periodtypes, StudyModeRepo $studymode,Academicperiods $academic,
                                PeriodFeesRepo $feesRepo,FeesRepo $fees,Classesrepo $classesrepo,CoursesRepo $coursesRepo)
    {
        $this->middleware(TeamSA::class, ['except' => ['destroy',] ]);
        $this->middleware(SuperAdmin::class, ['only' => ['destroy',] ]);

        $this->periodtypes = $periodtypes;
        $this->studymode = $studymode;
        $this->academic = $academic;
        $this->feesRepo = $feesRepo;
        $this->fees = $fees;
        $this->classrepo = $classesrepo;
        $this->coursesRepo = $coursesRepo;
       // $this->user = $user;
    }
    public function index()
    {
        $data['periodstypes'] = $this->periodtypes->getAll();
        $data['studymode'] = $this->studymode->getAll();
        $data['acperiods'] = $this->academic->getAll();
        $data['open'] = $this->academic->getAllopen();
        $data['closed'] = $this->academic->getAllClosed();
        return view('pages.academics.academic_periods.create',$data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AcademicPeriodCreate $req)
    {
        $data = $req->only(['code', 'registrationDate', 'lateRegistrationDate', 'acStartDate', 'acEndDate', 'periodID', 'resultsThreshold', 'registrationThreshold', 'type', 'examSlipThreshold', 'studyModeIDAllowed']);
        $data['registrationDate'] = date('Y-m-d', strtotime($data['registrationDate']));
        $data['lateRegistrationDate'] = date('Y-m-d', strtotime($data['lateRegistrationDate']));
        $data['acStartDate'] = date('Y-m-d', strtotime($data['acStartDate']));
        $data['acEndDate'] = date('Y-m-d', strtotime($data['acEndDate']));
        $this->academic->create($data);

        return Qs::jsonStoreOk();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id = Qs::decodeHash($id);
        $period['period'] = $academic = $this->academic->find($id);
        $period['fees'] = $this->fees->getAll();
        $period['courses'] = $this->coursesRepo->getAll();
        $period['instructors'] = DB::table('users')->where('user_type','=','instructor')->get();
         $ap = \App\Models\Academics\AcademicPeriods::data($id);
         //dd($ap);
       // $period['periodFees'] = $this->feesRepo->getPeriodFees($id);
        //dd($period['periodFees']);
        $period['periodFees'] = DB::table('ac_academicPeriodFees')
            ->where('ac_academicPeriodFees.academicPeriodID', $id)
            ->join('ac_academicPeriods', 'ac_academicPeriods.id', '=', 'ac_academicPeriodFees.academicPeriodID')
            ->join('ac_fees', 'ac_fees.id', '=', 'ac_academicPeriodFees.feeID')
            //->join('ac_studyModes', 'ac_studyModes.id', '=', 'ac_academicPeriodFees.studyModeID')
            ->select(
                'ac_academicPeriodFees.amount AS amount',
                'ac_academicPeriodFees.id AS id',
                'ac_fees.id as fee_id',
                'ac_fees.name AS fee_name',
                'ac_academicPeriodFees.crf AS repeat',
                'ac_academicPeriodFees.p_f as normal',
                'ac_academicPeriodFees.once_off as once_off',
                'ac_academicPeriods.id as ac_id',
                'ac_academicPeriods.code AS code'
            )->get();


        $academics = DB::table('ac_academicPeriods')
            ->where('ac_academicPeriods.id', $id)
            ->join('ac_classes', 'ac_academicPeriods.id', '=', 'ac_classes.academicPeriodID')
            ->join('ac_courses', 'ac_classes.courseID', '=', 'ac_courses.id')
            ->join('ac_programCourses', 'ac_courses.id', '=', 'ac_programCourses.courseID')
            ->join('users', 'ac_classes.instructorID', '=', 'users.id')
            ->join('ac_programs', 'ac_programCourses.programID', '=', 'ac_programs.id')
            ->join('ac_course_levels', 'ac_programCourses.level_id', '=', 'ac_course_levels.id')
            ->leftJoin('ac_prerequisites', 'ac_courses.id', '=', 'ac_prerequisites.courseID')
            ->leftJoin('ac_courses as prerequisites', 'ac_prerequisites.prerequisiteID', '=', 'prerequisites.id')
            ->select(
                'ac_academicPeriods.code AS academic_period',
                'ac_classes.id as class_id',
                'ac_programs.id as program_id',
                'ac_programs.name AS program',
                'ac_course_levels.name AS level',
                'ac_course_levels.id as level_id',
                'ac_courses.id as course_id',
                'ac_courses.name AS course',
                'ac_courses.code',
                'prerequisites.id as prerequisite_id',
                'prerequisites.code as prerequisite_code',
                'prerequisites.name as prerequisite_name',
                'users.first_name as first_name',
                'users.last_name as last_name'
            )
            ->orderBy('academic_period')
            ->orderBy('program')
            ->orderBy('level')
            ->get();

        $output = [];
        $currentProgramId = null;
        $currentLevelId = null;

        foreach ($academics as $academic) {
            if ($academic->program_id !== $currentProgramId) {
                $output[] = [
                    'program' => $academic->program,
                    'program_id' => $academic->program_id,
                    'levels' => []
                ];
                $currentProgramId = $academic->program_id;
                $currentLevelId = null;
            }

            $programIndex = count($output) - 1;

            if ($academic->level !== $currentLevelId) {
                $output[$programIndex]['levels'][] = [
                    'level' => $academic->level,
                    'level_id'=>$academic->level_id,
                    'courses' => []
                ];
                $currentLevelId = $academic->level;
            }

            $levelIndex = count($output[$programIndex]['levels']) - 1;

            // Check if the course has prerequisites
            $prerequisites = [];
            if ($academic->prerequisite_id && $academic->prerequisite_name) {
                $prerequisites[] = [
                    'prerequisite_id' => $academic->prerequisite_id,
                    'prerequisite_name' => $academic->prerequisite_name,
                    'prerequisite_code' => $academic->prerequisite_code
                ];
            }

            $output[$programIndex]['levels'][$levelIndex]['courses'][] = [
                'course_id' => $academic->course_id,
                'course_name' => $academic->course,
                'code' => $academic->code,
                'class_id' => $academic->class_id,
                'instructor' => $academic->first_name.' '.$academic->last_name,
                'prerequisites' => $prerequisites
            ];
        }

        //dd($output);
        return !is_null($academic ) ? view('pages.academics.academic_periods.show', compact('period','output'))
            : Qs::goWithDanger('pages.academics.academic_periods.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $id = Qs::decodeHash($id);
        $period['period'] = $academic = $this->academic->find($id);
        $data['periodstypes'] = $this->periodtypes->getAll();
        $data['studymode'] = $this->studymode->getAll();
        return !is_null($academic ) ? view('pages.academics.academic_periods.edit', $period,$data)
            : Qs::goWithDanger('pages.academics.academic_periods.index',$data);
    }
    public function addAcfees(PeriodFees $req){
        $data = $req->only(['academicPeriodID', 'feeID', 'amount','feetype']);
        $data['added_by_id'] = \Auth::user()->id;
        if ($data['feetype'] == 0){
            $data['p_f'] = 1;
        }elseif ($data['feetype'] == 1){
            $data['once_off'] = 1;
        }elseif ($data['feetype']==2){
            $data['crf'] = 1;
        }
        $this->feesRepo->create($data);

        return Qs::jsonStoreOk();
    }
    public function testddump(){
        $academicPeriodID = 63;

        $academics = DB::table('ac_academicPeriods')
            ->where('ac_academicPeriods.id', $academicPeriodID)
            ->join('ac_classes', 'ac_academicPeriods.id', '=', 'ac_classes.academicPeriodID')
            ->join('ac_courses', 'ac_classes.courseID', '=', 'ac_courses.id')
            ->join('ac_programCourses', 'ac_courses.id', '=', 'ac_programCourses.courseID')
            ->join('ac_programs', 'ac_programCourses.programID', '=', 'ac_programs.id')
            ->join('ac_course_levels', 'ac_programCourses.level_id', '=', 'ac_course_levels.id')
            ->select(
                'ac_academicPeriods.code AS academic_period',
                'ac_programs.name AS program',
                'ac_course_levels.name AS level',
                'ac_courses.name AS course'
            )
            ->orderBy('academic_period')
            ->orderBy('program')
            ->orderBy('level')
            ->get();

        $result = [];
        foreach ($academics as $academic) {
            $academicPeriod = $academic->academic_period;
            $program = $academic->program;
            $level = $academic->level;
            $course = $academic->course;

            $result[$academicPeriod][$program][$level][] = $course;
        }

// Output the result
        dd($result);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AcademicPeriodUpdate $req, string $id)
    {
        $id = Qs::decodeHash($id);
        $data = $req->only(['code', 'registrationDate', 'lateRegistrationDate', 'acStartDate', 'acEndDate', 'periodID', 'resultsThreshold', 'registrationThreshold', 'type', 'examSlipThreshold', 'studyModeIDAllowed']);
        $data['registrationDate'] = date('Y-m-d', strtotime($data['registrationDate']));
        $data['lateRegistrationDate'] = date('Y-m-d', strtotime($data['lateRegistrationDate']));
        $data['acStartDate'] = date('Y-m-d', strtotime($data['acStartDate']));
        $data['acEndDate'] = date('Y-m-d', strtotime($data['acEndDate']));
        $this->academic->update($id,$data);

        return Qs::jsonStoreOk();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Qs::decodeHash($id);
        $this->academic->find($id)->delete();
        return back()->with('flash_success', __('msg.delete_ok'));
    }
}
