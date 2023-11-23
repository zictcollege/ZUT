<?php

namespace App\Http\Controllers\Academics;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Models\Academics\CourseLevels;
use App\Traits\Academics\BoardOfExaminer;
use App\Traits\User\General;
use DB;
use Illuminate\Http\Request;

class CAsController extends Controller
{
    use General;
    use BoardOfExaminer;
    /**
     * Display a listing of the resource.
     */
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

    public function GetProgramResultsLevelCas(Request $request)
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
        $groupedAta = DB::table('ac_gradebook_imports')
            ->select('ac_gradebook_imports.studentID as student_id')
            ->where('ac_gradebook_imports.academicPeriodID', '=', $aid)
            ->where('ac_gradebook_imports.programID', '=', $pid)
            ->where('ac_gradebook_imports.student_level_id', '=', $level)
            ->where('ac_gradebook_imports.status', '=', 0)
            ->groupBy('ac_gradebook_imports.studentID')
            //->get();
            ->paginate(2, ['*'], 'page', 1);
        $studentIds = $groupedAta->pluck('student_id');
        //dd($studentIds);

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
                'ac_gradebook_imports.id as id',
                'ac_gradebook_imports.assessmentID as assessmentID',
                'ac_assessmentTypes.name as assessment_name',
                'ac_gradebook_imports.key as key',
                'ac_gradebook_imports.status as status'
            )
            ->where('ac_gradebook_imports.academicPeriodID', '=', $aid)
            ->where('ac_gradebook_imports.programID', '=', $pid)
            ->where('ac_gradebook_imports.student_level_id', '=', $level)
            ->where('ac_gradebook_imports.status', '=', 0)
            ->whereIn('ac_gradebook_imports.studentID', $studentIds)
            ->get();
        //->paginate(5);
        //->paginate(4, ['*'], 'page', 1);
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
                    'current_page' => $groupedAta->currentPage(),
                    'last_page' => $groupedAta->lastPage(),
                    'per_page' => $groupedAta->perPage(),
                    'program' => $row->program_id,
                    'program_name' => $row->program_name,
                    'level_name' => $level_name->name,
                    'level_id' => $level,
                    'program_code' => $row->program_code,
                    'academicperiodname' => $row->academicPeriodcode,
                    'total_students' => $totalStudents,
                    'students' => [],
                ];
            }
            if ($row->status == 1) {
                continue; // Skip this student
            }
            $studentId = $row->student_id;
            if (!isset($results[$academic['academic']]['students'][$studentId])) {
                $results[$academic['academic']]['students'][$studentId] = [
                    'name' => $row->first_name . ' ' . $row->last_name,
                    'student_id' => $studentId,
                    'level' => $level_name->name,
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
                        'assessments' => [],
                    ];
                }
            }


            $assess = $row->assessment_name;
            if (!isset($results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['assessments'][$assess])) {
                $assessments = [
                    'total' => $row->total,
                    //'id'=> $row->id,
                    'assessID' => $row->assessmentID,
                    'assessment_name' => $row->assessment_name,
                    'key' => $row->key,
                    'status' => $row->status,
                ];

                if ($row->assessment_name != 'Exam') {
                    $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['CA'] += $row->total;
                }
//                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['total'] += $row->total;
                //$results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['total'] += $row->total;
                //$results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['CA'] += $row->total;

                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['assessments'][$assess] = $assessments;
            }
        }
