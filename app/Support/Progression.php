<?php

namespace App\Support;

use App\Models\Academics\AcademicPeriods;
use App\Models\Academics\ClassAssessment;
use App\Models\Academics\Programs;
use App\Models\GradeBook;

class Progression
{



    # returns the students progression status
    public static function checkProgression($data)
    {

        # Check the progression of the student from the comments
        # Return an array of courses to be repeated

        /**
         * 0 - Clear Pass Proceed
         * 1 - Proceed with repeat
         * 2 - Repeat courses failed and pay for each and cant pick next course
         * 3 - Repeat year || Exclude
         *
         */
        if (isset($data['coursesFailedCount'])) {
            if ($data['coursesFailedCount'] < 1) {

                $courses     = $data['coursesFailed'];
                $progression = '0';
            }
            if ($data['coursesFailedCount'] > 0 && $data['coursesFailedCount'] <= 2) {

                $courses     = $data['coursesFailed'];
                $progression = '1';
            }
            if ($data['coursesFailedCount'] == 3) {

                $courses = $data['coursesFailed'];
                $progression = '2';
            }

            if ($data['coursesFailedCount'] > 3) {

                //  $courses = $data['coursesFailed'];
                $courses = array_merge($data['coursesFailed'],$data['coursesPassed']); //with comment Repeat year
                $progression = '3';
            }
        } else {

            $courses     = (array)[];
            $progression = 0;
        }



        return $content = [
            'courses'          => $courses,
            'progression'      => $progression,
        ];
    }
    # Returns the total mark a student in a particular class
    public static function calculateTotalGrade($user_id, $classID)
    {
        $totalMark = array();
        # Get the total of all class assessments and all classAssesments ids to place in a array
        $classAssesments = ClassAssessment::where('classID', $classID)->get();

        if ($classAssesments) {
            #$classAssesmentsTotal = $classAssesments->sum('total');

            foreach ($classAssesments as $assesment) {
                $classAssesmentsIDs[] = $assesment->id;
            }
            if (!empty($classAssesmentsIDs)) {
                # From the gradebooks get the users marks scored against the class assesment
                $multiply      = 0;
                $marks         = GradeBook::where('userID', $user_id)->wherein('classAssessmentID', $classAssesmentsIDs)->get()->unique('classAssessmentID'); //->get();
                $totalMarks    = $marks->sum('grade');
                $marksType     = GradeBook::where('userID', $user_id)->wherein('classAssessmentID', $classAssesmentsIDs)->first();

                if (!empty($marksType)) {
                    $multiply = $marksType->gradeType;
                } else {
                    $multiply = 0;
                }


                $totalMark = ['type' => $multiply, 'mark' => $totalMarks];
                //$totalMark[1] = 50;
                return  $totalMark;
            }
        } else {
            return 0;
        }
    }
    public static function score($mark)
    {
        //  $marks = 0;
        $grade = '';
        //$marks =  $mark[1];

        // Grading 1 = UNZA;
        // Grading 2 = Genral


        if ($mark['type'] == 0) {
            if ($mark['mark'] == 0) {
                $grade = 'Not Examined';
            }
            if ($mark['mark'] == -1) {
                $grade = 'Exempted';
            }
            if ($mark['mark'] == -2) {
                $grade = 'Withdrew with Permission';
            }
            if ($mark['mark'] == -3) {
                $grade = 'Disqualified';
            } else if ($mark['mark'] == 0) {
                $grade = 'NE';
            } else if ($mark['mark'] >= 1 && $mark['mark'] <= 39) {
                $grade = 'D';
            } else if ($mark['mark'] >= 40 && $mark['mark'] <= 49) {
                $grade = 'D+';
            } else if ($mark['mark'] >= 50 && $mark['mark'] <= 55) {
                $grade = 'C';
            } else if ($mark['mark'] >= 56 && $mark['mark'] <= 61) {
                $grade = 'C+';
            } else if ($mark['mark'] >= 62 && $mark['mark'] <= 67) {
                $grade = 'B';
            } else if ($mark['mark'] >= 68 && $mark['mark'] <= 75) {
                $grade = 'B+';
            } else if ($mark['mark'] >= 76 && $mark['mark'] <= 85) {
                $grade = 'A';
            } else if ($mark['mark'] >= 86 && $mark['mark'] <= 100) {
                $grade = 'A+';
            }
        } else if ($mark['type'] == 1) {
            if ($mark['mark'] == 0) {
                $grade = 'Not Examined';
            }
            if ($mark['mark'] == -1) {
                $grade = 'Exempted';
            }
            if ($mark['mark'] == -2) {
                $grade = 'Withdrew with Permission';
            }
            if ($mark['mark'] == -3) {
                $grade = 'Disqualified';
            }
            if ($mark['mark'] == -4) {
                $grade = 'Deferred';
            }
            if ($mark['mark'] == -5) {
                $grade = 'Changed Mode of Study';
            } else if ($mark['mark'] == 0) {
                $grade = 'NE';
            } else if ($mark['mark'] >= 1 && $mark['mark'] <= 29) {
                $grade = 'D';
            } else if ($mark['mark'] >= 30 && $mark['mark'] <= 39) {
                $grade = 'D+';
            } else if ($mark['mark'] >= 40 && $mark['mark'] <= 45) {
                $grade = 'C';
            } else if ($mark['mark'] >= 46 && $mark['mark'] <= 55) {
                $grade = 'C+';
            } else if ($mark['mark'] >= 56 && $mark['mark'] <= 65) {
                $grade = 'B';
            } else if ($mark['mark'] >= 66 && $mark['mark'] <= 75) {
                $grade = 'B+';
            } else if ($mark['mark'] >= 76 && $mark['mark'] <= 85) {
                $grade = 'A';
            } else if ($mark['mark'] >= 86 && $mark['mark'] <= 100) {
                $grade = 'A+';
            }
        }

        return $grade;

    }
    public static function comments($user_id, $academicPeriodID)
    {

        // check all couses failed if they have passed in the current academic period.

        $classes        = AcademicPeriods::myclasses($user_id, $academicPeriodID,0);  #1 parametter meets new requirements if one get all past classes
        # Count the number of courses attempted
        //$courseCount    = count($classes);
        if ($classes !== null) {
            $courseCount = count($classes);
        } else {
            $courseCount = 0; // Or handle the absence of classes in another appropriate way
        }

        $courses        = (object)$classes;

        # Filter passed and failed courses
        $passedCourse = 0;
        $failedCourse = 0;

        $marksType     = GradeBook::where('userID', $user_id)->get()->last();

        $gradeType     = '';

        if (!empty($marksType)) {
            $gradeType = $marksType->gradeType;
        } else {
            $gradeType = 0;
        }

        /*----*/
        foreach ($courses as $course) {

            if ($course['gradeType'] == 1) {

                if ($course['total_score'] >= 40 || $course['total_score'] == -1) { # -1 is for exeptions

                    $coursePassed = $course['course_code'];
                    $passedCourse = $passedCourse + 1;

                    $coursesPassed[]      = $coursePassed;
                    $coursesPassedArray[] = $course;

                    $passedCourses[]      = $passedCourse;
                } else {

                    // Check if course was taken before and has been cleared now

                    $courseFailed                   = $course['course_code'];
                    $courseFailedArray[]            = $course;
                    $failedCourse                   = $failedCourse + 1;
                    $coursesFailed[]                = $courseFailed;
                }
            } else {

                if ($course['total_score'] >= 50 || $course['total_score'] == -1) { # -1 is for exeptions

                    $coursePassed = $course['course_code'];
                    $passedCourse = $passedCourse + 1;

                    $coursesPassed[]      = $coursePassed;
                    $coursesPassedArray[] = $course;

                    $passedCourses[]      = $passedCourse;
                } else {

                    $courseFailed                   = $course['course_code'];
                    $courseFailedArray[]            = $course;
                    $failedCourse                   = $failedCourse + 1;
                    $coursesFailed[]                = $courseFailed;
                }
            }
        }

        if ($courseCount == $failedCourse) {

            foreach ($courses as $course) {
                if ($course['total_score'] == 0) {
                    $comment = '';
                } else {
                    $comment = 'Part Time';
                    //$comment = 'Repeat year';
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
            //$coursesToRepeat = array_merge($data['coursesFailed'],$data['coursesPassed']); //with comment Repeat year
            // $comment = 'Repeat Year ';
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

        return $data = [
            'comment'            => $comment,
            'coursesPassed'      => $coursesPassedArray,
            'coursesPassedCount' => $passedCourse,
            'coursesFailed'      => $courseFailedArray,
            'coursesFailedCount' => $failedCourse,
        ];
    }

    public static function classCalcualtor($nextClasses, $failedClasses, $passedClasses)
    {
        // dd($nextClasses,$failedClasses,$passedClasses);

        # Check through all next classes and find which ones have prerequisites.
        if (count($nextClasses)>0) {
            for ($i = 0; $i < count($nextClasses); $i++) {
                $nClasses = $nextClasses[$i];
                for ($j = 0; $j < count($failedClasses); $j++) {
                    $fClass = $failedClasses[$j];
                    foreach ($nClasses['prerequisiteCode'] as $nClass) {
                        if ($nClass['prerequisiteCode'] == $fClass['course_code']) {
                            unset($nextClasses[$i]);
                            break;
                        }
                    }
                }
            }
            for ($j = 0; $j < count($failedClasses); $j++) {
                $fClass = $failedClasses[$j];
                foreach ($nClasses['prerequisiteCode'] as $nClass) {
                    if ($nClass['prerequisiteCode'] == $fClass['course_code']) {
                        unset($nextClasses[$i]);
                        break;
                    }
                }
            }


            foreach ($nextClasses as $nClasses) {

                # Remove failed Courses
                foreach ($failedClasses as $fClass) {

                    if ($fClass['course_code'] == $nClasses['course_code']) {

                        # Remove this class from the nextClassesArray
                        $_ = array_search($nClasses['course_code'], $nextClasses);
                        unset($nextClasses[$_]);
                    }
                }

                # remove passed courses
                foreach ($passedClasses as $pClass) {
                    if ($pClass['course_code'] == $nClasses['course_code']) {

                        # Remove this class from the nextClassesArray
                        $_ = array_search($nClasses['course_code'], $nextClasses);
                        unset($nextClasses[$_]);
                    }
                }

            }

            # Return eligible next classes

        }
        return $nextClasses;
    }

    /** Returns student progression status.*/
    public function getStudentProgress($userID, $programID)
    {

        # find student program and courses
        $program        = Programs::find($programID);

    }
}