<?php

namespace App\Traits\Academics;

use App\Models\Academic\AcClass;
use App\Models\Academic\Course;
use App\Models\Academic\GradeBookImport;
use App\Traits\Enrollment\Progression;
use App\Traits\User\General;
use App\User;
use Notification;
use Illuminate\Http\Request;


trait BoardOfExaminer
{

    public static function sortResultsImport($academicPeriodID, $programID)
    {

        $studentRows            = GradeBookImport::where('programID', $programID)->where('academicPeriodID', $academicPeriodID)->get()->unique('studentID');
        $publishedStudents      = [];
        $unPublishedStudents    = [];

        foreach ($studentRows as $studentRow) {
            $user = User::where('student_id', $studentRow->studentID)->get()->first();
            if ($user) {

                // Sort courses
                $userCourses = GradeBookImport::where('programID', $programID)->where('academicPeriodID', $academicPeriodID)->where('studentID', $user->student_id)->get();

                foreach ($userCourses as $importCourse) {

                if ($programID == 32){
                    $mark = [
                        'type'  => 0,
                        'mark'  => $importCourse->total,
                    ];
                }else{
                    $mark = [
                        'type'  => 1,
                        'mark'  => $importCourse->total,
                    ];
                }

                    if ($importCourse->published == 0) {
                        $status = 'Not Published';
                    }
                    if ($importCourse->published == 1) {
                        $status = 'Published';
                    }
                    if ($importCourse->published == -1) {
                        $status = 'Course Not enrolled';
                    }
                    if ($importCourse->published == -2) {
                        $status = 'No Enrollments';
                    }



                    $course = [
                        'courseCode'    => $importCourse->code,
                        'courseTitle'   => $importCourse->title,
                        'status'        => $status,
                        'score'         => Progression::score($mark),
                        'total'         => $importCourse->total,
                    ];

                    $importedCourses[]  = $course;
                }

                if (empty($importedCourses)) {
                    $importedCourses = [];
                }

                $userJsonFormat = General::jsondataMini($user->id);
                $comments = BoardOfExaminer::comments($importedCourses);

                // check which courses will be published and provide status

                /* Get all courses running in the specified academic period and find the course code foreach

                Also, get students uploaded courses

                */
                $runningClasses = AcClass::where('academicPeriodID', $academicPeriodID)->get();

                $coursesToPublish    = [];
                $publishingStatus    = '';
                $coursesNotToPublish = [];
                $publishingComments  = [];

                foreach ($runningClasses as $rClass) {
                    $runningCourseCode = Course::where('id', $rClass->courseID)->get()->first();

                    foreach ($userCourses as $uc) {
                        if ($runningCourseCode->code == $uc->code) {
                            $coursesToPublish      = $uc->code;
                        } else {
                            $coursesNotToPublish[] = $uc->code;
                        }
                    }
                }

                if (!empty($coursesNotToPublish)) {
                    $publishingCourses  = $coursesNotToPublish;
                    $publishingComments = 'Course(s) not found in AC';
                } else {
                    $publishingComments = 'Proceed to Publishing';
                }





                $user = [
                    'key'                => $userJsonFormat['key'],
                    'id'                 => $userJsonFormat['id'],
                    'first_name'         => $userJsonFormat['first_name'],
                    'middle_name'        => $userJsonFormat['middle_name'],
                    'last_name'          => $userJsonFormat['last_name'],
                    'names'              => $userJsonFormat['names'],
                    'student_id'         => $userJsonFormat['student_id'],
                    'currentProgramName' => $userJsonFormat['currentProgramName'],
                    'currentMode'        => $userJsonFormat['currentMode'],
                    'email'              => $userJsonFormat['email'],
                    'gender'             => $userJsonFormat['gender'],
                    'courses'            => $importedCourses,
                    'comments'           => $comments,
                    'comment'            => $comments['comment'],
                    'publishingCourses'  => $coursesNotToPublish,
                    'publishingComments' => $publishingComments,
                    'status'             => $status,
                ];

                unset($importedCourses);
                unset($coursesNotToPublish);
                unset($publishingComments);

                switch ($status) {
                    case 'Published':
                        $publishedStudents[] = $user;
                        break;
                    case 'Not Published':
                        $unPublishedStudents[] = $user;
                        break;

                    default:

                        break;
                }


            }
        }

        return [
            'publishedStudents'     => $publishedStudents,
            'unPublishedStudents'   => $unPublishedStudents,
        ];


    }


