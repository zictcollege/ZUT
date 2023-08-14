<?php

namespace App\Models\Academics;

use App\Http\Requests\ProgramCourses\ProgramCourse;
use App\Models\Accounting\Fee;
use App\Models\Admissions\ProgramCourses;
use App\Models\Admissions\UserProgram;
use App\Models\Admissions\UserStudyModes;
use App\Models\Enrollment;
use App\Models\EnrolmentSummary;
use App\Models\ExamRegistraion;
use App\Models\PeriodFees;
use App\Models\User;
use App\Support\General;
use App\Support\Progression;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\academicPeriods
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicPeriods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicPeriods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicPeriods query()
 * @mixin \Eloquent
 */
class AcademicPeriods extends Model
{
    use HasFactory;
    protected $table = 'ac_academicPeriods';
    protected $fillable = ['code', 'registrationDate', 'lateRegistrationDate', 'acStartDate', 'acEndDate', 'periodID', 'resultsThreshold', 'registrationThreshold', 'type', 'examSlipThreshold', 'studyModeIDAllowed'];

    public function periodType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(period_types::class, 'type');
    }
    public function period()
    {
        return $this->hasOne(Period::class, 'id', 'periodID');
    }
    public function studyMode(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(studyModes::class, 'studyModeIDAllowed');
    }
    public function intake(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Intakes::class, 'intakeID');
    }
    public function classes()
    {
        return $this->hasMany(Classes::class, 'academicPeriodID');
    }

    public function Periodfees()
    {
        return $this->hasMany(PeriodFees::class, 'academicPeriodID');
    }


    public static function dataMini($id)
    {

        $ap = AcademicPeriods::find($id);
        $_fees  = PeriodFees::where('academicPeriodID', $ap->id)->where('class_id', null)->get();

        if ($_fees) {
            foreach ($_fees as $fee) {
                $_fee       = Fee::data($fee->feeID, $ap->id);
                $fees[]     = $_fee;
            }
        }
        if (empty($fees)) {
            $fees = [];
        }

        if (strtotime($ap->acEndDate) > strtotime(date("Y-m-d"))) {
            $status = 'Open';
        } else {
            $status = 'Closed';
        }

        $aPeriod = [
            'key'                   => $ap->id,
            'id'                    => $ap->id,
            'code'                  => $ap->code,
            'name'                  => $ap->period->type,
            'acStartDate'           => $ap->acStartDate,
            'acEndDate'             => $ap->acEndDate,
            'registrationDate'      => $ap->registrationDate,
            'lateRegistrationDate'  => $ap->lateRegistrationDate,
            'fees'                  => $fees,
            'status'                => $status,
            'type_int'              => $ap->type,
        ];
        return $aPeriod;
    }

    public static function data($id, $request_type = 1, $user_id = 0, $is_degree_student = 0, $programID = 0)
    {
        #$academicPeriod    = AcademicPeriod::data(request('academicPeriodID'), 0, request('userid'), $is_degree_student);


        $ap  = AcademicPeriods::find($id);

        $oldTuitionFee = 0;

        if ($ap->type ==  0) {
            $type  = "Academic";
        } else {
            $type  = "Professional";
        }

        if ($ap->type == 1) {
            # Find the available class fees
            $_classFees  = PeriodFees::where('academicPeriodID', $ap->id)->where('class_id', '!=', null)->get();

            if ($_classFees) {
                foreach ($_classFees as $cfee) {
                    $classesFees[]   = Fee::data($cfee->feeID, $ap->id, $cfee->class_id);
                }
            }

            if (empty($classesFees)) {
                $classesFees = [];
            }
        } else {
            $classesFees = [];
        }


        if ($request_type == 1) {

            $_classes = $ap->classes;

            foreach ($_classes as $_class) {
                $class     = Classes::data($_class->id);
                $classes[] = $class;
            }
            if (empty($classes)) {
                $classes = [];
            }

            # Find the programs running
            # Check for classes that are running and attached to the selected academic period
            $apClasses          = Classes::where('academicPeriodID', $ap->id)->get();
            $totalStudentsEnrolled = 0;
            if ($apClasses->count() > 0) {
                foreach ($apClasses as $apClass) {
                    $course_ids[] = $apClass->courseID;
                }

                $courses         = ProgramCourses::whereIn('courseID', $course_ids)->get()->unique('programID');
                foreach ($courses as $course) {
                    $program     = Programs::data($course->programID, $ap->id);
                    if (!empty($program['enrolledStudents']) && $program['enrolledStudents'] > 0) {
                        $totalStudentsEnrolled = $totalStudentsEnrolled + $program['enrolledStudents'];
                        $programs[]  = $program;
                    }
                }
            }
            if (empty($programs)) {
                $programs = [];
            }


            $_fees  = PeriodFees::where('academicPeriodID', $ap->id)->where('class_id', NULL)->get();

            if ($_fees) {
                foreach ($_fees as $fee) {

                    $_fee       = Fee::data($fee->feeID, $ap->id);
                    $fees[]     = $_fee;
                }
            }

            if (empty($fees)) {
                $fees = [];
            }




            $aPeriod = [
                'key'                   => $ap->id,
                'code'                  => $ap->code,
                'name'                  => $ap->period->type,
                'acStartDate'           => $ap->acStartDate,
                'acEndDate'             => $ap->acEndDate,
                'registrationDate'      => $ap->registrationDate,
                'lateRegistrationDate'  => $ap->lateRegistrationDate,
                'programs'              => $programs,
                'classes'               => $classes,
                'fees'                  => $fees,
                'type'                  => $type,
                'classesFees'           => $classesFees,
                'type_int'              => $ap->type,
                'totalStudentsEnrolled' => $totalStudentsEnrolled,
            ];
            return $aPeriod;
        } else {


            # If the request has degree Student remove the highest fee from the current fees attached to this academic period and place in the highest fee
            # from the users first ever academic period to track the first tuition fee that was charged.
            $userStudyMode          = UserStudyModes::where('userID', $user_id)->first();
            $firstEnrollment        = Enrollment::where('userID', $user_id)->get();

            $myOldEnrollments = Enrollment::where('userID', $user_id)->get();
            $apIDs = [];
            foreach ($myOldEnrollments as $oldEnrollment) {
                $_ap = AcademicPeriods::find($oldEnrollment->class->academicPeriodID);
                $apIDs[] = $_ap->id;
            }
            $date = AcademicPeriods::whereIn('id', $apIDs)->min('acStartDate');

            $minAP = AcademicPeriods::where('acStartDate', $date)->where('studyModeIDAllowed', $userStudyMode->studyModeID)->get()->first();

            $oldAcademicPeriodFees = [];
            if ($minAP && !empty($minAP->id)) {
                $oldAcademicPeriodFees  = PeriodFees::where('academicPeriodID', $ap->id)->where('studyModeID', $userStudyMode->studyModeID)->get();

            }

            if (!empty($oldAcademicPeriodFees)) {
                foreach ($oldAcademicPeriodFees as $oldFee) {

                    $fee_old = Fee::data($oldFee->feeID, $ap->id);
                    //$fee_old = Fee::data($oldFee->feeID, $_ap->id);
                    if(!empty($fee_old )){
                        if ($fee_old['is_tuition'] == 1 && $fee_old['crf'] == 0) {
                            $oldTuitionFee = $fee_old;
                        }
                    }
                }
                if (empty($oldTuitionFee)) {
                    $oldTuitionFee = [];
                }
            }



            # Lets get the highest fee

            if (empty($user_id) || $user_id == 0) {
                $new_fees  = PeriodFees::where('academicPeriodID', $ap->id)->get();

                if ($new_fees) {
                    foreach ($new_fees as $fee) {

                        $_fee       = Fee::data($fee->feeID, $ap->id, $user_id);

                        if ($_fee['is_tuition'] == 1) {
                            $newTuitionFee  = $_fee;
                        } else {

                            if ($user_id > 0) {
                                if ($_fee['once_off'] == "Recurring Fee") {
                                    $fees[]         = $_fee;
                                }
                            } else {
                                $fees[]         = $_fee;
                            }
                        }
                    }
                }
                if (empty($fees)) {
                    $fees = [];
                }
            }

            $canViewResults = 'No';

            if ($user_id > 0) {

                // check if student can view results
                $enrollments = Enrollment::where('userID', $user_id)->get();

                foreach ($enrollments as $enrollment) {
                    $classIDs[] = $enrollment->classID;
                }
                $studentsClasses = Classes::wherein('id', $classIDs)->where('academicPeriodID', '!=', $id)->get()->unique('academicPeriodID');

                foreach ($studentsClasses as $sClass) {
                    $tempAP = AcademicPeriods::find($sClass->academicPeriodID);
                    if (strtotime($tempAP->acStartDate) > strtotime($ap->acStartDate)) {
                        $canViewResults = 'Yes';
                    } else {
                        $canViewResults = 'No';
                    }

                    if ($sClass->academicPeriodID == $id) {
                        $canViewResults = 'Yes';
                    }
                    unset($tempAP);
                }


                // List to allow students to view results from HA
                $user  = User::find($user_id);
                $temp  = [];  //Temp::where('studentID', $user->student_id)->get()->first();

                if (!empty($temp)) {
                    $canViewResults = 'Yes';
                }

                $new_fees = null;

                $userMode = UserStudyModes::where('userID', $user_id)->first();

                $penalt = AcademicPeriods::find($ap->id);
                $penalty = DB::table('ac_fees')
                    ->where('name', 'Penalty Fee')
                    ->first();

                $penalt = AcademicPeriods::find($ap->id);
                $penalty = DB::table('ac_fees')
                    ->where('name', 'Late Registration')
                    ->first();

                if (!empty($penalty)) {

                    if (strtotime($penalt->lateRegistrationDate)< strtotime(date("Y-m-d". ' +10 day'))) {
                        $new_fees  = PeriodFees::where('academicPeriodID', '=', $ap->id)->where('studyModeID', '=', $userMode->studyModeID)->orwhere('feeID', '=', $penalty->id)->get();
                    } else {
                        $new_fees  = PeriodFees::where('academicPeriodID', '=', $ap->id)->where('studyModeID', '=', $userMode->studyModeID)->get();
                    }
                } else {
                    $new_fees  = PeriodFees::where('academicPeriodID', '=', $ap->id)->where('studyModeID', '=', $userMode->studyModeID)->get();
                }

                $newTuitionFee = [];
                $repeatFee = [];

                if ($new_fees){
                    foreach ($new_fees as $fee) {

                        $_fee       = Fee::data($fee->feeID, $ap->id);
                        if(!empty($_fee )){
                            if ($_fee['crf'] == 1) {
                                $repeatFee = $_fee;
                            }
                            if (empty($repeatFee)) {
                                $repeatFee = [];
                            }

                            if ($_fee['is_tuition'] == 1 && $_fee['crf'] == 0) {
                                $newTuitionFee  = $_fee;
                            } else {


                                if ($user_id > 0) {
                                    if ($_fee['once_off'] == "Recurring Fee") {
                                        $fees[]         = $_fee;
                                    }
                                } else {
                                    $fees[]         = $_fee;
                                }
                            }
                        }
                    }
                }
                if (empty($fees)) {
                    $fees = [];
                }


                $classes  = AcademicPeriods::myclasses($user_id, $ap->id,0);

                if ($is_degree_student == 1) {
                    $tuitionFee = $oldTuitionFee;
                } else {
                    $tuitionFee = $newTuitionFee;
                }

                $feeArray = [
                    'tuitionFee' => $tuitionFee,
                    'otherFees'  => $fees,
                ];

                if (empty($classes)) {
                    $classes = [];
                }

                $nextClasses = AcademicPeriods::nextClasses($user_id, $ap->id);
                $progression = Progression::comments($user_id, $ap->id);

                if (empty($progression)) {
                    $progression = [];
                }


                $aPeriod = [
                    'key'                   => $ap->id,
                    'code'                  => $ap->code,
                    'name'                  => $ap->period->type,
                    'acStartDate'           => $ap->acStartDate,
                    'acEndDate'             => $ap->acEndDate,
                    'registrationDate'      => $ap->registrationDate,
                    'lateRegistrationDate'  => $ap->lateRegistrationDate,
                    'fees'                  => $feeArray,
                    'classesFees'           => $classesFees,
                    'repeatFee'             => $repeatFee,
                    'classes'               => $classes,
                    'next_classes'          => $nextClasses,
                    'type'                  => $type,
                    'type_int'              => $ap->type,
                    'progression'           => $progression,
                    'canViewResults'        => $canViewResults,
                    'oldAcademicPeriodFees' => $oldAcademicPeriodFees,
                    'enrolmentSummary'      => EnrolmentSummary::academicPeriod($id, $user_id),
                ];
                return $aPeriod;
            } else {
                $aPeriod = [
                    'key'                   => $ap->id,
                    'code'                  => $ap->code,
                    'name'                  => $ap->period->type,
                    'acStartDate'           => $ap->acStartDate,
                    'acEndDate'             => $ap->acEndDate,
                    'registrationDate'      => $ap->registrationDate,
                    'lateRegistrationDate'  => $ap->lateRegistrationDate,
                    'fees'                  => $fees,
                    'classesFees'           => $classesFees,
                    'type'                  => $type,
                    'oldAcademicPeriodFees' => $oldAcademicPeriodFees,
                    'type_int'              => $ap->type,
                ];
                return $aPeriod;
            }
        }
    }


    public static function dataByUserEnrollment($id, $request_type = 1, $user_id = 0, $is_degree_student = 0, $programID = 0)
    {

        $ap  = AcademicPeriods::find($id);

        if ($ap->type ==  0) {
            $type  = "Academic";
        } else {
            $type  = "Professional";
        }




        $canViewResults = 'No';

        if ($user_id > 0) {

            // check if student can view results

            $enrollments = Enrollment::where('userID', $user_id)->get();

            foreach ($enrollments as $enrollment) {
                $classIDs[] = $enrollment->classID;
            }
            $studentsClasses = AcClass::wherein('id', $classIDs)->where('academicPeriodID', '!=', $id)->get()->unique('academicPeriodID');

            foreach ($studentsClasses as $sClass) {
                if ($sClass->academicPeriodID > $id) {
                    $canViewResults = 'Yes';
                } else {
                    $canViewResults = 'No';
                }

                if ($sClass->academicPeriodID == $id) {
                    $canViewResults = 'Yes';
                }
            }


            // List to allow students to view results from HA
            $user  = User::find($user_id);
            $temp  = [];  //Temp::where('studentID', $user->student_id)->get()->first();

            if (!empty($temp)) {
                $canViewResults = 'Yes';
            }


            $classes  = AcademicPeriod::myclasses($user_id, $ap->id,0);



            if (empty($classes)) {
                $classes = [];
            }




            $aPeriod = [
                'key'                   => $ap->id,
                'code'                  => $ap->code,
                'name'                  => $ap->period->type,
                'classes'               => $classes,
                'type'                  => $type,
                'type_int'              => $ap->type,
                'canViewResults'        => $canViewResults,
            ];
            return $aPeriod;
        } else {
            $aPeriod = [
                'key'                   => $ap->id,
                'code'                  => $ap->code,
                'name'                  => $ap->period->type,
                'type'                  => $type,
                'type_int'              => $ap->type,
            ];
            return $aPeriod;
        }
    }

    public static function myclasses($user_id, $academic_period_id,$id =0)
    {

        // Get all Enrolments belonging to this user
        $enrollments    = Enrollment::where('userID', $user_id)->get();

        if (!empty($enrollments))
        {
            foreach ($enrollments as $enrollment) {
                // Get class IDs from all users enrollments
                $classID    = $enrollment->classID;
                $classIDs[] = $classID;
            }

            if (!empty($classIDs)) {
                # new requirement requires check previous classes
                if($id == 1){
                    // $myClasses  = AcClass::whereIn('id', $classIDs)->where('academicPeriodID', $academic_period_id)->get();
                    $myClasses  = Classes::whereIn('id', $classIDs)->orderBy('academicPeriodID','DESC')->get()->unique('courseID');

                    foreach ($myClasses as $_class) {
                        $myClass      = Classes::data($_class->id, 0, $user_id);
                        $_myClasses[] = $myClass;
                        $myClass = [];
                    }
                    if (empty($_myClasses)) {
                        $_myClasses = [];
                    }
                    return $_myClasses;

                }else{
                    $myClasses  = Classes::whereIn('id', $classIDs)->where('academicPeriodID', $academic_period_id)->get();
                    # $myClasses  = AcClass::whereIn('id', $classIDs)->get();
                    foreach ($myClasses as $_class) {
                        $myClass      = Classes::data($_class->id, 0, $user_id);
                        $_myClasses[] = $myClass;
                        $myClass = [];
                    }
                    if (empty($_myClasses)) {
                        $_myClasses = [];
                    }
                    return $_myClasses;


                }
            }
        }
        else
        {
            return [];
        }
    }

    public static function myclassesTillProvidedAcademicPeriod($user_id, $academic_period_id)
    {
        // Get all Enrolments belonging to this user
        $enrollments  = Enrollment::where('userID', $user_id)->get();
        $aps          = [];
        $periods      = [];

        if (!empty($enrollments)) {

            foreach ($enrollments as $enrollment) {
                // Get class IDs from all users enrollments
                $classID    = $enrollment->classID;
                $classIDs[] = $classID;
            }

            if (!empty($classIDs)) {
                $myClasses  = Classes::whereIn('id', $classIDs)->where('academicPeriodID', '<=', $academic_period_id)->get();

                foreach ($myClasses as $class) {
                    $ap = AcademicPeriods::where('id', $class->academicPeriodID)->get()->first();
                    $apIDs[] = $ap->id;
                }

                if (!empty($apIDs)) {
                    $academicPeriods = AcademicPeriods::whereIn('id', $apIDs)->get()->unique('id');

                    foreach ($academicPeriods as $academicPeriod) {

                        $p = AcademicPeriods::dataMini($academicPeriod->id);
                        $period = [
                            'code'    => $p['code'],
                            'name'    => $p['name'],
                            'classes' => AcademicPeriods::myclasses($user_id, $academicPeriod->id,0),
                        ];

                        $periods[]  = $period;
                    }

                    return $periods;
                }
            }
        } else {
            return [];
        }
    }

    public static function myNextClasses($academic_period_id, $program_id)
    {
        # Return the classes that the student is able to take in this academic period and that are attached to this program
        $_classes           = [];
        $pCourseIDsArray    = [];
        $programCourses     = ProgramCourses::where('programID', $program_id)->where('active',0)->get();

        foreach ($programCourses as $pCourse) {
            $pCourseIDsArray[] = $pCourse->courseID;
        }
        if ($pCourseIDsArray) {
            # Run the classes set for this running academic period
            $academicPeriodClasses = Classes::where('academicPeriodID', $academic_period_id)->whereIn('courseID', $pCourseIDsArray)->get();

            foreach ($academicPeriodClasses as $class) {
                $_class     = Classes::data($class->id);
                $_classes[] = $_class;
            }
        }

        return $_classes;
    }

    public static function nextClasses($user_id, $currentAPID)
    {




        /************************************************/
        // Finding the most recent course that the student has done.
        $studetsEnrollments = Enrollment::where('userID', $user_id)->get();
        $sCourseIDs = [];
        foreach ($studetsEnrollments as $e) {
            $sCourseIDs[] = $e->class->course->id;
        }
        $userProgram    = UserProgram::where('userID', $user_id)->get()->last();
        $programCourse = ProgramCourses::whereIn('courseID', $sCourseIDs)->where('programID', $userProgram->programID)->orderBy('level_id', 'desc')->first();

        //$lastClass = AcClass::where('courseID',$programCourse->courseID)->where('academicPeriodID',$currentAPID)->get()->first();


        //$lastEnrollment = Enrollment::where('userID', $user_id)->where('classID',$lastClass->id)->get()->first();
        $lastEnrollment = Enrollment::where('userID', $user_id)->get()->last();
        $class          = Classes::find($lastEnrollment->classID);
        //$programCourse  = ProgramCourse::where('programID', $userProgram->programID)->where('courseID', $lastClass->id)->where('active', 1)->get()->first();
        $semesterStatus = 0;

        /************************************************/


        # Get previous academic period to find all courses that have been done.
        $userMode = UserStudyModes::where('userID', $user_id)->get()->first();
        if ($programCourse) {
            if ($userMode->studyModeID == 0 || $userMode->studyModeID == 1 && $programCourse) {

                # Check if all courses where taken.
                # Get all courses in past academic period
                $academic_period_id = $class->academicPeriodID;

                $previousEnrollments = Enrollment::where('userID', $user_id)->get();

                # get the course ids for all courses that the student took
                foreach ($previousEnrollments as $pEnrollment) {

                    if ($pEnrollment->class->academicPeriodID == $academic_period_id) {
                        $courseIDs[] = $pEnrollment->class->course->id;
                    }
                }

                $semesterStatus = General::checkStudentsSemester($user_id);
                #will check from here if the student has prerequiste courses then remove them
                // if ($semesterStatus == 0 && $programCourse) {
                //     $programCourses = ProgramCourse::where('programID', $programCourse->programID)->whereNotIn('courseID', $courseIDs)->where('level_id', $programCourse->level_id)->get();
                // }
                if ($semesterStatus == 0 && $programCourse) {
                    // $programCourses = ProgramCourse::where('programID', $programCourse->programID)->where('level_id', $programCourse->level_id + 1)->where('active', 1)->get();
                    $programCourses = ProgramCourses::where('programID', $programCourse->programID)->whereNotIn('courseID', $courseIDs)->where('level_id', $programCourse->level_id)->get();
                }


            } else {

                $resultsStatus = [];

                if (!empty($resultsStatus)) {
                    # Get custom classes
                    $programCourses = ProgramCourse::where('programID', $userProgram->programID)->where('level_id', $resultsStatus)->where('active', 1)->get();
                } else {

                    if (!empty($programCourse)) {
                        $programCourses = ProgramCourse::where('programID', $userProgram->programID)->where('level_id', $programCourse->level_id + 1)->get();
                    } else {
                        $programCourses = [];
                    }
                }
            }
            $pCourseIDsArray = [];
            foreach ($programCourses as $pCourse) {
                $pCourseIDsArray[] = $pCourse->courseID;
            }


            #will courses toby
            if ($pCourseIDsArray) {
                # Run the classes set for this running academic period
                $academicPeriodClasses = Classes::where('academicPeriodID', $currentAPID)->whereIn('courseID', $pCourseIDsArray)->get();

                foreach ($academicPeriodClasses as $class) {
                    $_class     = Classes::data($class->id);
                    $_classes[] = $_class;
                }
            }

            if (empty($_classes)) {

                if ($userMode->studyModeID == 2 || $userMode->studyModeID == 1) {

                    # Check if all courses where taken.
                    # Get all courses in past academic period
                    $academic_period_id = $class->academicPeriodID;

                    $previousEnrollments = Enrollment::where('userID', $user_id)->get();

                    # get the course ids for all courses that the student took
                    foreach ($previousEnrollments as $pEnrollment) {

                        if ($pEnrollment->class->academicPeriodID == $academic_period_id) {
                            $courseIDs[] = $pEnrollment->class->course->id;
                        }
                    }
                    $programCourses = ProgramCourse::where('programID', $programCourse->programID)->whereNotIn('courseID', $courseIDs)->where('level_id', $programCourse->level_id)->get();
                    //  $programCourses = ProgramCourse::where('programID', $programCourse->programID)->where('level_id', $programCourse->level_id + 1)->where('active', 1)->get();
                } else {

                    $resultsStatus = [];
                    if (!empty($resultsStatus)) {
                        # Get custom classes
                        $programCourses = ProgramCourse::where('programID', $userProgram->programID)->where('level_id', $resultsStatus)->where('active', 1)->get();
                    } else {

                        if (!empty($programCourse)) {
                            $programCourses = ProgramCourse::where('programID', $userProgram->programID)->where('level_id', $programCourse->level_id + 1)->where('active', 1)->get();
                        } else {
                            $programCourses = [];
                        }
                    }
                }

                $pCourseIDsArray = [];
                foreach ($programCourses as $pCourse) {
                    $pCourseIDsArray[] = $pCourse->courseID;
                }

                if ($pCourseIDsArray) {
                    # Run the classes set for this running academic period
                    $academicPeriodClasses = Classes::where('academicPeriodID', $currentAPID)->whereIn('courseID', $pCourseIDsArray)->get();

                    foreach ($academicPeriodClasses as $class) {
                        $_class     = Classes::data($class->id);
                        $_classes[] = $_class;
                    }
                }

                if (empty($_classes)) {
                    $_classes = [];
                }
            }

            return $_classes;
        } else {
            return [];
        }
    }

    public static function nextClassesTemp($user_id, $currentAPID)
    {

        # get the last enrollment
        $lastEnrollment = Enrollment::where('userID', $user_id)->get()->last();
        $class          = Classes::find($lastEnrollment->classID);

        $userProgram    = UserProgram::where('userID', $user_id)->get()->last();

        $programCourse  = ProgramCourse::where('programID', $userProgram->programID)->where('courseID', $class->courseID)->get()->first();


        # Get previous academic period to find all courses that have been done.
        $userMode = UserStudyModes::where('userID', $user_id)->get()->first();

        if ($userMode->studyModeID == 2) {

            # Check if all courses where taken.
            # Get all courses in past academic period
            $academic_period_id = $class->academicPeriodID;

            $previousEnrollments = Enrollment::where('userID', $user_id)->get();

            # get the course ids for all courses that the student took
            foreach ($previousEnrollments as $pEnrollment) {

                if ($pEnrollment->class->academicPeriodID == $academic_period_id) {
                    $courseIDs[] = $pEnrollment->class->course->id;
                }
            }
            // $programCourses = ProgramCourse::where('programID', $programCourse->programID)->whereNotIn('courseID', $courseIDs)->where('level_id', $programCourse->level_id)->get();

            if (empty($programCourses)) {
                $programCourses = ProgramCourse::where('programID', $programCourse->programID)->where('level_id', $programCourse->level_id + 1)->where('active', 1)->get();
            }
        } else {



            $resultsStatus = [];

            if (!empty($resultsStatus)) {
                # Get custom classes
                $programCourses = ProgramCourse::where('programID', $userProgram->programID)->where('level_id', $resultsStatus)->where('active', 1)->get();
            } else {

                if (!empty($programCourse)) {
                    $programCourses = ProgramCourse::where('programID', $userProgram->programID)->where('level_id', $programCourse->level_id + 1)->where('active', 1)->get();
                } else {
                    $programCourses = [];
                }
            }
        }



        $pCourseIDsArray = [];
        foreach ($programCourses as $pCourse) {
            $pCourseIDsArray[] = $pCourse->courseID;
        }

        if ($pCourseIDsArray) {
            # Run the classes set for this running academic period
            $academicPeriodClasses = Classes::where('academicPeriodID', $currentAPID)->whereIn('courseID', $pCourseIDsArray)->get();

            foreach ($academicPeriodClasses as $class) {
                $_class     = Classes::data($class->id);
                $_classes[] = $_class;
            }
        }

        if (empty($_classes)) {

            if ($userMode->studyModeID == 2) {

                # Check if all courses where taken.
                # Get all courses in past academic period
                $academic_period_id = $class->academicPeriodID;

                $previousEnrollments = Enrollment::where('userID', $user_id)->get();

                # get the course ids for all courses that the student took
                foreach ($previousEnrollments as $pEnrollment) {

                    if ($pEnrollment->class->academicPeriodID == $academic_period_id) {
                        $courseIDs[] = $pEnrollment->class->course->id;
                    }
                }



                $programCourses = ProgramCourse::where('programID', $programCourse->programID)->where('level_id', $programCourse->level_id + 1)->where('active', 1)->get();
            } else {



                $resultsStatus = [];

                if (!empty($resultsStatus)) {
                    # Get custom classes
                    $programCourses = ProgramCourse::where('programID', $userProgram->programID)->where('level_id', $resultsStatus)->where('active', 1)->get();
                } else {

                    if (!empty($programCourse)) {
                        $programCourses = ProgramCourse::where('programID', $userProgram->programID)->where('level_id', $programCourse->level_id + 1)->where('active', 1)->get();
                    } else {
                        $programCourses = [];
                    }
                }
            }

            $pCourseIDsArray = [];
            foreach ($programCourses as $pCourse) {
                $pCourseIDsArray[] = $pCourse->courseID;
            }

            if ($pCourseIDsArray) {
                # Run the classes set for this running academic period
                $academicPeriodClasses = Classes::where('academicPeriodID', $currentAPID)->whereIn('courseID', $pCourseIDsArray)->get();

                foreach ($academicPeriodClasses as $class) {
                    $_class     = Classes::data($class->id);
                    $_classes[] = $_class;
                }
            }


            if (empty($_classes)) {
                $_classes = [];
            }
        }


        return $_classes;
    }


    public static function dataTemp($id, $request_type = 1, $user_id = 0, $is_degree_student = 0, $programID = 0)
    {

        $ap  = AcademicPeriods::find($id);

        if ($ap->type ==  0) {
            $type  = "Academic";
        } else {
            $type  = "Professional";
        }

        if ($ap->type == 1) {
            # Find the available class fees
            $_classFees  = PeriodFees::where('academicPeriodID', $ap->id)->where('class_id', '!=', null)->get();

            if ($_classFees) {
                foreach ($_classFees as $cfee) {
                    $classesFees[]   = Fee::data($cfee->feeID, $ap->id, $cfee->class_id);
                }
            }

            if (empty($classesFees)) {
                $classesFees = [];
            }
        } else {
            $classesFees = [];
        }


        if ($request_type == 1) {
            $_classes = $ap->classes;

            foreach ($_classes as $_class) {
                $class     = Classes::data($_class->id);
                $classes[] = $class;
            }
            if (empty($classes)) {
                $classes = [];
            }

            # Find the programs running
            # Check for classes that are running and attached to the selected academic period
            $apClasses          = Classes::where('academicPeriodID', $ap->id)->get();

            if ($apClasses->count() > 0) {
                foreach ($apClasses as $apClass) {
                    $course_ids[] = $apClass->courseID;
                }

                $courses         = ProgramCourse::whereIn('courseID', $course_ids)->get()->unique('programID');
                foreach ($courses as $course) {
                    $program     = Classes::data($course->programID, $ap->id);
                    $programs[]  = $program;
                }
            }
            if (empty($programs)) {
                $programs = [];
            }


            $_fees  = PeriodFees::where('academicPeriodID', $ap->id)->where('class_id', null)->get();

            if ($_fees) {
                foreach ($_fees as $fee) {

                    $_fee       = Fee::data($fee->feeID, $ap->id);
                    $fees[]     = $_fee;
                }
            }

            if (empty($fees)) {
                $fees = [];
            }


            $aPeriod = [
                'key'                   => $ap->id,
                'code'                  => $ap->code,
                'name'                  => $ap->period->type,
                'acStartDate'           => $ap->acStartDate,
                'acEndDate'             => $ap->acEndDate,
                'registrationDate'      => $ap->registrationDate,
                'lateRegistrationDate'  => $ap->lateRegistrationDate,
                'programs'              => $programs,
                'classes'               => $classes,
                'fees'                  => $fees,
                'type'                  => $type,
                'classesFees'           => $classesFees,
                'type_int'              => $ap->type,
            ];
            return $aPeriod;
        } else {

            # If the request has degree Student remove the highest fee from the current fees attached to this academic period and place in the highest fee
            # from the users first ever academic period to track the first tuition fee that was charged.
            $userStudyMode          = UserStudyModes::where('userID', $user_id)->first();
            $firstEnrollment        = Enrollment::where('userID', $user_id)->first();

            $oldAcademicPeriodFees  = PeriodFees::where('academicPeriodID', $firstEnrollment->class->academicPeriodID)->where('studyModeID', $userStudyMode->studyModeID)->get();

            if ($oldAcademicPeriodFees) {
                foreach ($oldAcademicPeriodFees as $oldFee) {

                    $fee_old = Fee::data($oldFee->feeID, $firstEnrollment->class->academicPeriodID);

                    if ($fee_old['is_tuition'] == 1 && $fee_old['crf'] == 0) {
                        $oldTuitionFee = $fee_old;
                    }
                }
                if (empty($oldTuitionFee)) {
                    $oldTuitionFee = [];
                }
            }



            # Lets get the highest fee

            if (empty($user_id) || $user_id == 0) {
                $new_fees  = PeriodFees::where('academicPeriodID', $ap->id)->get();

                if ($new_fees) {
                    foreach ($new_fees as $fee) {

                        $_fee       = Fee::data($fee->feeID, $ap->id, $user_id);

                        if ($_fee['is_tuition'] == 1) {
                            $newTuitionFee  = $_fee;
                        } else {

                            if ($user_id > 0) {
                                if ($_fee['once_off'] == "Recurring Fee") {
                                    $fees[]         = $_fee;
                                }
                            } else {
                                $fees[]         = $_fee;
                            }
                        }
                    }
                }
                if (empty($fees)) {
                    $fees = [];
                }
            }

            $canViewResults = 'No';

            if ($user_id > 0) {

                // check if student can view results

                $enrollments = Enrollment::where('userID', $user_id)->get();

                foreach ($enrollments as $enrollment) {
                    $classIDs[] = $enrollment->classID;
                }
                $studentsClasses = Classes::wherein('id', $classIDs)->where('academicPeriodID', '!=', $id)->get()->unique('academicPeriodID');

                foreach ($studentsClasses as $sClass) {
                    if ($sClass->academicPeriodID > $id) {
                        $canViewResults = 'Yes';
                    } else {
                        $canViewResults = 'No';
                    }

                    if ($sClass->academicPeriodID == $id) {
                        $canViewResults = 'Yes';
                    }
                }


                // List to allow students to view results from HA
                $user  = User::find($user_id);
                $temp  = []; //Temp::where('studentID', $user->student_id)->get()->first();

                if (!empty($temp)) {
                    $canViewResults = 'Yes';
                }

                $new_fees = null;

                $userMode = UserStudyModes::where('userID', $user_id)->first();
                $new_fees  = PeriodFees::where('academicPeriodID', $ap->id)->where('studyModeID', $userMode->studyModeID)->get();
                $newTuitionFee = [];
                $repeatFee = [];
                if ($new_fees) {
                    foreach ($new_fees as $fee) {

                        $_fee       = Fee::data($fee->feeID, $ap->id);

                        if ($_fee['crf'] == 1) {
                            $repeatFee = $_fee;
                        }
                        if (empty($repeatFee)) {
                            $repeatFee = [];
                        }

                        if ($_fee['is_tuition'] == 1 && $_fee['crf'] == 0) {
                            $newTuitionFee  = $_fee;
                        } else {


                            if ($user_id > 0) {
                                if ($_fee['once_off'] == "Recurring Fee") {
                                    $fees[]         = $_fee;
                                }
                            } else {
                                $fees[]         = $_fee;
                            }
                        }
                    }
                }
                if (empty($fees)) {
                    $fees = [];
                }


                $classes  = AcademicPeriods::myclasses($user_id, $ap->id,0);

                if ($is_degree_student == 1) {
                    $tuitionFee = $oldTuitionFee;
                } else {
                    $tuitionFee = $newTuitionFee;
                }

                $feeArray = [
                    'tuitionFee' => $tuitionFee,
                    'otherFees'  => $fees,
                ];

                if (empty($classes)) {
                    $classes = [];
                }



                //$nextClasses = AcademicPeriod::nextClasses($user_id, $ap->id);
                $progression = Progression::comments($user_id, $ap->id);

                if (empty($progression)) {
                    $progression = [];
                }


                $aPeriod = [
                    'key'                   => $ap->id,
                    'code'                  => $ap->code,
                    'name'                  => $ap->period->type,
                    'acStartDate'           => $ap->acStartDate,
                    'acEndDate'             => $ap->acEndDate,
                    'registrationDate'      => $ap->registrationDate,
                    'lateRegistrationDate'  => $ap->lateRegistrationDate,
                    'fees'                  => $feeArray,
                    'classesFees'           => $classesFees,
                    'repeatFee'             => $repeatFee,
                    'classes'               => $classes,

                    'type'                  => $type,
                    'type_int'              => $ap->type,
                    'progression'           => $progression,
                    'canViewResults'        => $canViewResults,
                ];
                return $aPeriod;
            } else {
                $aPeriod = [
                    'key'                   => $ap->id,
                    'code'                  => $ap->code,
                    'name'                  => $ap->period->type,
                    'acStartDate'           => $ap->acStartDate,
                    'acEndDate'             => $ap->acEndDate,
                    'registrationDate'      => $ap->registrationDate,
                    'lateRegistrationDate'  => $ap->lateRegistrationDate,
                    'fees'                  => $fees,
                    'classesFees'           => $classesFees,
                    'type'                  => $type,
                    'type_int'              => $ap->type,
                ];
                return $aPeriod;
            }
        }
    }

    public static function dataExamRegistration($academicPeriodID)
    {

        $ap = AcademicPeriods::find($academicPeriodID);


        $examRegistrations = ExamRegistraion::where('academicPeriodID', $academicPeriodID)->get();

        foreach ($examRegistrations as $examRegistration) {
            $rawStudent =  User::jsondataMini($examRegistration->userID);

            $registrationClasses = ExamRegisteredCourses::where('registrationID', $examRegistration->id)->get();

            foreach ($registrationClasses as $registeredClass) {
                $classes[] = Classes::dataMini($registeredClass->classID, 0, null);
            }

            if (empty($classes)) {
                $classes = [];
            }

            if (!empty($examRegistration->created_at)) {
                $submissionDate  = $examRegistration->created_at->toFormattedDateString();
            } else {
                $submissionDate  = '';
            }
            $status = 'Pending';
            if ($examRegistration->status == 1) {
                $status = 'Approved';
            }
            if ($examRegistration->status == 0) {
                $status = 'Pending Approval ';
            }
            if ($examRegistration->status == -1) {
                $status = 'Declined ';
            }

            $student = [
                'id'                => $rawStudent['id'],
                'student_id'        => $rawStudent['student_id'],
                'initials'          => $rawStudent['initials'],
                'regID'             => $examRegistration->id,
                'avatar'            => $rawStudent['avatar'],
                'status'            => $status,
                'email'             => $rawStudent['email'],
                'gender'            => $rawStudent['gender'],
                'names'             => $rawStudent['names'],
                'program'           => $rawStudent['currentProgramName'],
                'progression'       => $rawStudent['progression']['currentLevelName'],
                'submissionDate'    => $submissionDate,
                'numOfCourses'      => $registrationClasses->count(),
                'comment'           => $examRegistration->note,
                'classes'           => $classes,
            ];

            if ($examRegistration->status == 0) {
                # Pending
                $pendingStudents[] = $student;
            }
            if ($examRegistration->status == 1) {
                # Approved
                $approvedStudents[] = $student;
            }
            if ($examRegistration->status == -0) {
                # Declined
                $declinedStudents[] = $student;
            }


            if (empty($approvedStudents)) {
                $approvedStudents = [];
            }
            if (empty($pendingStudents)) {
                $pendingStudents = [];
            }
            if (empty($declinedStudents)) {
                $declinedStudents = [];
            }

            $students[] = $student;

            unset($student, $rawStudent, $classes);
        }


        // Find the classes that are attached to this academic period.
        $approvedExamRegistrations = ExamRegistraion::where('academicPeriodID', $academicPeriodID)->where('status', 1)->get();
        foreach ($approvedExamRegistrations as $examRegistration) {
            $registrationIDs[] = $examRegistration->id;
        }

        if (!empty($registrationIDs)) {
            $uniqueClassIDs = ExamRegisteredCourses::whereIn('registrationID', $registrationIDs)->get()->unique('classID');
            if (!empty($uniqueClassIDs)) {
                foreach ($uniqueClassIDs as $uniqueClassID) {
                    $apClasses[] = Classes::dataMini($uniqueClassID->classID);
                }
            }
        }

        if (empty($apClasses)) {
            $apClasses = [];
        }



        $aPeriod = [
            'key'                   => $ap->id,
            'code'                  => $ap->code,
            'name'                  => $ap->period->type,
            'acStartDate'           => $ap->acStartDate,
            'acEndDate'             => $ap->acEndDate,
            'registrationDate'      => $ap->registrationDate,
            'lateRegistrationDate'  => $ap->lateRegistrationDate,
            'type_int'              => $ap->type,
            'students'              => $students,
            'pendingStudents'       => $pendingStudents,
            'approvedStudents'      => $approvedStudents,
            'declinedStudents'      => $declinedStudents,
            'apClasses'             => $apClasses,

        ];
        return $aPeriod;
    }


}
