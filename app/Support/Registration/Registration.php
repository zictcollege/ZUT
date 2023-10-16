<?php

namespace App\Support\Registration;

use Illuminate\Http\Request;
use App\Models\Accounting\Fee;
use App\Models\Admissions\UserProgram;

use App\Traits\Finance\Accounting\Invoicing;
use App\Traits\User\General;
use Illuminate\Support\Facades\DB;
use App\User;
use Carbon\Carbon;

trait Registration
{
    use Invoicing;

    public function status($user_id)
    {
        $user = General::jsondata($user_id);
        return $user;
    }

    public function findUpcomingAcademicPeriods($programID, $userID)
    {

        # Return academic periods that belong to the programs certification type
        $programCourses = ProgramCourse::where('programID', $programID)->get();
        $userStudyMode  = UserMode::where('userID', $userID)->get()->last();



        foreach ($programCourses as $course) {
            $courseIDs[]    = $course->courseID;
        }

        $setClasses = AcClass::wherein('courseID', $courseIDs)->get()->unique('academicPeriodID');

        foreach ($setClasses as $uniqueClass) {
            # Find the academic periods that have an open registration date which is greater than today
            $ap                = AcademicPeriod::where('id', $uniqueClass->academicPeriodID)->where('studyModeIDAllowed', $userStudyMode->studyModeID)->get()->first();

            if ($ap) {
                $academicPeriod    = AcademicPeriod::dataMini($ap->id);
                if ($academicPeriod['acEndDate'] > Carbon::now()) {
                    $academicPeriods[] = $academicPeriod;
                }
            }
        }

        return $academicPeriods;
    }
    public function classesAttended(Request $request)
    {



       /* try {*/

            # Find if student is degree student and add the value to the academic period data function
            $userProgram = UserProgram::where('programID', request('programID'))->where('userID', request('userid'))->get()->first();

            $certification = $userProgram->program->qualification->name;

            switch ($certification) {
                case 'Degree':
                    $is_degree_student = 1;
                    break;
                case 'Professional':
                    $is_degree_student = 0;
                    break;
                case 'Diploma':
                    $is_degree_student = 0;
                    break;
                default:
                    $is_degree_student = 0;
                    break;
            }


            $enrollments       = Enrollment::where('userID', request('userid'))->get();



            if ($certification == 'Degree' || $certification == 'Diploma') {


                $academicPeriod    = AcademicPeriod::data(request('academicPeriodID'), 0, request('userid'), $is_degree_student);
            } else {
                $academicPeriod    = AcademicPeriod::data(request('academicPeriodID'), 1, request('userid'), $is_degree_student, request('programID'));
            }

            foreach ($enrollments as $enrollment) {
                $class         = AcClass::data($enrollment->classID, 0, request('userid'));
                $classes[]     = $class;
            }

            $previousEnrollment = Enrollment::where('userID', request('userid'))->get()->last();
            $comments           = Progression::comments(request('userid'), $previousEnrollment->class->academicPeriod->id);
            $progression        = Progression::checkProgression($comments);



            # Find the last academic period that the student registered for and return null if the selected has been registered for
            foreach ($enrollments as $enrollment) {
                $oldAcademicPeriodIds[] = $enrollment->class->academicPeriod->id;
            }
            $has_registered =0;
            foreach ($oldAcademicPeriodIds as $oldAcademicPeriodID) {


             $timeFactor = AcademicPeriod::find(request('academicPeriodID'));

              # $addTen= strtotime($timeFactor->lateRegistrationDate)+10;



            #add 10 days to late registration

             $addTen = date('Y-m-d', strtotime($timeFactor->lateRegistrationDate. ' + 10 days'));

                if($addTen < date("Y-m-d")){
                    $has_registered = 5;
                }else {
                    if ($oldAcademicPeriodID == request('academicPeriodID')) {
                        # User has registered for this academic period before
                        $has_registered = 1;
                    } else {
                        $has_registered = 0;
                    }

                }

            }
            // $lastEnrollmentAP = Enrollment::where('userID',(request('userid'))->get()->last();
            // $classAcademicPeriod         = AcClass::find($lastEnrollmentAP->classID);
            // $classAcademicPeriod->academicPeriodID;
            # Find next classes



         $nextClasses   = AcademicPeriod::nextClasses(request('userid'),  request('academicPeriodID'));


            $failedClasses = $comments['coursesFailed'];
            $passedClasses = $comments['coursesPassed'];



            $userMode           = UserMode::where('userID', request('userid'))->get()->last();
            $semesterStatus     = General::checkStudentsSemester(request('userid'));



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

                // return $data = [
                //     'selectedAcademicPeriod' =>  $academicPeriod,
                //     'classes'                => $classes,
                //     'resultsdata'            => $comments,
                //     'progression'            => $progression,
                //     'has_registered'         => $has_registered,
                //     'covid_affected'         => 0,
                //     'studyModeID'            => $userMode->studyModeID,
                //     'semesterStatus'         => $semesterStatus,
                // ];

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





       /* } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);
        }*/
    }