//        foreach ($results as &$academicPeriodData) {
//            foreach ($academicPeriodData['students'] as &$studentData) {
//                foreach ($studentData['courses'] as &$courseData) {
//                    $totalScore = $courseData['total'];
//                    $courseData['grade'] = $this->calculateGrade($totalScore);
//                    $studentData['commentData'] = $this->calculateComment($studentData['courses']);
//                }
//            }
//        }

        //dd($results);
        return view('pages.academics.cas.results_review_board', compact('results'));
        //return view('pages.academics.class_assessments.update_marks', compact('results'));
    }

    public function GetProgramsToPublishCas(string $id)
    {
        $id = Qs::decodeHash($id);
        //dd($id);
        $grouped = DB::table('ac_gradebook_imports')
            ->join('ac_programs', 'ac_programs.id', '=', 'ac_gradebook_imports.programID')
            ->join('ac_academicPeriods', 'ac_academicPeriods.id', '=', 'ac_gradebook_imports.academicPeriodID')
            ->join('ac_qualifications', 'ac_qualifications.id', '=', 'ac_programs.qualification_id')
            //->join('ac_programCourses', 'ac_programCourses.programID', '=', 'ac_programs.id')
            ->join('ac_course_levels', 'ac_gradebook_imports.student_level_id', '=', 'ac_course_levels.id')
            ->select(
                'ac_programs.id',
                'ac_programs.name',
                'ac_programs.code',
                'ac_academicPeriods.code as ac_code',
                'ac_qualifications.name AS qualification',
                'ac_course_levels.id as level_id',
                'ac_course_levels.name as level_name',
                'status',
                DB::raw('COUNT(DISTINCT studentID) as students')
            )
            ->where('ac_gradebook_imports.academicPeriodID', '=', $id)
            // ->groupBy('ac_programs.id')
            ->distinct()
            ->groupBy('ac_programs.id', 'ac_programs.name', 'ac_programs.code', 'ac_code', 'status', 'level_id', 'level_name')
            ->get();

        $groupedApClasses = [];
        foreach ($grouped as $program) {
            $programID = $program->id;

            // Create an array for the class if it doesn't exist
            if (!isset($groupedApClasses[$programID])) {
                $groupedApClasses[$programID] = [
                    'code' => $program->code,
                    'id' => $program->id,
                    'name' => $program->name,
                    'ac_code' => $program->ac_code,
                    'qualification' => $program->qualification,
                    'students' => 0,
                    'status' => $program->status,
                    'levels' => [],
                ];
            }
            $groupedApClasses[$programID]['students'] += $program->students;
            // Check if the level data is not already present
            $levelData = [
                'level_name' => $program->level_name,
                'level_id' => $program->level_id,
                //'students' => $program->students,
            ];
            if (!in_array($levelData, $groupedApClasses[$programID]['levels'])) {
                // Add assessment data to the class's levels array
                $groupedApClasses[$programID]['levels'][] = $levelData;
            }
        }


        // Convert the associative array to indexed array if needed
        $programs = array_values($groupedApClasses);
        //dd($programs);

        $academic['apid'] = $id;
        $academic['period'] = \App\Models\Academics\AcademicPeriods::find($id);


        //dd($programs[0]->name);
        return view('pages.academics.cas.edit', compact('programs'), $academic);
    }

    public
    function LoadMoreResultsCas(Request $request)
    {
        $current_page = $request->input('current_page');
        $last_page = $request->input('last_page');
        $per_page = $request->input('per_page');

        $aid = $request->input('academic');
        $pid = $request->input('program');
        $leveid = $request->input('level_name');

        $level_name = CourseLevels::find($leveid);
        //dd($level);

        $groupedAta = DB::table('ac_gradebook_imports')
            ->select('ac_gradebook_imports.studentID as student_id')
            ->where('ac_gradebook_imports.academicPeriodID', '=', $aid)
            ->where('ac_gradebook_imports.programID', '=', $pid)
            ->where('ac_gradebook_imports.student_level_id', '=', $leveid)
            ->where('ac_gradebook_imports.status', '=', 0)
            ->groupBy('ac_gradebook_imports.studentID')
            //->get();
            ->paginate($per_page, ['*'], 'page', $current_page + 1);
        $studentIds = $groupedAta->pluck('student_id');
        //dd($studentIds);

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
                'ac_gradebook_imports.id as id',
                'ac_gradebook_imports.assessmentID as assessmentID',
                'ac_assessmentTypes.name as assessment_name',
                'ac_gradebook_imports.key as key',
                'ac_gradebook_imports.status as status'
            )
            ->where('ac_gradebook_imports.academicPeriodID', '=', $aid)
            ->where('ac_gradebook_imports.programID', '=', $pid)
            ->where('ac_gradebook_imports.student_level_id', '=', $leveid)
            ->where('ac_gradebook_imports.status', '=', 0)
            ->whereIn('ac_gradebook_imports.studentID', $studentIds)
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
                    'current_page' => $groupedAta->currentPage(),
                    'last_page' => $groupedAta->lastPage(),
                    'per_page' => $groupedAta->perPage(),
                    'program' => $row->program_id,
                    'program_name' => $row->program_name,
                    'level_name' => $level_name->name,
                    'level_id' => $leveid,
                    'program_code' => $row->program_code,
                    'academicperiodname' => $row->academicPeriodcode,
                    'students' => [],
                ];
            }
            $progression = self::checkProgression($row->userID, $row->program_id);
            $yearOfStudy = $progression['currentLevelName'];
            if ($row->status == 1) {
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

                if ($row->assessment_name != 'Exam') {
                    $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['CA'] += $row->total;
                }
                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['total'] += $row->total;
                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['assessments'][$assess] = $assessments;
            }
        }

        return $results;
    }
}