    public static function comments($courses)
    {


        $classes        = $courses;
        # Count the number of courses attempted
        $courseCount    = count($classes);
        $courses        = (object)$classes;

        # Filter passed and failed courses
        $passedCourse = 0;
        $failedCourse = 0;


        $gradeType = 1;

        if ($gradeType == 1) {
            foreach ($courses as $course) {

                if ($course['total'] >= 40 || $course['total'] < 0) { # -1 is for exeptions

                    $coursePassed = $course['courseCode'];
                    $passedCourse = $passedCourse + 1;

                    $coursesPassed[]      = $coursePassed;
                    $coursesPassedArray[] = $course;

                    $passedCourses[]      = $passedCourse;
                } else {

                    $courseFailed                   = $course['courseCode'];
                    $courseFailedArray[]            = $course;
                    $failedCourse                   = $failedCourse + 1;
                    $coursesFailed[]                = $courseFailed;
                }
            }

            if ($courseCount == $failedCourse) {

                foreach ($courses as $course) {
                    if ($course['total'] == 0) {
                        $comment = '';
                    } else {
                        $comment = 'Repeat year';
                    }
                }
            }

            if ($passedCourse == $courseCount) {
                $comment = "Clear Pass";
            }
            if ($courseCount - 1 == $passedCourse) {
                $coursesToRepeat = implode(", ", $coursesFailed);
                $comment = 'Proceed, RPT ' . $coursesToRepeat;
            }
            if ($courseCount - 2 == $passedCourse) {
                $coursesToRepeat = implode(", ", $coursesFailed);
                $comment = 'Proceed, RPT ' . $coursesToRepeat;
            }

            if ($courseCount - 3 == $passedCourse) {
                $coursesToRepeat = implode(", ", $coursesFailed);
                $comment = 'Part time ' . $coursesToRepeat;
            }
            if ($courseCount - 4 == $passedCourse) {
                $coursesToRepeat = implode(", ", $coursesFailed);
                $comment = 'Part time ' . $coursesToRepeat;
            }


            if (empty($comment)) {
                $comment = 'No Comment';
            }


            if (empty($courseFailedArray)) {
                $courseFailedArray = [];
            }
            if (empty($coursesPassedArray)) {
                $coursesPassedArray = [];
            }
        } else if ($gradeType == 0) {
            foreach ($courses as $course) {

                if ($course['total'] >= 50 || $course['total'] < 0) { # -1 is for exeptions

                    $coursePassed = $course['courseCode'];
                    $passedCourse = $passedCourse + 1;

                    $coursesPassed[]      = $coursePassed;
                    $coursesPassedArray[] = $course;

                    $passedCourses[]      = $passedCourse;
                } else {

                    $courseFailed                   = $course['courseCode'];
                    $courseFailedArray[]            = $course;
                    $failedCourse                   = $failedCourse + 1;
                    $coursesFailed[]                = $courseFailed;
                }
            }

            if ($courseCount == $failedCourse) {
                foreach ($courses as $course) {
                    if ($course['total'] == 0) {
                        $comment = '';
                    } else {
                        $comment = 'Repeat year';
                    }
                }
            }
            if ($passedCourse == $courseCount) {
                $comment = "Clear Pass";
            }
            if ($courseCount - 1 == $passedCourse) {
                $coursesToRepeat = implode(", ", $coursesFailed);
                $comment = 'Proceed, RPT ' . $coursesToRepeat;
            }
            if ($courseCount - 2 == $passedCourse) {
                $coursesToRepeat = implode(", ", $coursesFailed);
                $comment = 'Proceed, RPT ' . $coursesToRepeat;
            }

            if ($courseCount - 3 == $passedCourse) {
                $coursesToRepeat = implode(", ", $coursesFailed);
                $comment = 'Part time ' . $coursesToRepeat;
            }
            if ($courseCount - 4 == $passedCourse) {
                $coursesToRepeat = implode(", ", $coursesFailed);
                $comment = 'Part time ' . $coursesToRepeat;
            }


            if (empty($comment)) {
                $comment = '';
            }


            if (empty($courseFailedArray)) {
                $courseFailedArray = [];
            }

            if (empty($coursesPassedArray)) {
                $coursesPassedArray = [];
            }
        }

        return $data = [
            'comment'            => $comment,
            'coursesPassed'      => $coursesPassedArray,
            'coursesPassedCount' => $passedCourse,
            'coursesFailed'      => $courseFailedArray,
            'coursesFailedCount' => $failedCourse,
        ];
    }


    public static function courseClearance($courseCode, $academicPeriodID)
    {

        // Check if student registered for this course
        // drop all courses that student registered for
        // Find course and class
        $course = Course::where('code', $courseCode)->get()->first();
        $status = 0;
        if ($course) {
            // Find class and check if its running in academic period
            $class = AcClass::where('courseID', $course->id)->where('academicPeriodID', $academicPeriodID)->get()->first();
            if ($class) {
                $status = 1;
            }
        } else {
            $status = 0;
        }

        return $status;
    }
}
