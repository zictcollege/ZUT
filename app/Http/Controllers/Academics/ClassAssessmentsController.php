<?php

namespace App\Http\Controllers\Academics;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Repositories\Academicperiods;
use App\Repositories\AssessmentTypesRepo;
use App\Repositories\ClassAssessmentsRepo;
use DB;
use Illuminate\Http\Request;

class ClassAssessmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $classaAsessmentRepo,$academic,$assessmentTypes;
    public function __construct(ClassAssessmentsRepo $classaAsessmentRepo,Academicperiods $academic,AssessmentTypesRepo $assessmentTypes)
    {
        $this->middleware(TeamSA::class, ['except' => ['destroy',] ]);
        $this->middleware(SuperAdmin::class, ['only' => ['destroy',] ]);

        $this->classaAsessmentRepo = $classaAsessmentRepo;
        $this->academic = $academic;
        $this->assessmentTypes = $assessmentTypes;
    }
    public function index()
    {
        // Retrieve the academic periods and class assessments
        $academicPeriods = DB::table('ac_academicPeriods')
            ->whereDate('ac_academicPeriods.acEndDate', '>=', now())
            ->join('ac_classes', 'ac_academicPeriods.id', '=', 'ac_classes.academicPeriodID')
            ->join('ac_courses', 'ac_classes.courseID', '=', 'ac_courses.id')
            ->join('ac_classAssesments', 'ac_classes.id', '=', 'ac_classAssesments.classID')
            ->join('ac_assessmentTypes', 'ac_assessmentTypes.id', '=', 'ac_classAssesments.assesmentID')
            ->select(
                'ac_academicPeriods.id AS academic_period_id',
                'ac_academicPeriods.code AS academic_period_code',
                'ac_courses.id AS course_id',
                'ac_courses.code AS course_code',
                'ac_courses.name AS course_name',
                'ac_classAssesments.id AS class_assessment_id',
                'ac_classAssesments.total',
                'ac_assessmentTypes.name AS assessment_type_name'
            )
            ->get()
            ->groupBy('academic_period_id');

        $academicPeriodsArray = [];

        foreach ($academicPeriods as $academicPeriodId => $academicPeriod) {
            $classAssessments = [];

            foreach ($academicPeriod as $item) {
                $classAssessments[] = [
                    'class_assessment_id' => $item->class_assessment_id,
                    'total' => $item->total,
                    'course_id' => $item->course_id,
                    'course_code' => $item->course_code,
                    'course_name' => $item->course_name,
                    'assessment_type_name' => $item->assessment_type_name,
                ];
            }

            $academicPeriodsArray[] = [
                'academic_period_id' => $academicPeriodId,
                'academic_period_code' => $academicPeriod[0]->academic_period_code,
                'class_assessments' => $classAssessments,
            ];
        }
        $data['open'] = $this->academic->getAllopen();
        $data['assess'] = $this->assessmentTypes->getAll();
        return view('pages.academics.class_assessments.index', ['academicPeriodsArray' => $academicPeriodsArray],$data);
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
    public function destroy(string $id)
    {
        //
    }
    public function getClasses(string $id){
        $classes = DB::table('ac_classes')
            ->where('ac_classes.academicPeriodID', $id)
            ->join('ac_courses', 'ac_classes.courseID', '=', 'ac_courses.id')
            ->select(
                'ac_classes.id AS id',
                'ac_courses.code AS course_code',
                'ac_courses.name AS name'
            )
            ->get();
        return $classes;
    }
}
