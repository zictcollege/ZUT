<?php

namespace App\Traits\Academics;

use App\Models\Academics\AcademicPeriods;
use App\Models\Academics\Classes;
use App\Models\Academics\Programs;
use App\Models\Admissions\ProgramCourses;
use App\Models\Admissions\UserProgram;

use App\Models\Admissions\UserStudyModes;
use App\Models\Audit\Trail;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;


trait ExaminationReportTrait
{

    public function showAcademicReportPage()
    {
        $ip = getenv("REMOTE_ADDR");
        //dd($ip);
        return view('app.modules.academics.reports.academicReport');
    }

    /**
     * 
     * Generates a report on final program report detailing the students status.
     * 
     * */

    public static function generateFinalProgramReport($programID, $studyModeID)
    {

        ini_set('max_execution_time', 300);


        // Find the students who are enrolled into this program. 
        $userPrograms = UserProgram::where('programID', $programID)->get();

        // Filter results with user modes table to get students who are in the provided mode 
        foreach ($userPrograms as $userProgram) {

            $userMode = UserStudyModes::where('userID', $userProgram->userID)->get()->last();
            if (!empty($userMode->studyModeID) && $userMode->studyModeID == $studyModeID) {
                $users[] = User::find($userProgram->userID);
            }
        }

        if (empty($users)) {
            $users = [];
        }

        if ($users) {

            foreach ($users as $user) {


                $userJson = [
                    'key'                    => $user->id,
                    'student_id'             => $user->student_id,
                    'studyModeID'            => $studyModeID,
                    'names'                  => $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name,
                    'email'                  => $user->email,
                    'gender'                 => $user->gender,
                    'programID'              => $programID,
                    'enrollmentDistribution' => self::enrollmentData($programID,$user->id),
                ];

                $usersJson[] = $userJson;
            }
        }

        if (empty($usersJson)) {
            $usersJson = [];
        }


        return $usersJson;
    }

    public static function generateFinalProgramReportProgramName($programID, $studyModeID)
    {

        ini_set('max_execution_time', 300);
        $program = Programs::data($programID);

        $data = [
            'auditTrail'  => Trail::examiationAudit($programID),
            'program'     => $program,
        ];
        return $data;
    }


    public static function enrollmentData($id,$userID) // UserProgramID
    {
        $userProgram    = UserProgram::where('programID',$id)->where('userID',$userID)->get()->last();
        $programCourses = ProgramCourses::where('programID', $userProgram->programID)->get();

        foreach ($programCourses as $programCourse) {
            $courseIDs[] = $programCourse->courseID;
        }

        $classes = Classes::wherein('courseID', $courseIDs)->get();
        foreach ($classes as $class) {
            $classIDs[] = $class->id;
        }
        $enrollments = Enrollment::wherein('classID', $classIDs)->where('userID', $userProgram->userID)->get();
        foreach ($enrollments as $enrollment) {
            $attendedClassIDs[] = $enrollment->classID;
        }

        if (!empty($attendedClassIDs)) {

            $myClasses = Classes::wherein('id', $attendedClassIDs)->get()->unique('academicPeriodID');

            foreach ($myClasses as $class) {
                $academicPeriod = AcademicPeriods::find($class->academicPeriodID);
                $_ap            = AcademicPeriods::dataByUserEnrollment($academicPeriod->id, 0, $userProgram->userID);
                $_aps[]         = $_ap;
            }
        }


        if (empty($_aps)) {
            $_aps = [];
        }
        return $_aps;
    }
}