    public function register(Request $request)
    {
        $data                   = (new self)->classesAttended($request);
        $proposedClasses        = $data['selectedAcademicPeriod']['next_classes'];
        $selectedClasses        = request('selectedRowKeys');
        $otherFees              = $data['selectedAcademicPeriod']['fees']['otherFees'];
        $tuitionFee             = array($data['selectedAcademicPeriod']['fees']['tuitionFee']);
        $repeatFee              = $data['selectedAcademicPeriod']['repeatFee'];
        $checkSemesterStatus    = General::checkStudentsSemester(request('userid'));
        $userStudyMode          = UserMode::where('userID', request('userid'))->get()->last();
        $studyModeID            = $userStudyMode->studyModeID;

        if (empty(request('selectedRowKeys')) && $studyModeID == 2) {
            $this->Validate($request, array(
                'selectedRowKeys'  => 'required',
            ));
            if (empty($selectedClasses)) {
                return response()->json([
                    'status' => 'error',
                    'errors' => 'No Classes Selected'
                ], 422);
            }
        }

        /**
         * 0 - Clear Pass Proceed
         * 1 - Proceed with repeat
         * 2 - Repeat courses failed and pay for each and cant pick next course
         * 3 - Repeat year || Exclude
         */


        // check for registration bypass for users not to be invoiced. Note that this is a temporal resolution for students who where invoiced for the whole year
        // registration bypass
        $user = User::find(request('userid'));
        $bypass = Temp2::where('studentID', $user->student_id)->get()->first();

        if (!empty($bypass)) {
            $registrationBypass = 1;
        } else {
            $registrationBypass = 0;
        }


        try {
            DB::beginTransaction();
            # Progression check

            if ($data['progression']['progression'] == 0) {
                # Clear pass so get the selected courses.

                if ($selectedClasses) {
                    # enroll student into the classes selected...
                    foreach ($selectedClasses as $classID) {
                        Enrollment::create([
                            'userID'    => request('userid'),
                            'classID'   => $classID,
                            'key'       => request('userid') . '-' . $classID . '-' . $data['selectedAcademicPeriod']['key'].'-'. request('userid'),
                        ]);
                    }
                } else {

                    foreach ($proposedClasses as $class) {

                        Enrollment::create([
                            'userID'    => request('userid'),
                            'classID'   => $class['key'],
                            'key'       => request('userid') . '-' . $class['key'] . '-' . $data['selectedAcademicPeriod']['key'].'-'. request('userid'),
                        ]);
                    }
                }



                # Merge otherFees and tution fees;

                #commented out for evening mode
                if ($checkSemesterStatus == 1 && $studyModeID == 2) {
                    $fees = array_merge($otherFees, $tuitionFee);
                } else {
                    $fees = array_merge($otherFees, $tuitionFee);
                }

                #making all fees compulsory according to academic period predefined fees
                //$fees = array_merge($otherFees, $tuitionFee);

                if ($registrationBypass == 0) {
                    Invoicing::store(request('userid'), $fees, $data['selectedAcademicPeriod']['key']);
                }
            }

            if ($data['progression']['progression'] == 1) {


                # Repeat the courses failed without an option of selecting new courses
                $repeatCourses = $data['resultsdata']['coursesFailed'];

                foreach ($repeatCourses as $class) {

                    # Find the course if its being offered in the selected academic period
                    $course = Course::where('code', $class['course_code'])->first();
                    $_class = AcClass::where('academicPeriodID', $data['selectedAcademicPeriod']['key'])->where('courseID', $course->id)->first();
                    if ($_class) {
                        Enrollment::create([
                            'userID'        => request('userid'),
                            'classID'       => $_class->id,
                            'repeatStatus'  => 1,
                            'key'       => request('userid') . '-' . $class['key'] . '-' . $data['selectedAcademicPeriod']['key'],
                        ]);
                    }
                }

                if ($selectedClasses) {
                    # enroll student into the classes selected...
                    foreach ($selectedClasses as $classID) {

                        Enrollment::create([
                            'userID'    => request('userid'),
                            'classID'   => $classID,
                            'key'       => request('userid') . '-' . $classID . '-' . $data['selectedAcademicPeriod']['key'],
                        ]);
                    }
                } else {
                    foreach ($proposedClasses as $class) {

                        Enrollment::create([
                            'userID'    => request('userid'),
                            'classID'   => $class['key'],
                            'key'       => request('userid') . '-' . $class['key'] . '-' . $data['selectedAcademicPeriod']['key'],
                        ]);
                    }
                }

                # Merge otherFees and tution fees;
                //$fees = array_merge($otherFees,$tuitionFee);
              //  $fees = array_merge($otherFees, $tuitionFee);
                if ($checkSemesterStatus == 1 && $studyModeID == 2) {
                    $fees = array_merge($otherFees, $tuitionFee);
                } else {
                    $fees = array_merge($otherFees, $tuitionFee);
                }

                if ($registrationBypass == 0) {
                    Invoicing::store(request('userid'), $fees, $data['selectedAcademicPeriod']['key']);
                }
            }

            if ($data['progression']['progression'] == 2) {

                # Repeat the courses failed without an option of selecting new courses
                $repeatCourses = $data['resultsdata']['coursesFailed'];

                foreach ($repeatCourses as $class) {

                    # Find the course if its being offered in the selected academic period
                    $course = Course::where('code', $class['course_code'])->first();
                    $_class = AcClass::where('academicPeriodID', $data['selectedAcademicPeriod']['key'])->where('courseID', $course->id)->first();

                    if (empty($_class)) {
                        # No class available create class
                        $_class = AcClass::createClass(1, $course->id, $data['selectedAcademicPeriod']['key']);
                    }

                    if ($_class) {

                        Enrollment::create([
                            'userID'        => request('userid'),
                            'classID'       => $_class->id,
                            'repeatStatus'  => 1,
                            'key'       => request('userid') . '-' . $class['key'] . '-' . $data['selectedAcademicPeriod']['key'],
                        ]);
                    }
                }

                # Merge otherFees and course repeat fee * the num fees;
                $repeatFeeAmount = $data['selectedAcademicPeriod']['repeatFee']['amount'];
                $data['selectedAcademicPeriod']['repeatFee']['amount'] = $repeatFeeAmount * $data['resultsdata']['coursesFailedCount'];
                $repeatFee = array($data['selectedAcademicPeriod']['repeatFee']);

                //$fees = array_merge($otherFees,$repeatFee);
                $fees = array_merge($otherFees, $tuitionFee);
                if ($checkSemesterStatus == 1 && $studyModeID == 2) {
                    // $fees = $tuitionFee;
                    $fees = array_merge($otherFees, $tuitionFee);
                } else {
                    $fees = array_merge($otherFees, $repeatFee);
                }

                if ($registrationBypass == 0) {
                    # code...
                    Invoicing::store(request('userid'), $fees, $data['selectedAcademicPeriod']['key']);
                }
            }

            if ($data['progression']['progression'] == 3) {

                # Repeat the courses failed without an option of selecting new courses
                $repeatCoursesF = $data['resultsdata']['coursesFailed'];
                $repeatCoursesP = $data['resultsdata']['coursesPassed'];


                foreach ($repeatCoursesF as $class) {

                    # Find the course if its being offered in the selected academic period
                    $course = Course::where('code', $class['course_code'])->first();
                    $_class = AcClass::where('academicPeriodID', $data['selectedAcademicPeriod']['key'])->where('courseID', $course->id)->first();
                    if ($_class) {
                        Enrollment::create([
                            'userID'        => request('userid'),
                            'classID'       => $_class->id,
                            'repeatStatus'  => 1,
                            'key'       => request('userid') . '-' . $class['key'] . '-' . $data['selectedAcademicPeriod']['key'].'-'.'3',
                        ]);
                    }
                }
                foreach ($repeatCoursesP as $class) {

                    # Find the course if its being offered in the selected academic period
                    $course = Course::where('code', $class['course_code'])->first();
                    $_class = AcClass::where('academicPeriodID', $data['selectedAcademicPeriod']['key'])->where('courseID', $course->id)->first();
                    if ($_class) {
                        Enrollment::create([
                            'userID'        => request('userid'),
                            'classID'       => $_class->id,
                            'repeatStatus'  => 1,
                            'key'       => request('userid') . '-' . $class['key'] . '-' . $data['selectedAcademicPeriod']['key'].'-'.'3',
                        ]);
                    }
                }

                # Merge otherFees and tution fees;
                //$fees = array_merge($otherFees,$tuitionFee);
               // $fees = array_merge($otherFees, $tuitionFee);
                if ($checkSemesterStatus == 1 && $studyModeID == 2) {
                    //$fees = $tuitionFee;
                    $fees = array_merge($otherFees, $tuitionFee);
                } else {
                    $fees = array_merge($otherFees, $tuitionFee);
                }

                if ($registrationBypass == 0) {
                    Invoicing::store(request('userid'), $fees, $data['selectedAcademicPeriod']['key']);
                }
            }


            DB::commit();

            $data = (new self)->classesAttended($request);
        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);
        }
    }


    public function registerProfessional(Request $request)
    {


        $data = (new self)->submitProfessionalCoursesCall($request);

        # Academic Data
        $proposedClasses = $data['next_classes'];

        # Financal Data
        $otherFees       = $data['otherFees'];
        $tuitionFee      = $data['tuitionFees'];


        try {
            DB::beginTransaction();
            # Progression check


            foreach ($proposedClasses as $class) {

                Enrollment::create([
                    'userID'    => request('userid'),
                    'classID'   => $class->id,
                    'key'       => request('userid') . '-' . $class->id . '-' . $data['academic_period_id'],
                ]);
            }

            # Merge otherFees and tution fees;
            $fees = array_merge($otherFees, $tuitionFee);
            Invoicing::store(request('userid'), $fees, $data['academic_period_id']);



            DB::commit();

            $data = (new self)->submitProfessionalCoursesCall($request);
        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);
        }
    }


    public static function submitProfessionalCoursesCall(Request $request)
    {

        /*try {

            /*if (empty(request('academicPeriodID'))) {
                $academicPeriodID = request('academicPeriodID');
                $programID        = request('programID');
            }*/


        $academic_period    = AcademicPeriod::find(request('academicPeriodID'));
        $study_mode         = StudyMode::find(request('studymodeid'));
        $course             = ProgramCourse::where('programID', request('programid'))->first();


        # Check for classes that are running and attached to the selected academic period
        $apClasses          = AcClass::where('academicPeriodID', request('academicPeriodID'))->whereIn('id', request('selectedCourses'))->get();

        foreach ($apClasses as $apClass) {
            $course_ids[] = $apClass->courseID;
            $classIDs[]   = $apClass->id;
        }

        $acFees    = AcademicPeriodFee::where('academicPeriodID', request('academicPeriodID'))->where('studyModeID', request('studymodeid'))->where('class_id', null)->get();
        $courses   = ProgramCourse::whereIn('courseID', $course_ids)->get();

        foreach ($courses as $_course) {
            $course = Course::find($_course->courseID);
            $_courses[] = $course;
        }

        if (empty($_courses)) {
            $_courses = [];
        }


        # Get selected classes fees
        $selectedClassesFees = AcademicPeriodFee::whereIn('class_id', $classIDs)->get();

        $ftotal = 0;

        if ($selectedClassesFees) {
            foreach ($selectedClassesFees as $sFee) {

                if ($sFee->crf == 0) {
                    $__fee      = Fee::data($sFee->feeID, request('academicPeriodID'), $sFee->class_id);
                    $ftotal     = $ftotal + $__fee['amount'];
                    $_sfees[]   = $__fee;
                }
            }
        }

        if (empty($_sfees)) {
            $_sfees = [];
        }

        $total  = 0;
        if ($acFees) {
            foreach ($acFees as $acFee) {
                if ($acFee->crf == 0) {
                    $_fee      = Fee::data($sFee->feeID, request('academicPeriodID'));
                    $total     = $total + $_fee['amount'];
                    $_fees[]   = $_fee;
                }
            }
        }

        if (empty($_fees)) {
            $_fees = [];
        }

        // Other Fees
        $otherFees = AcademicPeriodFee::where('academicPeriodID', request('academicPeriodID'))->where('once_off', 0)->where('crf', 0)->where('class_id', null)->get();

        if ($otherFees) {
            foreach ($otherFees as $oFee) {
                $oFees[] = Fee::data($oFee->feeID, request('academicPeriodID'));
                $total     = $total + $oFee['amount'];
            }
        }

        $data = [
            'courses'               => $_courses,
            'academic_period_id'    => request('academicPeriodID'),
            'academicPeriod'        => $academic_period,
            'program'               => Program::data(request('programID')),
            'fees'                  => $_sfees,
            'otherFees'             => $oFees,
            'fee_total'             => $total + $ftotal,
            'tuitionFees'           => $_sfees,
            'academic_period'       => $academic_period,
            'study_mode'            => $study_mode,
            'authenticateduser_id'  => request('authenticateduser_id'),
            'user_id'               => request('user_id'),
            'program_id'            => request('programid'),
            'selectedCourses'       => request('selectedCourses'),
            'paymentPlanID'         => request('paymentPlanID'),
            'next_classes'          => $apClasses,
        ];

        return $data;
        /*
             } catch (\Exception $e) {

               DB::rollback();

               return response()->json([
                   'status' => 'error',
                   'errors' => $e->getMessage()
               ], 422);

             }*/
    }
}
