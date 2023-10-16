<?php

namespace App\Http\Controllers\Academics;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\SuperAdmin;
use App\Http\Middleware\Custom\TeamSA;
use App\Http\Middleware\Custom\TeamSAT;
use App\Http\Requests\ClassAssessment\ClassAssessments;
use App\Models\Academics\AssessmentTypes;
use App\Models\Academics\ClassAssessment;
use App\Models\Academics\Classes;
use App\Models\Academics\CourseLevels;
use App\Models\Academics\Courses;
use App\Models\Academics\Programs;
use App\Models\Accounting\Invoice;
use App\Models\Admissions\PreActivation;
use App\Models\Admissions\ProgramCourses;
use App\Models\Admissions\UserProgram;
use App\Models\Enrollment;
use App\Models\GradeBook;
use App\Models\GradeBookImport;
use App\Models\Results\ImportClass;
use App\Models\Results\ImportList;
use App\Models\User;
use App\Repositories\Academicperiods;
use App\Repositories\AssessmentTypesRepo;
use App\Repositories\ClassAssessmentsRepo;
use App\Support\ClassEnrollment;
use App\Traits\Academics\BoardOfExaminer;
use App\Traits\User\General;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ClassAssessmentsController extends Controller
{
    use General;
    use BoardOfExaminer;

    /**
     * Display a listing of the resource.
     */
    protected $classaAsessmentRepo, $academic, $assessmentTypes;

    public function __construct(ClassAssessmentsRepo $classaAsessmentRepo, Academicperiods $academic, AssessmentTypesRepo $assessmentTypes)
    {
//        $this->middleware(TeamSA::class, ['except' => ['destroy','']]);
//        $this->middleware(TeamSAT::class, ['except' => ['destroy','']]);
//        $this->middleware(SuperAdmin::class, ['only' => ['destroy',]]);
        $this->middleware(TeamSAT::class, ['only' => ['destroy',]]);

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
            //->where('ac_assessmentTypes.id','=',1)
            ->select(
                'ac_academicPeriods.id AS academic_period_id',
                'ac_academicPeriods.code AS academic_period_code',
                'ac_courses.id AS course_id',
                'ac_courses.code AS course_code',
                'ac_courses.name AS course_name',
                'ac_classAssesments.id AS class_assessment_id',
                'ac_classAssesments.total',
                'ac_classAssesments.end_date',
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
                    'end_date' => $item->end_date,
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
        return view('pages.academics.class_assessments.index', ['academicPeriodsArray' => $academicPeriodsArray], $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['open'] = $this->academic->getAllopen();
        return view('pages.academics.class_assessments.show', $data);
    }

    public function ProcessUploadedResults(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|mimes:csv,excel,xls,xlsx|max:2048',
            'academic' => 'required',
            'programID' => 'required|integer',
            'instructor' => 'sometimes'
        ]);


        try {
            $academic = Qs::decodeHash($validatedData['academic']);
            $program = $validatedData['programID'];
            $isInstructor = $validatedData['instructor'] == 'instructorav' ? 1 : 0;

            $import = new ImportClass(); // Replace with your import class
            //$data = Excel::toCollection($import, $request->file('file'))[0]; // Get the data from the file

            $path = $request->file('file')->getRealPath();
            $data = Excel::toCollection('', $path, null, \Maatwebsite\Excel\Excel::TSV)[0];

            // Loop through the data and add two additional columns
            $processedData = $data->map(function ($row) use ($program, $academic) {
                // Add two additional columns with desired values
                $row['academicPeriodID'] = $academic;
                $row['programID'] = $program;

                return $row;
            });

            $data->forget(0);
            //$firstElement = $data->shift();
            foreach ($data as $row) {

                $academicPeriodID   = $academic;
                $studentID          = $row[0];    // Student ID
                $code               = $row[1];
                $title              = $row[2];
                $total              = $row[3];
                $programID          = $program;
                $key                = $code . '-' . $studentID . '-' . $academicPeriodID . '-' . $programID;

                $existingRow = GradeBookImport::where('key', $key)->get()->first();

                if (!empty($existingRow)) {
                    $existingRow->delete();
                }

                // check if user has registered for this academic period.
                $user = User::where('student_id', $studentID)->get()->first();
                if ($user) {
                    $lastEnrollment      = Enrollment::where('userID', $user->id)->get()->last();

                    if ($lastEnrollment) {

                        $lastEnrolledClass = Classes::where('id', $lastEnrollment->classID)->get()->first();
                       // dd($lastEnrollment);
                        if ($lastEnrolledClass) {
                            # Proceed to importing
                                # Add results to imports
                                $course = Courses::where('code', $code)->get()->first();
                                if ($course) {
                                    GradeBookImport::create([
                                        'programID'         => $program,
                                        'academicPeriodID'  => $academicPeriodID,
                                        'studentID'         => $studentID,
                                        'total'             => $total, // Total
                                        'title'             => $title,
                                        'code'              => $code,
                                        'key'               => $key,
                                    ]);
                                }
                        }
                    }
                }


//                GradeBookImport::create([
//                    'programID'         => $program,
//                    'academicPeriodID'  => $academicPeriodID,
//                    'studentID'         => $studentID,
//                    'total'             => $total, // Total
//                    'title'             => $title,
//                    'code'              => $code,
//                    'key'               => $key,
//                ]);
            }
            //$data->push($firstElement);
           // $data->prepend($firstElement);
            // Now, you have the data with two additional columns
            // You can choose to save this data or manipulate it further before saving

            // Example: Saving the processed data
            //ImportList::insert($processedData->toArray());
            $dataS = $this->prepareResultsview($validatedData['academic']);
            if ($isInstructor == 1) {

                return view('pages.academics.class_assessments.instructor_assessment.index', compact('data', 'dataS'), ['isInstructor' => $isInstructor])->with('flash_success', __('msg.get_ok')); // Pass the data to the view
            } else {

                //dd($data);
                return view('pages.academics.class_assessments.show', compact('data'))->with('flash_success', __('msg.retrieve_ok')); // Pass the data to the view
            }

        } catch (\Exception $e) {
            //return redirect()->route('dashboard')->with('error', 'Error importing data: ' . $e->getMessage());
            dd($e->getMessage());
        }
        /*
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:csv,excel,xls,xlsx|max:2048', // Adjust validation rules as needed
        ]);

        // Handle the uploaded file and import the data using your import class
        try {
            $import = new YourImportClass(); // Replace with your import class
            Excel::import($import, $request->file('file'));

            // Data import successful
            return redirect()->route('import.index')->with('success', 'Data imported successfully.');
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the import process
            return redirect()->route('import.index')->with('error', 'Error importing data: ' . $e->getMessage());
        }*/
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClassAssessments $req)
    {
        $data = $req->only(['assesmentID', 'classID', 'total', 'academic', 'end_date']);
        $classData = ClassAssessment::where('classID',$data['classID'])->get();
        $data['end_date'] = date('Y-m-d', strtotime($data['end_date']));
        $data['key'] = $data['classID'] . '-' . $data['academic'] . '-' . $data['assesmentID'];
        $totalValue = 0;
        foreach ($classData as $class){
            $totalValue = $totalValue + $class['total'];
        }
        $existingRecord = ClassAssessment::where([
            'assesmentID' => $data['assesmentID'],
            'classID' => $data['classID']
        ])->first();

        if ($existingRecord) {
            return Qs::json('Data already exists',false);
        }else{
            if ($totalValue > 100 || ($totalValue+$data['total'])>100){
                return Qs::json('Total for the assessment is greater than 100',false);
            }else{
                $this->classaAsessmentRepo->create($data);
                return Qs::jsonStoreOk();
            }
        }
        //dd($totalValue);

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

    public function getClassesToPublish($academic_id)
    {
        $academicPeriodID = Qs::decodeHash($academic_id);
        //self::getClasses()
        if (Auth::user()->user_type == 'instructor'){
           /* $apClasses = DB::table('ac_classes')->where('academicPeriodID', '=', $academicPeriodID)
                ->where('ac_classes.instructorID', '=', Auth::user()->id)
                ->select('code', 'name', 'ac_courses.id as courseID', 'ac_classes.id as id', 'academicPeriodID','users.first_name','users.last_name',
                    DB::raw('COUNT(ac_enrollments.id) as enrollment_count'))
                ->join('ac_courses', 'ac_courses.id', '=', 'ac_classes.courseID')
                ->join('users','users.id','=','ac_classes.instructorID')
                ->join('ac_enrollments','ac_enrollments.classID','=','ac_classes.id')
                ->groupBy('code', 'name', 'ac_courses.id', 'ac_classes.id', 'academicPeriodID', 'users.first_name', 'users.last_name')
                ->get();*/

            $grouped = DB::table('ac_classes')->where('academicPeriodID', '=', $academicPeriodID)
                ->where('ac_classes.instructorID', '=', Auth::user()->id)
                ->select('ac_courses.code', 'ac_courses.name', 'ac_courses.id as courseID', 'ac_classes.id as id', 'academicPeriodID','users.first_name',
                    'users.last_name','ac_assessmentTypes.name as assessTypeName','ac_assessmentTypes.id as assessTypeId',
                    DB::raw('COUNT(ac_enrollments.id) as enrollment_count'))
                ->join('ac_courses', 'ac_courses.id', '=', 'ac_classes.courseID')
                ->join('ac_classAssesments','ac_classes.id','=','ac_classAssesments.classID')
                ->join('ac_assessmentTypes','ac_assessmentTypes.id','=','ac_classAssesments.assesmentID')
                ->join('users','users.id','=','ac_classes.instructorID')
                ->join('ac_enrollments','ac_enrollments.classID','=','ac_classes.id')
                ->groupBy('ac_courses.code', 'ac_courses.name', 'ac_courses.id', 'ac_classes.id', 'academicPeriodID', 'users.first_name', 'users.last_name'
                    ,'ac_assessmentTypes.name','ac_assessmentTypes.id')
                ->get();

            $groupedApClasses = [];
            foreach ($grouped as $class) {
                $classId = $class->id;

                // Create an array for the class if it doesn't exist
                if (!isset($groupedApClasses[$classId])) {
                    $groupedApClasses[$classId] = [
                        'code' => $class->code,
                        'id' => $classId,
                        'name' => $class->name,
                        'courseID' => $class->courseID,
                        'academicPeriodID' => $class->academicPeriodID,
                        'first_name' => $class->first_name,
                        'last_name' => $class->last_name,
                        'enrollment_count' => $class->enrollment_count,
                        'assessments' => [],
                    ];
                }
                // Add assessment data to the class's assessments array
                $groupedApClasses[$classId]['assessments'][] = [
                    'assessTypeName' => $class->assessTypeName,
                    'assessTypeId' => $class->assessTypeId
                ];
            }

            // Convert the associative array to indexed array if needed
            $apClasses = array_values($groupedApClasses);

            $data['open'] = $this->academic->getAllopen();
            $data['infor'] = $this->academic->find($academicPeriodID);
            //dd($groupedApClasses);
            return view('pages.academics.class_assessments.show_classes', compact('apClasses'), $data);
        }else{
            $grouped = DB::table('ac_classes')->where('academicPeriodID', '=', $academicPeriodID)
                ->select('ac_courses.code', 'ac_courses.name', 'ac_courses.id as courseID', 'ac_classes.id as id', 'academicPeriodID','users.first_name',
                    'users.last_name','ac_assessmentTypes.name as assessTypeName','ac_assessmentTypes.id as assessTypeId',
                    DB::raw('COUNT(ac_enrollments.id) as enrollment_count'))
                ->join('ac_courses', 'ac_courses.id', '=', 'ac_classes.courseID')
                ->join('ac_classAssesments','ac_classes.id','=','ac_classAssesments.classID')
                ->join('ac_assessmentTypes','ac_assessmentTypes.id','=','ac_classAssesments.assesmentID')
                ->join('users','users.id','=','ac_classes.instructorID')
                ->join('ac_enrollments','ac_enrollments.classID','=','ac_classes.id')
                ->groupBy('ac_courses.code', 'ac_courses.name', 'ac_courses.id', 'ac_classes.id', 'academicPeriodID', 'users.first_name', 'users.last_name'
                    ,'ac_assessmentTypes.name','ac_assessmentTypes.id')
                ->get();
            $groupedApClasses = [];
            foreach ($grouped as $class) {
                $classId = $class->id;

                // Create an array for the class if it doesn't exist
                if (!isset($groupedApClasses[$classId])) {
                    $groupedApClasses[$classId] = [
                        'code' => $class->code,
                        'id' => $classId,
                        'name' => $class->name,
                        'courseID' => $class->courseID,
                        'academicPeriodID' => $class->academicPeriodID,
                        'first_name' => $class->first_name,
                        'last_name' => $class->last_name,
                        'enrollment_count' => $class->enrollment_count,
                        'assessments' => [],
                    ];
                }
                // Add assessment data to the class's assessments array
                $groupedApClasses[$classId]['assessments'][] = [
                    'assessTypeName' => $class->assessTypeName,
                    'assessTypeId' => $class->assessTypeId
                ];
            }

            // Convert the associative array to indexed array if needed
            $apClasses = array_values($groupedApClasses);
           // dd($apClasses);
            $data['open'] = $this->academic->getAllopen();
            $data['infor'] = $this->academic->find($academicPeriodID);
            //dd($dataS);
            return view('pages.academics.class_assessments.show_classes', compact('apClasses'), $data);
        }

    }
    public function StudentListResults($class,$assessid)
    {
        $class = Qs::decodeHash($class);
        $instructorID = Auth::user()->id;
        //if its instructor
        /*
        $academicPeriodsData = \App\Models\Academics\AcademicPeriods::where('id', $academicPeriodID)
            ->with(['classes' => function ($query) use ($instructorID) {
                $query->where('instructorID', $instructorID)
                    ->with(['course', 'enrollments.user']);
            }])->get();
        //class with students without instructor
        $academicPeriodsDatas = \App\Models\Academics\AcademicPeriods::where('id', $academicPeriodID)
            ->with(['classes' => function ($query) use ($instructorID) {
                $query->with(['course', 'enrollments.user']);
            }])->get();
        //get classes just
        $academicPeriodsDatas = DB::table('ac_classes')->where('academicPeriodID', '=', $academicPeriodID)
            ->select('code', 'name', 'ac_courses.id as courseID', 'ac_classes.id as id', 'academicPeriodID')
            ->join('ac_courses', 'ac_courses.id', '=', 'ac_classes.courseID')->get();
        //get students in that class


        //dd($academicPeriodsData);

        //modified
        /*

        //dd($dataS);
// Organize the data into the desired array structure
        $dataS = $academicPeriodsData->map(function ($academicPeriod) {
            return [
                'academicPeriodID' => $academicPeriod->id,
                'academicPeriodCode' => $academicPeriod->code,
                'classes' => $academicPeriod->classes->map(function ($class) use ($academicPeriod) {
                    return [
                        'classID' => $class->id,
                        'courseName' => $class->course->name,
                        'courseCode' => $class->course->code,
                        'instructor' => [
                            'instructorID' => $class->instructorID,
                            'instructorName' => $class->instructor->first_name . ' ' . $class->instructor->last_name,
                        ],
                        'students' => $class->enrollments->map(function ($enrollment) use ($class, $academicPeriod) {
                            return [
                                'userID' => $enrollment->user->id,
                                'student_id' => $enrollment->user->student_id,
                                'first_name' => $enrollment->user->first_name,
                                'last_name' => $enrollment->user->last_name,
                                'program' => self::getUserProgramID($enrollment->user->id),
                                'total' => $this->getcurrentTotalonImports($enrollment->user->student_id, $enrollment->user->id, $academicPeriod->id, $class->course->code)//to get this from imports table
                            ];
                        }),
                    ];
                }),
            ];
        })->toArray();
*/
// Return the final result
        $assessID = Qs::decodeHash($assessid);
        //dd($assessID);
        $academicPeriodsData = Classes::where('id', $class)->with(['course', 'enrollments.user','academicPeriod','assessments'])
            ->get();
        $assessment_total = ClassAssessment::where('classID',$class)->where('assesmentID',$assessID)->get()->first();
        $aseessname = AssessmentTypes::find($assessID);
        $class = $academicPeriodsData->map(function ($class) use ($assessment_total, $aseessname, $assessID) {
            return [
                'classID' => $class->id,
                'courseName' => $class->course->name,
                'courseCode' => $class->course->code,
                'assess_total' => $assessment_total->total,
                'assessmentId' => $assessID,
                'assessmentName' => $aseessname->name,
                'instructor' => [
                    'instructorID' => $class->instructorID,
                    'instructorName' => $class->instructor->first_name . ' ' . $class->instructor->last_name,
                ],
                    'apid' => $class->academicPeriodID,
                    'code' => $class->academicPeriod->code,
                'students' => $class->enrollments->map(function ($enrollment) use ($assessID, $class) {
                    return [
                        'userID' => $enrollment->user->id,
                        'student_id' => $enrollment->user->student_id,
                        'first_name' => $enrollment->user->first_name,
                        'last_name' => $enrollment->user->last_name,
                        'program' => self::getUserProgramID($enrollment->user->id),
                        'total' => $this->getcurrentTotalonImports($assessID,$enrollment->user->student_id, $enrollment->user->id, $class->academicPeriodID, $class->course->code)//to get this from imports table
                    ];
                }),
            ];
        })->toArray();
        $data['open'] = $this->academic->getAllopen();
        //dd($class);
        return view('pages.academics.class_assessments.instructor_assessment.index', compact('class'), $data);
    }

    public function getcurrentTotalonImports($assessID,$studeID, $id, $academic, $code)
    {
        //$studeID = 1913589;$id = 9;$academic = 29;$code ='BAC 1100';
        $program = self::getUserProgramID($id);
        $result = ImportList::where('academicPeriodID', $academic)->where('programID', $program)
            ->where('studentID', $studeID)->where('assessmentID', $assessID)->where('code', $code)->get()->last();
        //dd($result);
        if ($result) {
            return $result->total;
        } else {
            return 0;
        }
    }

    public function prepareResultsview($academic_id)
    {
        $academicPeriodID = Qs::decodeHash($academic_id);
        $instructorID = Auth::user()->id;
        $academicPeriodsData = \App\Models\Academics\AcademicPeriods::where('id', $academicPeriodID)
            ->with(['classes' => function ($query) use ($instructorID) {
                $query->where('instructorID', $instructorID)
                    ->with(['course', 'enrollments.user']);
            }])->get();

// Organize the data into the desired array structure
        $dataS = $academicPeriodsData->map(function ($academicPeriod) {
            return [
                'academicPeriodID' => $academicPeriod->id,
                'academicPeriodCode' => $academicPeriod->code,
                'classes' => $academicPeriod->classes->map(function ($class) use ($academicPeriod) {
                    return [
                        'classID' => $class->id,
                        'courseName' => $class->course->name,
                        'courseCode' => $class->course->code,
                        'instructor' => [
                            'instructorID' => $class->instructorID,
                            'instructorName' => $class->instructor->first_name . ' ' . $class->instructor->last_name,
                        ],
                        'students' => $class->enrollments->map(function ($enrollment) use ($class, $academicPeriod) {
                            return [
                                'userID' => $enrollment->user->id,
                                'student_id' => $enrollment->user->student_id,
                                'first_name' => $enrollment->user->first_name,
                                'last_name' => $enrollment->user->last_name,
                                'program' => self::getUserProgramID($enrollment->user->id),
                                'total' => $this->getcurrentTotalonImports($enrollment->user->student_id, $enrollment->user->id, $academicPeriod->id, $class->course->code)//to get this from imports table
                            ];
                        }),
                    ];
                }),
            ];
        })->toArray();
        return $dataS;
    }

    public function getClasses(string $id)
    {
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

    public function ProgramForResults(string $id)
    {
        $id = Qs::decodeHash($id);
        $programs = [];
        $apClasses = Classes::where('academicPeriodID', $id)->get();
        $totalStudentsEnrolled = 0;
        if ($apClasses->count() > 0) {
            foreach ($apClasses as $apClass) {
                $course_ids[] = $apClass->courseID;
            }

            $courses = ProgramCourses::whereIn('courseID', $course_ids)->get()->unique('programID');
            foreach ($courses as $course) {
                $program = Programs::data($course->programID, $id);
                unset($program['courses']);
                unset($program['levels']);
                unset($program['allowedStudyModes']);

                if (!empty($program['enrolledStudents']) && $program['enrolledStudents'] > 0) {
                    unset($program['enrolledStudents']);
                    $programs[] = $program;
                }
            }
        }
        // dd($programs);
        return $programs;
    }

    public function UpdateTotalResultsExams(Request $request, string $id)
    {
        $id = Qs::decodeHash($id);
        $total = $request->input('total');
        $data['total'] = $request->input('total');
        if ($request->input('end_date') !== null || !empty($request->input('end_date'))) {
            $data['end_date'] = date('Y-m-d', strtotime($request->input('end_date')));
            //$data['end_date'] = $request->input('end_date');
        }
        if ($request->input('total') !== null || !empty($request->input('total'))) {
            $data['total'] = $request->input('total');
        }
//        dd($id);
//        $updatedRows = DB::table('ac_classAssesments')
//            ->where('classID', '=', $specificClassID)
//            ->update(['total' => $newTotalValue]);
        if (count($data) > 0) {
            $this->classaAsessmentRepo->update($id, $data);
            return Qs::jsonUpdateOk();
        } else {
            return Qs::json('error failed update', false);
        }
    }
    public function LoadMoreResults(Request $request){
        $current_page = $request->input('current_page');
        $last_page = $request->input('last_page');
        $per_page = $request->input('per_page');

        $aid = $request->input('academic');
        $pid = $request->input('program');
        $leveid = $request->input('level_name');

//        $aid = $request->query('aid');
//        $pid = $request->query('pid');
//        $level = $request->query('level');
//        $aid = Qs::decodeHash($aid);
//        $pid = Qs::decodeHash($pid);
//        $level = Qs::decodeHash($level);
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
            ->paginate($per_page, ['*'], 'page', $current_page+1);
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
            ->whereIn('ac_gradebook_imports.studentID',$studentIds)
            ->get();

        $results = [];
        foreach ($grouped as $row) {

            $academicPeriod = $row->academicPeriod;
            $programId = $row->program_id;
            $academic=[
                'academic' => $row->academicPeriod,
                'program' => $row->program_id,
            ];
            if (!isset($results[$academic['academic']])) {
                $results[$academic['academic']] = [
                    'academic' => $row->academicPeriod,
                    'current_page'=> $groupedAta->currentPage(),
                    'last_page'=> $groupedAta->lastPage(),
                    'per_page'=> $groupedAta->perPage(),
                    'program' => $row->program_id,
                    'program_name' => $row->program_name,
                    'level_name' => $level_name->name,
                    'level_id' => $leveid,
                    'program_code' => $row->program_code,
                    'academicperiodname' =>$row->academicPeriodcode,
                    'students' => [],
                ];
            }
            $progression = self::checkProgression($row->userID, $row->program_id);
            $yearOfStudy = $progression['currentLevelName'];
            if ( $row->status == 1) {
                continue; // Skip this student
            }
            $studentId = $row->student_id;
            if (!isset($results[$academic['academic']]['students'][$studentId])) {
                $results[$academic['academic']]['students'][$studentId] = [
                    'name' => $row->first_name . ' ' . $row->last_name,
                    'student_id' => $studentId,
                    'level' => $yearOfStudy,
                    'courses' => [],
                    'classes'=> \App\Models\Academics\AcademicPeriods::myclasses($row->userID,$row->academicPeriod)
                ];
            }

            $courseCode = $row->code;
            if (!isset($results[$academic['academic']]['students'][$studentId]['courses'][$courseCode])) {
                $course = [
                    'code' => $row->code,
                    'title' => $row->title,
                    'CA' => 0,
                    'total' => 0,
                    'assessments' =>[],
                ];

                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode] = $course;
            }
            // Check if the course code is already present in the classes array and remove it
            foreach ($results[$academic['academic']]['students'][$studentId]['classes'] as $index => $class){
                if (isset($class['course_code']) &&  $class['course_code'] == $row->code) {
                    unset($results[$academic['academic']]['students'][$studentId]['classes'][$index]);
                }else{
                    $results[$academic['academic']]['students'][$studentId]['courses'][$class['course_code']] = [
                        'code' => $class['course_code'],
                        'title' => $class['course_name'],
                        'CA' => 0,
                        'total' => 0,
                        'assessments' =>[],
                    ];
                }
            }


            $assess = $row->assessment_name;
            if (!isset($results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['assessments'][$assess])) {
                $assessments = [
                    'total' => $row->total,
                    'id'=> $row->id,
                    'assessID' => $row->assessmentID,
                    'assessment_name' => $row->assessment_name,
                    'key' => $row->key,
                    'status' => $row->status,
                ];

                if ($row->assessment_name != 'Exam'){
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
                    $courseData['grade'] = $this->calculateGrade($totalScore);
                    $studentData['commentData'] = $this->calculateComment($studentData['courses']);
                }
            }
        }
       // dd($results);

         return $results;
    }
    public function BoardofExaminersUpdateResults(Request $request)
    {
        $requestData = $request->input('updatedAssessments'); // Get the request data
        //dd($requestData);
        // Loop through the data and insert or update each record
        //dd($request);
        /*
        if ($request->has('program')){
            $program = $request->input('program');
            foreach ($requestData as $item) {
                if ($item['total'] <= $item['outof']) {
                    ImportList::where([
                        'assessmentID' => $item['id'],
                        'code' => $item['code'],
                        'programID' => $program,
                        'academicPeriodID' => $item['apid']
                    ])->update([
                        'total' => $item['total'],
                    ]);
                    $msg =+' Course '.$item['code'].' updated successfully';
                }else{
                    $msg =+' Course '.$item['code']. ' '.$item['total'].' is greater than '.$item['outof'].' ';
                }
            }
            return Qs::json($msg, true);
        }else {
            foreach ($requestData as $item) {
                if ($item['total'] <= $item['outof']) {
                    ImportList::where([
                        'id' => $item['id'],
                        'key' => $item['key'],
                    ])->update([
                        'total' => $item['total'],
                    ]);
                    return Qs::json('updated successfully', true);
                }else{
                    $msg =+' Course '.$item['code']. ' '.$item['total'].' is greater than '.$item['outof'].' ';
                }
            }
            return Qs::json($msg, true);

        }*/
        //dd($requestData[0]['code']);
        $operation = $request->input('operation');
        $sOperation = ($operation==1 ? '+' : '-');
        if (isset($requestData[0]['code'])){
            $program = $request->input('program');
            $operation = $request->input('operation');
            $studentTotals = ImportList::where([
                'code' => $requestData[0]['code'],
                'programID' => $program,
                'academicPeriodID' => $requestData[0]['apid']
            ])->get();
            $studentTotals = ImportList::where([
                'code' => $requestData[0]['code'],
                'programID' => $program,
                'academicPeriodID' => $requestData[0]['apid']
            ])->select('studentID')->distinct()->get();

            $uniqueStudentIDs = $studentTotals->pluck('studentID')->toArray();

            // Create a unique array of student IDs
            $studentIDs = array_unique($uniqueStudentIDs);
//dd($studentIDs);
            foreach ($studentIDs as $studentID) {
                foreach ($requestData as $item) {
                    $currentTotal = ImportList::where([
                        'assessmentID' => $item['id'],
                        'code' => $item['code'],
                        'programID' => $program,
                        'academicPeriodID' => $item['apid'],
                        'studentID' => $studentID
                    ])->value('total');
                    if ($sOperation == '+') {
                        $newTotal = $currentTotal + $item['total'];
                    }else{
                        $newTotal = $currentTotal - $item['total'];
                    }

                    if ($newTotal > $item['outof']) {
                        $newTotal = $item['outof'];
                    }

                    ImportList::where([
                        'assessmentID' => $item['id'],
                        'code' => $item['code'],
                        'programID' => $program,
                        'academicPeriodID' => $item['apid'],
                        'studentID' => $studentID
                    ])->update([
                        'total' => $newTotal,
                    ]);
                }
            }
            return Qs::json('Marks updated successfully', true);
        }else {
            foreach ($requestData as $item) {

                $currentTotal = ImportList::where([
                    'id' => $item['id'],
                    'key' => $item['key'],
                ])->value('total');

                //$newTotal = $currentTotal + $item['total'];

                if ($sOperation == '+') {
                    $newTotal = $currentTotal + $item['total'];
                }else{
                    $newTotal = $currentTotal - $item['total'];
                }
                if ($newTotal > $item['outof']) {
                    $newTotal = $item['outof'];
                }
                ImportList::where([
                    'id' => $item['id'],
                    'key' => $item['key'],
                ])->update([
                    'total' => $newTotal,
                ]);

            }
            return Qs::json('Marks updated successfully', true);
        }
    }

    public function UpdateResultsPublish(Request $request, $id)
    {
        $id = Qs::decodeHash($id);
        $total = $request->input('total');
        if ($request->input('total') !== null || !empty($request->input('total'))) {
            $data['total'] = $request->input('total');
        }
        if (count($data) > 0) {
            $this->classaAsessmentRepo->updateImportlistResults($id, $data);
            return Qs::jsonUpdateOk();
        } else {
            return Qs::json('error failed update', false);
        }
    }

    public function PostStudentResults(Request $request)
    {
        //$id = Qs::decodeHash($id);
        $data['academicPeriodID']  = $request->input('academicPeriodID');
        $data['programID']  = $request->input('programID');
        $data['studentID']  = Qs::decodeHash($request->input('studentID'));
        $data['code']  = $request->input('code');
        $data['title']  = $request->input('title');
        $data['total']  = $request->input('total');
        $data['assessmentID'] = $request->input('type');
        $Suserid = $request->input('userID');
        $progression = self::checkProgression($Suserid,  $data['programID']);
        $data['student_level_id'] = $progression['currentLevelId'];

        $data['key']  = $data['code'].'-'.$data['studentID'].'-'.$data['academicPeriodID'].'-'.$data['programID'].'-'.$data['assessmentID'].'-'.$data['student_level_id'];

        $id = $request->input('id');
        //$data['total'] = $request->input('total');
        $classData = ClassAssessment::where('classID',$id)->where('assesmentID',$data['assessmentID'])->get();
        $dateToCheck = Carbon::parse($classData[0]['end_date']);
        $currentDate = Carbon::now();

        if ($classData && $dateToCheck->isPast()) {
           return Qs::json('The date has passed hence marks not updated',false);
        }elseif ($classData && $data['total'] > $classData[0]['total']){
            return Qs::json('The total assessment mark can not be greater than the set total for the class',false);
        }
        else{
           // dd($dudate[0]['end_date']);
            //dd($dudate[0]['end_date']);
//            GradeBookImport::create([
//                'programID'         => $data['programID'],
//                'academicPeriodID'  => $data['academicPeriodID'],
//                'studentID'         => $data['studentID'],
//                'total'             => $data['total'], // Total
//                'title'             => $data['title'],
//                'code'              => $data['code'],
//                'key'               => $data['key'] ,
//                'assessmentID'      => $data['assessmentID']
//            ]);

            GradeBookImport::updateOrInsert(
                [
                    'programID'        => $data['programID'],
                    'academicPeriodID' => $data['academicPeriodID'],
                    'studentID'        => $data['studentID'],
                    'assessmentID'     => $data['assessmentID'],
                    'title'            => $data['title'],
                    'code'             => $data['code'],
                    'key'              => $data['key'],
                ],
                [
                    'total'            => $data['total'],
                    'status'             => 0,
                    'student_level_id'  => $data['student_level_id'],
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]
            );
            return Qs::jsonStoreOk();
        }
/*
        $existingData = ImportList::where('academicPeriodID', $data['academicPeriodID'])
            ->where('programID', $data['programID'])
            ->where('studentID', $data['studentID'])
            ->where('code', $data['code'])
            ->where('title', $data['title'])
            ->first();
        dd($data);
        if (count($data) > 0 && $existingData) {
            $this->classaAsessmentRepo->addResults($data);
            return Qs::jsonStoreOk();
        } else {
            return Qs::json('error', 'Okay', ['message' => 'failed create']);
        }*/
    }

    public function GetProgramsToPublish(string $id)
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
            ->groupBy('ac_programs.id', 'ac_programs.name', 'ac_programs.code','ac_code','status','level_id','level_name')
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
        return view('pages.academics.class_assessments.edit', compact('programs'), $academic);
    }

    public function GetProgramResults($aid, $pid)
    {
        $aid = Qs::decodeHash($aid);
        $pid = Qs::decodeHash($pid);
        //dd($pid);


        $results = DB::table('ac_gradebook_imports')
            ->join('users', 'users.student_id', '=', 'ac_gradebook_imports.studentID')
            ->join('ac_assessmentTypes','ac_assessmentTypes.id','=','ac_gradebook_imports.assessmentID')
            ->select(
                'ac_gradebook_imports.academicPeriodID',
                'ac_gradebook_imports.programID',
                'users.first_name',
                'users.last_name',
                'ac_gradebook_imports.code',
                'ac_gradebook_imports.title',
                'ac_gradebook_imports.total',
                'ac_gradebook_imports.studentID',
                'ac_gradebook_imports.id',
                'ac_gradebook_imports.assessmentID',
                'ac_assessmentTypes.name',
                'ac_gradebook_imports.status'
            )
            ->where('ac_gradebook_imports.academicPeriodID', '=', $aid)
            ->where('ac_gradebook_imports.programID', '=', $pid)
            ->get();

        //dd($results);
        return view('pages.academics.class_assessments.update_marks', compact('results'));
    }
    public function getAssessToUpdate(Request $request){

        $aid = $request->input('academicPeriodID');
        $pid = $request->input('programID');
        $code = $request->input('code');
        //$aid = Qs::decodeHash($aid);
        //$pid = Qs::decodeHash($pid);
        if (!$request->has('studentID')) {
            $academicPeriods = DB::table('ac_academicPeriods')
                ->where('ac_academicPeriods.id', '=', $aid)
                ->where('ac_courses.code', '=', $code)
                ->join('ac_classes', 'ac_academicPeriods.id', '=', 'ac_classes.academicPeriodID')
                ->join('ac_courses', 'ac_classes.courseID', '=', 'ac_courses.id')
                ->join('ac_classAssesments', 'ac_classes.id', '=', 'ac_classAssesments.classID')
                ->join('ac_assessmentTypes', 'ac_assessmentTypes.id', '=', 'ac_classAssesments.assesmentID')
                ->select(
                    'ac_academicPeriods.id AS academic_period_id',
                    'ac_academicPeriods.code AS academic_period_code',
                    'ac_courses.id AS course_id',
                    'ac_courses.code AS code',
                    'ac_courses.name AS name',
                    'ac_classAssesments.id AS class_assessment_id',
                    'ac_classAssesments.total',
                    'ac_assessmentTypes.name AS assessment_type_name',
                    'ac_assessmentTypes.id AS assessment_type_id'
                )
                ->get()
                //->groupBy('class_assessment_id')
                ->toArray(); // Convert the result to an array

// Create an array where 'classAssessments' is the key
            $classAssessments = [];

            foreach ($academicPeriods as $classAssessmentId => $details) {
                $classAssessments[$classAssessmentId] = $details;
            }
           // dd($classAssessments);
            return $classAssessments;
        } else {

            $studentID = $request->input('studentID');
            //$studentID = Qs::decodeHash($studentId);
            $course = Courses::where('code',$code)->get()->first();
            $classess = Classes::where('academicPeriodID',$aid)->where('courseID',$course->id)->get()->first();
            $grouped['assess'] = ClassAssessment::where('classID',$classess->id)->get();

            $assessmentIDs = ImportList::where('studentID',$studentID)->where('academicPeriodID',$aid)->where('programID',$pid)->where('code',$code)->pluck('assessmentID');

            $grouped = DB::table('ac_gradebook_imports')
                ->join('users', 'users.student_id', '=', 'ac_gradebook_imports.studentID')
                ->join('ac_assessmentTypes', 'ac_assessmentTypes.id', '=', 'ac_gradebook_imports.assessmentID')
                ->select(
                    'users.first_name as first_name',
                    'users.last_name as last_name',
                    'ac_gradebook_imports.code as code',
                    'ac_gradebook_imports.title as title',
                    'ac_gradebook_imports.total as marks',
                    'ac_gradebook_imports.studentID as student_id',
                    'ac_gradebook_imports.id as id',
                    'ac_gradebook_imports.assessmentID as assessmentID',
                    'ac_assessmentTypes.name as assessment_name',
                    'ac_gradebook_imports.key as key',
                    'ac_gradebook_imports.status as status',
                )
                ->where('ac_gradebook_imports.academicPeriodID', '=', $aid)
                ->where('ac_gradebook_imports.code', '=', $code)
                ->where('ac_gradebook_imports.programID', '=', $pid)
                ->where('ac_gradebook_imports.studentID', '=', $studentID)
                ->get();

            foreach ($grouped as $item) {
                foreach ($assessmentIDs as $assessmentID) {
                    if ($item->assessmentID === $assessmentID) {
                        // Add the value here
                        $total = ClassAssessment::where('classID',$classess->id)->where('assesmentID',$assessmentID)->get()->first();
                        $item->total = $total->total;
                    }
                }
            }

        }
        //dd($grouped);


        return $grouped;
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
            ->whereIn('ac_gradebook_imports.studentID',$studentIds)
            ->get();
            //->paginate(5);
            //->paginate(4, ['*'], 'page', 1);
        $results = [];
        foreach ($grouped as $row) {

            $academicPeriod = $row->academicPeriod;
            $programId = $row->program_id;
            $academic=[
                'academic' => $row->academicPeriod,
                'program' => $row->program_id,
            ];
            if (!isset($results[$academic['academic']])) {
                $results[$academic['academic']] = [
                    'academic' => $row->academicPeriod,
                    'current_page'=> $groupedAta->currentPage(),
                    'last_page'=> $groupedAta->lastPage(),
                    'per_page'=> $groupedAta->perPage(),
                    'program' => $row->program_id,
                    'program_name' => $row->program_name,
                    'level_name' => $level_name->name,
                    'level_id' => $level,
                    'program_code' => $row->program_code,
                    'academicperiodname' =>$row->academicPeriodcode,
                    'total_students' => $totalStudents,
                    'students' => [],
                ];
            }
            if ( $row->status == 1) {
                continue; // Skip this student
            }
            $studentId = $row->student_id;
            if (!isset($results[$academic['academic']]['students'][$studentId])) {
                $results[$academic['academic']]['students'][$studentId] = [
                    'name' => $row->first_name . ' ' . $row->last_name,
                    'student_id' => $studentId,
                    'level' => $level_name->name,
                    'courses' => [],
                    'classes'=> \App\Models\Academics\AcademicPeriods::myclasses($row->userID,$row->academicPeriod)
                ];
            }

            $courseCode = $row->code;
            if (!isset($results[$academic['academic']]['students'][$studentId]['courses'][$courseCode])) {
                $course = [
                    'code' => $row->code,
                    'title' => $row->title,
                    'CA' => 0,
                    'total' => 0,
                    'assessments' =>[],
                ];

                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode] = $course;
            }
            // Check if the course code is already present in the classes array and remove it
            foreach ($results[$academic['academic']]['students'][$studentId]['classes'] as $index => $class){
                if (isset($class['course_code']) &&  $class['course_code'] == $row->code) {
                    unset($results[$academic['academic']]['students'][$studentId]['classes'][$index]);
                }else{
                    $results[$academic['academic']]['students'][$studentId]['courses'][$class['course_code']] = [
                        'code' => $class['course_code'],
                        'title' => $class['course_name'],
                        'CA' => 0,
                        'total' => 0,
                        'assessments' =>[],
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

                if ($row->assessment_name != 'Exam'){
                    $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['CA'] += $row->total;
                }
                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['total'] += $row->total;
                //$results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['total'] += $row->total;
                //$results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['CA'] += $row->total;

                $results[$academic['academic']]['students'][$studentId]['courses'][$courseCode]['assessments'][$assess] = $assessments;
            }
        }
        foreach ($results as &$academicPeriodData) {
            foreach ($academicPeriodData['students'] as &$studentData) {
                foreach ($studentData['courses'] as &$courseData) {
                    $totalScore = $courseData['total'];
                    $courseData['grade'] = $this->calculateGrade($totalScore);
                    $studentData['commentData'] = $this->calculateComment($studentData['courses']);
                }
            }
        }

        //dd($results);
        return view('pages.academics.class_assessments.results_review_board', compact('results'));
        //return view('pages.academics.class_assessments.update_marks', compact('results'));
    }
    public static function calculateComment($courses) {
        $comment = '';

        $courseCount = count($courses);
        $passedCourse = 0;
        $failedCourse = 0;
        $coursesPassedArray = [];
        $courseFailedArray = [];

        foreach ($courses as $course) {
            if ($course['total'] >= 50 || $course['total'] == -1) {
                $passedCourse++;
                $coursesPassedArray[] = $course;
            } else {
                $failedCourse++;
                $courseFailedArray[] = $course;
            }
        }

        if ($courseCount == $failedCourse) {
            $coursesToRepeat = implode(", ", array_column($courseFailedArray, 'code'));
            //$comment = 'Part Time';
            $comment = 'Part Time ' . $coursesToRepeat;
        } elseif ($courseCount == $passedCourse) {
            $comment = 'Clear Pass';
        } elseif ($courseCount - 1 == $passedCourse || $courseCount - 2 == $passedCourse) {
            $coursesToRepeat = implode(", ", array_column($courseFailedArray, 'code'));
            $comment = 'Proceed, RPT ' . $coursesToRepeat;
        } elseif ($courseCount - 3 <= $passedCourse) {
            $coursesToRepeat = implode(", ", array_column($courseFailedArray, 'code'));
            $comment = 'Part Time ' . $coursesToRepeat;
        }

//        return [
//            'comment'            => $comment,
//            'coursesPassed'      => $coursesPassedArray,
//            'coursesPassedCount' => $passedCourse,
//            'coursesFailed'      => $courseFailedArray,
//            'coursesFailedCount' => $failedCourse,
//        ];
        return $comment;
    }


    public static function calculateGrade($total)
    {
        // Define your grade thresholds and corresponding values here
        if ($total == 0) {
            return 'Not Examined';
        } else if ($total == -1) {
            return 'Exempted';
        } else if ($total == -2) {
            return 'Withdrew with Permission';
        } else if ($total == -3) {
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
    public function PublishProgramResults(Request $request)
    {
        /**
         * Error Key for published
         * -1 = Course Not enrolled
         * -2 = Student never been enrolled
         */
        //dd($request);
        $student_id = $request->input('ids');
       // dd($student_id);
        $programID = $request->input('programID');
        $academicPeriodID = $request->input('academicPeriodID');
        $importedGrades = GradeBookImport::whereIn('studentID',$request->ids)->where('programID', $programID)->where('academicPeriodID', $academicPeriodID)->get();
        //dd($importedGrades);
        foreach ($importedGrades as $importedGrade) {

            // check if user has registered for this academic period.
            $user = User::where('student_id', $importedGrade->studentID)->get()->first();
            if ($user) {
                $lastEnrollment      = Enrollment::where('userID', $user->id)->get()->last();

//                if (empty($lastEnrollment)) {
//                    // check pre activation courses for course
//                    $preactivation = PreActivation::where('userID', $user->id)->get()->first();
//                    // Check preactivation classes and turn then into courses
//                    if ($preactivation && $preactivation->classes) {
//                        foreach ($preactivation->classes as $activationClass) {
//                            $class       = Classes::where('id', $activationClass->classID)->get()->first();
//
//                            if ($class && $class->id) {
//                                $course = Courses::find($class->courseID);
//                            }
//                            if (empty($course)) {
//                                $course = [];
//                            } else {
//                                $courses[]   = $course;
//                            }
//                        }
//
//
//                        ClassEnrollment::enroll($user->id, $preactivation->academicPeriodID, $courses);
//                        $importedGrade->published = 0;
//                        $importedGrade->save();
//                    }
//                    // Check if user has been invoiced and invoice has apid
//                    $hasInvoice = Invoice::where('user_id', $user->id)->where('academicPeriodID', $academicPeriodID)->get()->first();
//                    if ($hasInvoice) {
//                        # Check imported grades for last import with this student id and academic period id
//                        # then check running classes in this academic period with the same class / course code.
//                        $lastGradeImport = GradeBookImport::where('studentID', $user->student_id)->where('programID', $programID)->where('academicPeriodID', request('academicPeriodID'))->get()->last();
//
//                        if ($lastGradeImport) {
//                            // check running classes
//
//                            $course = Courses::where('code', $lastGradeImport->code)->get()->first();
//                            if ($course) {
//
//                                $runningClass = Classes::where('courseID', $course->id)->where('academicPeriodID', request('academicPeriodID'))->get()->first();
//
//                                if ($runningClass) {
//
//                                    # enroll user in this course
//                                    $existingEnrollment = Enrollment::where('userID', $user->id)->where('classID', $runningClass->id)->get()->first();
//
//                                    if (empty($existingEnrollment)) {
//                                        Enrollment::create([
//                                            'userID'     => $user->id,
//                                            'classID'       => $runningClass->id,
//                                            'key'           => $user->id . '-' . $runningClass->id,
//                                        ]);
//                                    }
//
//
//                                    // get last enrollment
//                                    $lastEnrollment      = Enrollment::where('userID', $user->id)->get()->last();
//                                } else {
//
//                                    // create class and allocate instructor
//                                    Classes::create([
//                                        'courseID'           => $course->id,
//                                        'academicPeriodID'   => $lastGradeImport->academicPeriodID,
//                                        'instructorID'       => 8927,
//                                        'key'                => $course->id . '' . $lastGradeImport->academicPeriodID,
//                                    ]);
//
//                                    $runningClass = Classes::get()->last();
//
//                                    if ($runningClass) {
//                                        # enroll user in this course
//                                        $existingEnrollment = Enrollment::where('userID', $user->id)->where('classID', $runningClass->id)->get()->first();
//
//                                        if (empty($existingEnrollment)) {
//                                            Enrollment::create([
//                                                'userID'     => $user->id,
//                                                'classID'       => $runningClass->id,
//                                                'key'           => $user->id . '-' . $runningClass->id,
//                                            ]);
//                                        }
//                                        // get last enrollment
//                                        $lastEnrollment      = Enrollment::where('userID', $user->id)->get()->last();
//                                    }
//                                }
//                            } else {
//
//                                $lastGradeImport = GradeBookImport::where('studentID', $user->student_id)->where('programID', request('programID'))->where('academicPeriodID', request('academicPeriodID'))->get()->first();
//
//                                if ($lastGradeImport) {
//                                    // check running classes
//                                    $course = Courses::where('code', $lastGradeImport->code)->get()->first();
//                                    if ($course) {
//                                        $runningClass = Classes::where('courseID', $course->id)->where('academicPeriodID', request('academicPeriodID'))->get()->first();
//                                        if ($runningClass) {
//                                            # enroll user in this course
//                                            $existingEnrollment = Enrollment::where('userID', $user->id)->where('classID', $runningClass->id)->get()->first();
//
//                                            if (empty($existingEnrollment)) {
//                                                Enrollment::create([
//                                                    'userID'     => $user->id,
//                                                    'classID'       => $runningClass->id,
//                                                    'key'           => $user->id . '-' . $runningClass->id,
//                                                ]);
//                                            }
//
//                                            // get last enrollment
//                                            $lastEnrollment      = Enrollment::where('userID', $user->id)->get()->last();
//                                        } else {
//
//                                            // create class and allocate instructor
//                                            Classes::create([
//                                                'courseID'           => $course->id,
//                                                'academicPeriodID'   => $lastGradeImport->academicPeriodID,
//                                                'instructorID'       => 8927,
//                                                'key'                => $course->id . '' . $lastGradeImport->academicPeriodID,
//                                            ]);
//
//                                            $runningClass = Classes::get()->last();
//
//                                            if ($runningClass) {
//                                                # enroll user in this course
//                                                $existingEnrollment = Enrollment::where('userID', $user->id)->where('classID', $runningClass->id)->get()->first();
//
//                                                if (empty($existingEnrollment)) {
//                                                    Enrollment::create([
//                                                        'userID'     => $user->id,
//                                                        'classID'       => $runningClass->id,
//                                                        'key'           => $user->id . '-' . $runningClass->id,
//                                                    ]);
//                                                }
//                                                // get last enrollment
//                                                $lastEnrollment      = Enrollment::where('userID', $user->id)->get()->last();
//                                            }
//                                        }
//                                    }
//                                } else {
//                                    $course = Courses::where('code', $lastGradeImport->code)->get()->first();
//                                    if ($course) {
//                                        $runningClass = Classes::where('courseID', $course->id)->where('academicPeriodID', request('academicPeriodID'))->get()->first();
//                                        if ($runningClass) {
//                                            # enroll user in this course
//                                            $existingEnrollment = Enrollment::where('userID', $user->id)->where('classID', $runningClass->id)->get()->first();
//
//                                            if (empty($existingEnrollment)) {
//                                                Enrollment::create([
//                                                    'userID'     => $user->id,
//                                                    'classID'       => $runningClass->id,
//                                                    'key'           => $user->id . '-' . $runningClass->id,
//                                                ]);
//                                            }
//
//                                            // get last enrollment
//                                            $lastEnrollment      = Enrollment::where('userID', $user->id)->get()->last();
//                                        }
//                                    }
//                                }
//                            } //  end
//
//                        }
//                    }
//                }

                if ($lastEnrollment) {

                    $lastEnrolledClass   = Classes::where('id', $lastEnrollment->classID)->get()->first();
                    /*if ($lastEnrolledClass && $lastEnrolledClass->academicPeriodID == request('academicPeriodID')) {*/
                    if ($lastEnrolledClass) {
                        # Proceed to importing

                        // Check if course can be published
                        $status = self::courseClearance($importedGrade->code, $academicPeriodID);

                        if ($status == 1) {

                            # Add results to gradebook
                            $course = Courses::where('code', $importedGrade->code)->get()->first();
                            if ($course) {

                                $class = Classes::where('courseID', $course->id)->where('academicPeriodID', $academicPeriodID)->get()->first();
                                // check if class has assesments
                                $assesment = ClassAssessment::where('classID', $class->id)->get()->first();
                                // Update GradeBook
                                $gradeBook = GradeBook::where('key', $user->id . '-' . $assesment->id . '' . '1')->get()->first();
                                //$gradeBook = GradeBook::where('key', $user->id . '-' . $class->id. '-' . '1')->get()->first();

                                if (empty($gradeBook)) {
                                    $gradeBook                      = new GradeBook();
                                    $gradeBook->userID              = $user->id;
                                    $gradeBook->grade               = $importedGrade->total;
                                    $gradeBook->classAssessmentID   = $assesment->id;
                                    //$gradeBook->classAssessmentID   = 1;
                                    $gradeBook->key                 = $user->id . '-' . $assesment->id . '' . '1';
                                    //$gradeBook->key                 = $user->id . '-' . $class->id. '-' . '1';
                                    $gradeBook->save();
                                } else {
                                    $gradeBook->grade               = ($gradeBook->grade + $importedGrade->total);
                                    $gradeBook->save();
                                }

                                // check if for enrollment
                                $_enrollment = Enrollment::where('key', $user->id . '-' . $class->id)->get()->first();
                            /*
                                if (empty($_enrollment)) {
                                    Enrollment::create([
                                        'userID'    => $user->id,
                                        'classID'   => $class->id,
                                        'key'       => $user->id . '-' . $class->id,
                                    ]);
                                }*/

                                $importedGrade->published = 1;
                                $importedGrade->status = 1;
                                $importedGrade->processed_by = Auth::user()->id;
                                $importedGrade->save();
                                $studentIds = $request->ids; // Assuming $request->ids is an array of student IDs
                                GradeBookImport::whereIn('studentID', $studentIds)->where('programID', $programID)->where('academicPeriodID', $academicPeriodID)
                                    ->update([
                                        'status' => 1,
                                        'published' => 1,
                                        'processed_by' => Auth::user()->id,
                                    ]);

//                                foreach ($studentIds as $studentId) {
//                                    GradeBookImport::updateOrInsert(
//                                        [
//                                            'programID' => $programID,
//                                            'academicPeriodID' => $academicPeriodID,
//                                            'studentID' => $studentId
//                                        ],
//                                        [
//                                            'status' => 1,
//                                            'published' => 1,
//                                            'processed_by' => Auth::user()->id,
//                                        ]
//                                    );
//                                }

                                //return Qs::json($importedGrade,true);

                            }
                            return Qs::json('Results published successfully',true);
                        } // ends status
                        // drop courses without gradebook
                        else {
                            $importedGrade->published = -1;
                            return Qs::json('Error while publishing results try again',false);
                        }
                    }
                } else {

                    $importedGrade->published = -2;
                    $importedGrade->save();
                    return Qs::json('Error while publishing results try again',false);
                }
            }
        }

    }

}
