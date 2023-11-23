<?php

namespace App\Http\Controllers\Academics;


use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Models\Academics\CourseLevels;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Academics\ClassAssessmentsController as classAssess;

class AssessmentReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(string $id)
    {
        $id = Qs::decodeHash($id);

        $grouped = DB::table('ac_gradebook_imports')
            ->join('users', 'users.student_id', '=', 'ac_gradebook_imports.studentID')
            ->join('ac_assessmentTypes', 'ac_assessmentTypes.id', '=', 'ac_gradebook_imports.assessmentID')
            ->join('ac_academicPeriods', 'ac_academicPeriods.id', '=', 'ac_gradebook_imports.academicPeriodID')
            ->join('ac_programs', 'ac_programs.id', '=', 'ac_gradebook_imports.programID')
            ->select(
                'ac_gradebook_imports.academicPeriodID as academicPeriod',
                'ac_academicPeriods.code as academicPeriodcode',
                'ac_gradebook_imports.programID as program_id',
                'ac_programs.name as program_name',
                'ac_programs.code as program_code',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.id as userID',
                'ac_gradebook_imports.studentID as student_id',
                'ac_gradebook_imports.id as id',
                'ac_gradebook_imports.assessmentID as assessmentID',
                'ac_assessmentTypes.name as assessment_name',
                'ac_gradebook_imports.status as status',
                DB::raw('SUM(ac_gradebook_imports.total) as total_score') // Sum of total scores
            )
            ->where('ac_gradebook_imports.academicPeriodID', '=', $id)
            ->where('ac_gradebook_imports.programID', '=', 4)
            ->where('ac_gradebook_imports.student_level_id', '=', 2)
            ->where('ac_gradebook_imports.status', '=', 0)
            ->where('ac_gradebook_imports.code', '=', 'BIT 2210')
            ->groupBy(
                'ac_gradebook_imports.academicPeriodID',
                'ac_academicPeriods.code',
                'ac_gradebook_imports.programID',
                'ac_programs.name',
                'ac_programs.code',
                'users.first_name',
                'users.last_name',
                'users.id',
                'ac_gradebook_imports.studentID',
                'ac_gradebook_imports.id',
                'ac_gradebook_imports.assessmentID',
                'ac_assessmentTypes.name',
                'ac_gradebook_imports.status'
            )
            ->orderByDesc('total_score') // Order by total score in descending order to get best performers first
            ->limit(5) // Limit the results to the top 5 performers
            ->get();
        $academics['period'] = \App\Models\Academics\AcademicPeriods::find($id);
        $academics['programs'] = DB::table('ac_programs')
            ->distinct()
            ->join('ac_gradebook_imports', 'ac_programs.id', '=', 'ac_gradebook_imports.programID')
            ->select('ac_programs.id', 'ac_programs.name', 'ac_programs.code')
            ->where('ac_gradebook_imports.academicPeriodID', '=', $id)
            ->get();
        // dd( $academics['programs']);

        $students = DB::table('ac_gradebook_imports')
            ->select('ac_gradebook_imports.studentID as student_id')
            ->where('ac_gradebook_imports.academicPeriodID', '=', $id)
            ->where('ac_gradebook_imports.programID', '=', 4)
            //->where('ac_gradebook_imports.student_level_id', '=', $level)
            ->where('ac_gradebook_imports.status', '=', 0)
            ->groupBy('ac_gradebook_imports.studentID')
            ->get();

        $totalStudents = $students->count();

        $groupeddt = DB::table('ac_gradebook_imports')
            ->join('users', 'users.student_id', '=', 'ac_gradebook_imports.studentID')
            ->join('ac_assessmentTypes', 'ac_assessmentTypes.id', '=', 'ac_gradebook_imports.assessmentID')
            ->join('ac_academicPeriods', 'ac_academicPeriods.id', '=', 'ac_gradebook_imports.academicPeriodID')
            ->join('ac_programs', 'ac_programs.id', '=', 'ac_gradebook_imports.programID')
            ->select(
                'ac_gradebook_imports.academicPeriodID as academicPeriod',
                'ac_academicPeriods.code as academicPeriodcode',
                'ac_gradebook_imports.programID as program_id',
                'ac_programs.name as program_name',
                'ac_programs.code as program_code',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.gender as gender',
                'users.id as userID',
                'ac_gradebook_imports.code as code',
                'ac_gradebook_imports.title as title',
                'ac_gradebook_imports.total as total',
                'ac_gradebook_imports.studentID as student_id',
                'ac_gradebook_imports.assessmentID as assessmentID',
                'ac_assessmentTypes.name as assessment_name',
            )
            ->where('ac_gradebook_imports.academicPeriodID', '=', $id)
            ->where('ac_gradebook_imports.programID', '=', 4)
            //->where('ac_gradebook_imports.student_level_id', '=', $level)
            ->where('ac_gradebook_imports.status', '=', 0)
            //->whereIn('ac_gradebook_imports.studentID',$studentIds)
            ->get();
        //->paginate(5);
        //->paginate(4, ['*'], 'page', 1);
        $results = [];
        foreach ($groupeddt as $row) {
            $academic = [
                'academic' => $row->academicPeriod,
            ];
            if (!isset($results['academic'])) {
                $results['academic'] = [
                    'academic' => $row->academicPeriod,
                    'program_name' => $row->program_name,
                    // 'level_name' => $level_name->name,
                    // 'level_id' => $level,
                    'program_code' => $row->program_code,
                    'academicperiodname' => $row->academicPeriodcode,
                    'total_students' => $totalStudents,
                    'clearPassCount' => 0,
                    'failCount' => 0,
                    'MalesClearPassCount' => 0,
                    'FemaleClearPassCount' => 0,
                    'FailedFemaleCount' => 0,
                    'FailedMaleCount' => 0,
                    'students' => [],
                ];
            }
            $studentId = $row->student_id;
            if (!isset($results['academic']['students'][$studentId])) {
                $results['academic']['students'][$studentId] = [
                    'name' => $row->first_name . ' ' . $row->last_name,
                    'student_id' => $studentId,
                    'total' => 0,
                    'gender' => $row->gender,
                    //'level' => $level_name->name,
                    'courses' => [],
                ];
            }

            $courseCode = $row->code;
            if (!isset($results['academic']['students'][$studentId]['courses'][$courseCode])) {
                $course = [
                    'code' => $row->code,
                    'title' => $row->title,
                    'CA' => 0,
                    'total' => 0,
                    'assessments' => [],
                ];

                $results['academic']['students'][$studentId]['courses'][$courseCode] = $course;
            }
            $assess = $row->assessment_name;
            if (!isset($results['academic']['students'][$studentId]['courses'][$courseCode]['assessments'][$assess])) {
                $assessments = [
                    'total' => $row->total,
                    'assessID' => $row->assessmentID,
                    'assessment_name' => $row->assessment_name,
                    //'key' => $row->key,
                    //'status' => $row->status,
                ];

                if ($row->assessment_name != 'Exam') {
                    $results['academic']['students'][$studentId]['courses'][$courseCode]['CA'] += $row->total;
                }
                $results['academic']['students'][$studentId]['courses'][$courseCode]['total'] += $row->total;

                $results['academic']['students'][$studentId]['courses'][$courseCode]['assessments'][$assess] = $assessments;
            }
        }

        foreach ($results as &$academicPeriodData) {
            foreach ($academicPeriodData['students'] as &$studentData) {
                foreach ($studentData['courses'] as &$courseData) {
                    $totalScore = $courseData['total'];
                    $studentData['total'] += $totalScore;
                    $courseData['grade'] = classAssess::calculateGrade($totalScore);
                    $studentData['commentData'] = classAssess::calculateComment($studentData['courses']);
                }
                if ($studentData['commentData'] == 'Clear Pass') {
                    $academicPeriodData['clearPassCount']++;
                } else {
                    $academicPeriodData['failCount']++;
                }

                if ($studentData['commentData'] == 'Clear Pass' && $studentData['gender'] == 'Male' || $studentData['gender'] == 'male') {
                    $academicPeriodData['MalesClearPassCount']++;
                } elseif ($studentData['commentData'] == 'Clear Pass' && $studentData['gender'] == 'Female' || $studentData['gender'] == 'female') {
                    $academicPeriodData['FemaleClearPassCount']++;
                } elseif ($studentData['commentData'] != 'Clear Pass' && $studentData['gender'] == 'Male' || $studentData['gender'] == 'male') {
                    $academicPeriodData['FailedMaleCount']++;
                } elseif (!$studentData['commentData'] != 'Clear Pass' && $studentData['gender'] == 'Female' || $studentData['gender'] == 'female') {
                    $academicPeriodData['FailedFemaleCount']++;
                }
            }
        }

        //dd($results);
        return view('pages.academics.class_assessments.reports.index', compact('grouped', 'results'), $academics);
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

    public function GetProgramResultsLevel(Request $request)
    {
        $aid = $request->query('aid');
        $pid = $request->query('pid');
        $level = $request->query('level');
        $aid = Qs::decodeHash($aid);
        $pid = Qs::decodeHash($pid);
        $level = Qs::decodeHash($level);
        $level_name = CourseLevels::find($level);
        //dd($level);

        $students = DB::table('ac_gradebook_imports')
            ->select('ac_gradebook_imports.studentID as student_id')
            ->where('ac_gradebook_imports.academicPeriodID', '=', $aid)
            ->where('ac_gradebook_imports.programID', '=', $pid)
            ->where('ac_gradebook_imports.student_level_id', '=', $level)
            ->where('ac_gradebook_imports.status', '=', 0)
            ->groupBy('ac_gradebook_imports.studentID')
            ->get();

        $totalStudents = $students->count();

        $grouped = DB::table('ac_gradebook_imports')
            ->join('users', 'users.student_id', '=', 'ac_gradebook_imports.studentID')
            ->join('ac_assessmentTypes', 'ac_assessmentTypes.id', '=', 'ac_gradebook_imports.assessmentID')
            ->join('ac_academicPeriods', 'ac_academicPeriods.id', '=', 'ac_gradebook_imports.academicPeriodID')
            ->join('ac_programs', 'ac_programs.id', '=', 'ac_gradebook_imports.programID')
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
                'ac_gradebook_imports.assessmentID as assessmentID',
                'ac_assessmentTypes.name as assessment_name',
            )
            ->where('ac_gradebook_imports.academicPeriodID', '=', $aid)
            ->where('ac_gradebook_imports.programID', '=', $pid)
            ->where('ac_gradebook_imports.student_level_id', '=', $level)
            ->where('ac_gradebook_imports.status', '=', 0)
            //->whereIn('ac_gradebook_imports.studentID',$studentIds)
            ->get();
        //->paginate(5);
        //->paginate(4, ['*'], 'page', 1);
        $results = [];
        foreach ($grouped as $row) {
            $academic = [
                'academic' => $row->academicPeriod,
            ];
            if (!isset($results[$academic['academic']])) {
                $results[$academic['academic']] = [
                    'academic' => $row->academicPeriod,
                    'program_name' => $row->program_name,
                    'level_name' => $level_name->name,
                    'level_id' => $level,
                    'program_code' => $row->program_code,
                    'academicperiodname' => $row->academicPeriodcode,
                    'total_students' => $totalStudents,
                    'students' => [],
                ];
            }
            $studentId = $row->student_id;
            if (!isset($results[$academic['academic']]['students'][$studentId])) {
                $results[$academic['academic']]['students'][$studentId] = [
                    'name' => $row->first_name . ' ' . $row->last_name,
                    'student_id' => $studentId,
                    'level' => $level_name->name,
                    'courses' => [],
                ];
            }

            $courseCode = $row->code;
            if (!isset($results[$academic['academic']]['students'][$studentId]['courses'][$courseCode])) {
                $course = [
                    'code' => $row->code,
                    'title' => $row->title,
                    'CA' => 0,
                    'total' => 0,
                    'assessments' => [],
                ];

                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode] = $course;
            }
            $assess = $row->assessment_name;
            if (!isset($results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['assessments'][$assess])) {
                $assessments = [
                    'total' => $row->total,
                    'assessID' => $row->assessmentID,
                    'assessment_name' => $row->assessment_name,
                    'key' => $row->key,
                    'status' => $row->status,
                ];

                if ($row->assessment_name != 'Exam') {
                    $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['CA'] += $row->total;
                }
                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['total'] += $row->total;

                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['assessments'][$assess] = $assessments;
            }
        }
        foreach ($results as &$academicPeriodData) {
            foreach ($academicPeriodData['students'] as &$studentData) {
                foreach ($studentData['courses'] as &$courseData) {
                    $totalScore = $courseData['total'];
                    $courseData['grade'] = classAssess::calculateGrade($totalScore);
                    $studentData['commentData'] = classAssess::calculateComment($studentData['courses']);
                }
            }
        }

        dd($results);

    }

    public function getbestprogramLeveresults(Request $request)
    {
        $id = Qs::decodeHash($id);

        $grouped = DB::table('ac_gradebook_imports')
            ->join('users', 'users.student_id', '=', 'ac_gradebook_imports.studentID')
            ->join('ac_assessmentTypes', 'ac_assessmentTypes.id', '=', 'ac_gradebook_imports.assessmentID')
            ->join('ac_academicPeriods', 'ac_academicPeriods.id', '=', 'ac_gradebook_imports.academicPeriodID')
            ->join('ac_programs', 'ac_programs.id', '=', 'ac_gradebook_imports.programID')
            ->select(
                'ac_gradebook_imports.academicPeriodID as academicPeriod',
                'ac_academicPeriods.code as academicPeriodcode',
                'ac_gradebook_imports.programID as program_id',
                'ac_programs.name as program_name',
                'ac_programs.code as program_code',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.id as userID',
                'ac_gradebook_imports.studentID as student_id',
                'ac_gradebook_imports.id as id',
                'ac_gradebook_imports.assessmentID as assessmentID',
                'ac_assessmentTypes.name as assessment_name',
                'ac_gradebook_imports.status as status',
                DB::raw('SUM(ac_gradebook_imports.total) as total_score') // Sum of total scores
            )
            ->where('ac_gradebook_imports.academicPeriodID', '=', $id)
            ->where('ac_gradebook_imports.programID', '=', 4)
            ->where('ac_gradebook_imports.student_level_id', '=', 2)
            ->where('ac_gradebook_imports.status', '=', 0)
            ->where('ac_gradebook_imports.code', '=', 'BIT 2210')
            ->groupBy(
                'ac_gradebook_imports.academicPeriodID',
                'ac_academicPeriods.code',
                'ac_gradebook_imports.programID',
                'ac_programs.name',
                'ac_programs.code',
                'users.first_name',
                'users.last_name',
                'users.id',
                'ac_gradebook_imports.studentID',
                'ac_gradebook_imports.id',
                'ac_gradebook_imports.assessmentID',
                'ac_assessmentTypes.name',
                'ac_gradebook_imports.status'
            )
            ->orderByDesc('total_score') // Order by total score in descending order to get best performers first
            ->limit(5) // Limit the results to the top 5 performers
            ->get();


        //dd($results);
        return $grouped;
    }

    public function getprogrambest(Request $request)
    {
        $academics['period'] = \App\Models\Academics\AcademicPeriods::find($id);

        $students = DB::table('ac_gradebook_imports')
            ->select('ac_gradebook_imports.studentID as student_id')
            ->where('ac_gradebook_imports.academicPeriodID', '=', $id)
            ->where('ac_gradebook_imports.programID', '=', 4)
            //->where('ac_gradebook_imports.student_level_id', '=', $level)
            ->where('ac_gradebook_imports.status', '=', 0)
            ->groupBy('ac_gradebook_imports.studentID')
            ->get();

        $totalStudents = $students->count();

        $groupeddt = DB::table('ac_gradebook_imports')
            ->join('users', 'users.student_id', '=', 'ac_gradebook_imports.studentID')
            ->join('ac_assessmentTypes', 'ac_assessmentTypes.id', '=', 'ac_gradebook_imports.assessmentID')
            ->join('ac_academicPeriods', 'ac_academicPeriods.id', '=', 'ac_gradebook_imports.academicPeriodID')
            ->join('ac_programs', 'ac_programs.id', '=', 'ac_gradebook_imports.programID')
            ->select(
                'ac_gradebook_imports.academicPeriodID as academicPeriod',
                'ac_academicPeriods.code as academicPeriodcode',
                'ac_gradebook_imports.programID as program_id',
                'ac_programs.name as program_name',
                'ac_programs.code as program_code',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.gender as gender',
                'users.id as userID',
                'ac_gradebook_imports.code as code',
                'ac_gradebook_imports.title as title',
                'ac_gradebook_imports.total as total',
                'ac_gradebook_imports.studentID as student_id',
                'ac_gradebook_imports.assessmentID as assessmentID',
                'ac_assessmentTypes.name as assessment_name',
            )
            ->where('ac_gradebook_imports.academicPeriodID', '=', $id)
            ->where('ac_gradebook_imports.programID', '=', 4)
            //->where('ac_gradebook_imports.student_level_id', '=', $level)
            ->where('ac_gradebook_imports.status', '=', 0)
            ->get();
        $results = [];
        foreach ($groupeddt as $row) {
            $academic = [
                'academic' => $row->academicPeriod,
            ];
            if (!isset($results['academic'])) {
                $results['academic'] = [
                    'academic' => $row->academicPeriod,
                    'program_name' => $row->program_name,
                    // 'level_name' => $level_name->name,
                    // 'level_id' => $level,
                    'program_code' => $row->program_code,
                    'academicperiodname' => $row->academicPeriodcode,
                    'total_students' => $totalStudents,
                    'clearPassCount' => 0,
                    'failCount' => 0,
                    'MalesClearPassCount' => 0,
                    'FemaleClearPassCount' => 0,
                    'FailedFemaleCount' => 0,
                    'FailedMaleCount' => 0,
                    'students' => [],
                ];
            }
            $studentId = $row->student_id;
            if (!isset($results['academic']['students'][$studentId])) {
                $results['academic']['students'][$studentId] = [
                    'name' => $row->first_name . ' ' . $row->last_name,
                    'student_id' => $studentId,
                    'total' => 0,
                    'gender' => $row->gender,
                    //'level' => $level_name->name,
                    'courses' => [],
                ];
            }

            $courseCode = $row->code;
            if (!isset($results['academic']['students'][$studentId]['courses'][$courseCode])) {
                $course = [
                    'code' => $row->code,
                    'title' => $row->title,
                    'CA' => 0,
                    'total' => 0,
                    'assessments' => [],
                ];

                $results['academic']['students'][$studentId]['courses'][$courseCode] = $course;
            }
            $assess = $row->assessment_name;
            if (!isset($results['academic']['students'][$studentId]['courses'][$courseCode]['assessments'][$assess])) {
                $assessments = [
                    'total' => $row->total,
                    'assessID' => $row->assessmentID,
                    'assessment_name' => $row->assessment_name,
                    //'key' => $row->key,
                    //'status' => $row->status,
                ];

                if ($row->assessment_name != 'Exam') {
                    $results['academic']['students'][$studentId]['courses'][$courseCode]['CA'] += $row->total;
                }
                $results['academic']['students'][$studentId]['courses'][$courseCode]['total'] += $row->total;

                $results['academic']['students'][$studentId]['courses'][$courseCode]['assessments'][$assess] = $assessments;
            }
        }

        foreach ($results as &$academicPeriodData) {
            foreach ($academicPeriodData['students'] as &$studentData) {
                foreach ($studentData['courses'] as &$courseData) {
                    $totalScore = $courseData['total'];
                    $studentData['total'] += $totalScore;
                    $courseData['grade'] = classAssess::calculateGrade($totalScore);
                    $studentData['commentData'] = classAssess::calculateComment($studentData['courses']);
                }
                if ($studentData['commentData'] == 'Clear Pass') {
                    $academicPeriodData['clearPassCount']++;
                } else {
                    $academicPeriodData['failCount']++;
                }

                if ($studentData['commentData'] == 'Clear Pass' && $studentData['gender'] == 'Male' || $studentData['gender'] == 'male') {
                    $academicPeriodData['MalesClearPassCount']++;
                } elseif ($studentData['commentData'] == 'Clear Pass' && $studentData['gender'] == 'Female' || $studentData['gender'] == 'female') {
                    $academicPeriodData['FemaleClearPassCount']++;
                } elseif ($studentData['commentData'] != 'Clear Pass' && $studentData['gender'] == 'Male' || $studentData['gender'] == 'male') {
                    $academicPeriodData['FailedMaleCount']++;
                } elseif (!$studentData['commentData'] != 'Clear Pass' && $studentData['gender'] == 'Female' || $studentData['gender'] == 'female') {
                    $academicPeriodData['FailedFemaleCount']++;
                }
            }
        }
        return $results;
    }

    public function getReportLevels(Request $request)
    {
        $programID = $request->input('programID');
        $academicPeriodID = $request->input('academicPeriodID');
         $data = DB::table('ac_gradebook_imports')
             ->distinct()
             ->join('ac_course_levels', 'ac_course_levels.id', '=', 'ac_gradebook_imports.student_level_id')
             ->select(
                 'ac_course_levels.name as name',
                 'ac_course_levels.id as id',
             )
             ->where('ac_gradebook_imports.academicPeriodID', '=', $academicPeriodID)
             ->where('ac_gradebook_imports.programID', '=', $programID)
             ->where('ac_gradebook_imports.status', '=', 0)
             ->get();
         return $data;
    }
    public function getReportsToAnalyzeP(Request $request){
        $academic = $request->input('academic');
        $programID = $request->input('programID');
        $level_id = $request->input('level_id');

        $grouped = DB::table('ac_gradebook_imports')
            ->join('users', 'users.student_id', '=', 'ac_gradebook_imports.studentID')
            ->join('ac_assessmentTypes', 'ac_assessmentTypes.id', '=', 'ac_gradebook_imports.assessmentID')
            ->join('ac_academicPeriods', 'ac_academicPeriods.id', '=', 'ac_gradebook_imports.academicPeriodID')
            ->join('ac_programs', 'ac_programs.id', '=', 'ac_gradebook_imports.programID')
            ->select(
                'ac_gradebook_imports.academicPeriodID as academicPeriod',
                'ac_academicPeriods.code as academicPeriodcode',
                'ac_gradebook_imports.programID as program_id',
                'ac_programs.name as program_name',
                'ac_programs.code as program_code',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.id as userID',
                'ac_gradebook_imports.studentID as student_id',
                'ac_gradebook_imports.id as id',
                'ac_gradebook_imports.assessmentID as assessmentID',
                'ac_assessmentTypes.name as assessment_name',
                'ac_gradebook_imports.status as status',
                DB::raw('SUM(ac_gradebook_imports.total) as total_score') // Sum of total scores
            )
            ->where('ac_gradebook_imports.academicPeriodID', '=', $academic)
            ->where('ac_gradebook_imports.programID', '=', $programID)
            ->where('ac_gradebook_imports.student_level_id', '=', $level_id)
            ->where('ac_gradebook_imports.status', '=', 0)
            ->where('ac_gradebook_imports.code', '=', 'BIT 2210')
            ->groupBy(
                'ac_gradebook_imports.academicPeriodID',
                'ac_academicPeriods.code',
                'ac_gradebook_imports.programID',
                'ac_programs.name',
                'ac_programs.code',
                'users.first_name',
                'users.last_name',
                'users.id',
                'ac_gradebook_imports.studentID',
                'ac_gradebook_imports.id',
                'ac_gradebook_imports.assessmentID',
                'ac_assessmentTypes.name',
                'ac_gradebook_imports.status'
            )
            ->orderByDesc('total_score') // Order by total score in descending order to get best performers first
            ->limit(5) // Limit the results to the top 5 performers
            ->get();
        $academics['period'] = \App\Models\Academics\AcademicPeriods::find($academic);
        $academics['programs'] = DB::table('ac_programs')
            ->distinct()
            ->join('ac_gradebook_imports', 'ac_programs.id', '=', 'ac_gradebook_imports.programID')
            ->select('ac_programs.id', 'ac_programs.name', 'ac_programs.code')
            ->where('ac_gradebook_imports.academicPeriodID', '=', $academic)
            ->get();

        $students = DB::table('ac_gradebook_imports')
            ->select('ac_gradebook_imports.studentID as student_id')
            ->where('ac_gradebook_imports.academicPeriodID', '=', $academic)
            ->where('ac_gradebook_imports.programID', '=', $programID)
            ->where('ac_gradebook_imports.student_level_id', '=', $level_id)
            ->where('ac_gradebook_imports.status', '=', 0)
            ->groupBy('ac_gradebook_imports.studentID')
            ->get();

        $totalStudents = $students->count();

        $groupeddt = DB::table('ac_gradebook_imports')
            ->join('users', 'users.student_id', '=', 'ac_gradebook_imports.studentID')
            ->join('ac_assessmentTypes', 'ac_assessmentTypes.id', '=', 'ac_gradebook_imports.assessmentID')
            ->join('ac_academicPeriods', 'ac_academicPeriods.id', '=', 'ac_gradebook_imports.academicPeriodID')
            ->join('ac_programs', 'ac_programs.id', '=', 'ac_gradebook_imports.programID')
            ->select(
                'ac_gradebook_imports.academicPeriodID as academicPeriod',
                'ac_academicPeriods.code as academicPeriodcode',
                'ac_gradebook_imports.programID as program_id',
                'ac_programs.name as program_name',
                'ac_programs.code as program_code',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.gender as gender',
                'users.id as userID',
                'ac_gradebook_imports.code as code',
                'ac_gradebook_imports.title as title',
                'ac_gradebook_imports.total as total',
                'ac_gradebook_imports.studentID as student_id',
                'ac_gradebook_imports.assessmentID as assessmentID',
                'ac_assessmentTypes.name as assessment_name',
            )
            ->where('ac_gradebook_imports.academicPeriodID', '=', $academic)
            ->where('ac_gradebook_imports.programID', '=', $programID)
            ->where('ac_gradebook_imports.student_level_id', '=', $level_id)
            ->where('ac_gradebook_imports.status', '=', 0)
            //->whereIn('ac_gradebook_imports.studentID',$studentIds)
            ->get();
        //->paginate(5);
        //->paginate(4, ['*'], 'page', 1);
        $results = [];
        foreach ($groupeddt as $row) {
            $academic = [
                'academic' => $row->academicPeriod,
            ];
            if (!isset($results['academic'])) {
                $results['academic'] = [
                    'academic' => $row->academicPeriod,
                    'program_name' => $row->program_name,
                    // 'level_name' => $level_name->name,
                    // 'level_id' => $level,
                    'program_code' => $row->program_code,
                    'academicperiodname' => $row->academicPeriodcode,
                    'total_students' => $totalStudents,
                    'clearPassCount' => 0,
                    'failCount' => 0,
                    'MalesClearPassCount' => 0,
                    'FemaleClearPassCount' => 0,
                    'FailedFemaleCount' => 0,
                    'FailedMaleCount' => 0,
                    'students' => [],
                ];
            }
            $studentId = $row->student_id;
            if (!isset($results['academic']['students'][$studentId])) {
                $results['academic']['students'][$studentId] = [
                    'name' => $row->first_name . ' ' . $row->last_name,
                    'student_id' => $studentId,
                    'total' => 0,
                    'gender' => $row->gender,
                    //'level' => $level_name->name,
                    'courses' => [],
                ];
            }

            $courseCode = $row->code;
            if (!isset($results['academic']['students'][$studentId]['courses'][$courseCode])) {
                $course = [
                    'code' => $row->code,
                    'title' => $row->title,
                    'CA' => 0,
                    'total' => 0,
                    'assessments' => [],
                ];

                $results['academic']['students'][$studentId]['courses'][$courseCode] = $course;
            }
            $assess = $row->assessment_name;
            if (!isset($results['academic']['students'][$studentId]['courses'][$courseCode]['assessments'][$assess])) {
                $assessments = [
                    'total' => $row->total,
                    'assessID' => $row->assessmentID,
                    'assessment_name' => $row->assessment_name,
                    //'key' => $row->key,
                    //'status' => $row->status,
                ];

                if ($row->assessment_name != 'Exam') {
                    $results['academic']['students'][$studentId]['courses'][$courseCode]['CA'] += $row->total;
                }
                $results['academic']['students'][$studentId]['courses'][$courseCode]['total'] += $row->total;

                $results['academic']['students'][$studentId]['courses'][$courseCode]['assessments'][$assess] = $assessments;
            }
        }

        foreach ($results as &$academicPeriodData) {
            foreach ($academicPeriodData['students'] as &$studentData) {
                foreach ($studentData['courses'] as &$courseData) {
                    $totalScore = $courseData['total'];
                    $studentData['total'] += $totalScore;
                    $courseData['grade'] = classAssess::calculateGrade($totalScore);
                    $studentData['commentData'] = classAssess::calculateComment($studentData['courses']);
                }
                if ($studentData['commentData'] == 'Clear Pass') {
                    $academicPeriodData['clearPassCount']++;
                } else {
                    $academicPeriodData['failCount']++;
                }

                if ($studentData['commentData'] == 'Clear Pass' && $studentData['gender'] == 'Male' || $studentData['gender'] == 'male') {
                    $academicPeriodData['MalesClearPassCount']++;
                } elseif ($studentData['commentData'] == 'Clear Pass' && $studentData['gender'] == 'Female' || $studentData['gender'] == 'female') {
                    $academicPeriodData['FemaleClearPassCount']++;
                } elseif ($studentData['commentData'] != 'Clear Pass' && $studentData['gender'] == 'Male' || $studentData['gender'] == 'male') {
                    $academicPeriodData['FailedMaleCount']++;
                } elseif (!$studentData['commentData'] != 'Clear Pass' && $studentData['gender'] == 'Female' || $studentData['gender'] == 'female') {
                    $academicPeriodData['FailedFemaleCount']++;
                }
            }
        }
        //return $results;
        return view('pages.academics.class_assessments.reports.index', compact('grouped', 'results'), $academics);

    }
}
