<?php

namespace App\Support;


use App\Models\Academics\AcademicPeriods;
use App\Models\Academics\Classes;
use App\Models\Admissions\ProgramCourses;
use App\Models\Admissions\UserProgram;
use App\Models\Enrollment;
use App\Models\User;
use Carbon\Carbon;
use App\Traits\User\General as useri;
class ClassEnrollment
{
use useri;
    public static function enroll($user_id, $academic_period_id, $courses)
    {

        foreach ($courses as $course) {

            if ($course['id']) {
                $class = Classes::where('courseID', $course['id'])->where('academicPeriodID', $academic_period_id)->first();
            } else {
                $class = Classes::where('courseID', $course->id)->where('academicPeriodID', $academic_period_id)->first();
            }

            if ($class) {
                # Enroll user into a class having this course id and academic period that is in the request
                $enrollment = Enrollment::where('userID', $user_id)->where('classID', $class->id)->get()->first();
                if (empty($enrollment) && empty($enrollment->id)) {
                    $enrollment = new Enrollment();
                    $enrollment->userID   = User::find($user_id)->id;
                    $enrollment->classID  = $class->id;
                    $enrollment->key      = $class->id . '-' . User::find($user_id)->id;
                    $enrollment->save();
                }
            }
        }
    }

    public static function academicPeriodProgramEnrollments($programID, $academicPeriodID)
    {

        # Find the classes that are running in the provided academic period
        $classes = Classes::where('academicPeriodID', $academicPeriodID)->get();
        foreach ($classes as $class) {
            # Store class IDs
            $classIDs[] = $class->id;
        }
        $enrollments = Enrollment::whereIn('classID', $classIDs)->get()->unique('userID');

        # Get userIDs
        foreach ($enrollments as $enrollment) {
            $userIDs[] = $enrollment->userID;
        }

        if (!empty($userIDs)) {
            $userPrograms = UserProgram::whereIn('userID', $userIDs)->where('programID', $programID)->get();
            return $userPrograms;
        } else {
            return [];
        }
    }

    public static function viewFullProgramEnrollments($programID, $academicPeriodID)
    {

        # Find the classes that are running in the provided academic period
        $classes = Classes::where('academicPeriodID', $academicPeriodID)->get();
        foreach ($classes as $class) {
            # Store class IDs
            $classIDs[] = $class->id;
        }
        $enrollments = Enrollment::whereIn('classID', $classIDs)->get()->unique('userID');

        # Get userIDs
        foreach ($enrollments as $enrollment) {
            $userIDs[] = $enrollment->userID;
        }

        if (!empty($userIDs)) {
            $userPrograms = UserProgram::whereIn('userID', $userIDs)->where('programID', $programID)->get();

            foreach ($userPrograms as $userProgram) {

                $users[] = self::jsondataMini($userProgram->userID);
            }

            if (empty($users)) {
                $users = [];
            }

            return $users;
        } else {
            return $users = [];
        }
    }

    public static function viewFullProgramEnrollmentsByProgramID($programID, $academicPeriodID)
    {

        # Find the classes that are running in the provided academic period
        $classes = Classes::where('academicPeriodID', $academicPeriodID)->get();
        foreach ($classes as $class) {
            $classIDs[] = $class->id;
        }
        $enrollments = Enrollment::whereIn('classID', $classIDs)->get()->unique('userID');

        # Get userIDs
        foreach ($enrollments as $enrollment) {
            $userProgram = UserProgram::where('userID', $enrollment->userID)->where('programID', $programID)->get()->first();
            if ($userProgram && $userProgram->programID == $programID) {
                $userIDs[] = $enrollment->userID;
            }
        }

        if (!empty($userIDs)) {
            $userPrograms = UserProgram::whereIn('userID', $userIDs)->where('programID', $programID)->get();
            foreach ($userPrograms as $userProgram) {
                $users[] = self::jsondataMini($userProgram->userID);
            }

            if (empty($users)) {
                $users = [];
            }

            return $users;
        } else {
            return $users = [];
        }
    }


    public static function enrollmentsSummary()
    {

        // Get all active academic periods 
        $academicPeriods = AcademicPeriods::where('acEndDate', '>', Carbon::now())->get();
        $totalActive     = 0;
        foreach ($academicPeriods as $academicPeriod) {

            $apData = AcademicPeriods::data($academicPeriod->id);
            $totalEnrollments = 0;

            foreach ($apData['programs'] as $program) {

                $totalEnrollments = (int)$totalEnrollments + (int)$program['enrolledStudents'];
                $totalActive      = (int)$totalActive + (int)$program['enrolledStudents'];
            }

            $ap = [
                'name'              => $apData['name'],
                'enrolledStudents'  => $totalEnrollments,
            ];

            $labels[] = $apData['name'];
            $series[] = $totalEnrollments;

            $result = "'" . implode("', '", $labels) . "'";

            $ap = [
                'label'  => $apData['name'],
                'serie'  => $totalEnrollments,
            ];

            $aps[] = $ap;
        }


        if (empty($aps)) {
            return [];
        } else {

            $students = User::where('student_id', '>', 0)->get();
            return $data = [
                'labels'        => $labels,
                'series'        => $series,
                'aps'           => $aps,
                'total'         => $totalActive,
                'maxStudents'   => $students->count(),
            ];
        }
    }


    public static function deEnroll($programID, $userID)
    {

        $programCourses = ProgramCourses::where('programID', $programID)->get();

        foreach ($programCourses as $programCourse) {
            $courseIDs[] = $programCourse->courseID;
        }
        $runningClasses = Classes::whereIn('courseID', $courseIDs)->get();

        foreach ($runningClasses as $runningClass) {
            $classIDs[] = $runningClass->id;
        }

        $enrollments = Enrollment::whereIn('classID', $classIDs)->where('userID', $userID)->get();

        foreach ($enrollments as $enrollment) {
            $enrollment->delete();
        }
    }
}
