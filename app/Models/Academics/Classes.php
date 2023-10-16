<?php

namespace App\Models\Academics;

use App\Models\Admissions\UserProgram;
use App\Models\Enrollment;
use App\Models\User;
use App\Support\Finance\Accounting\Accounting;
use App\Support\General;
use App\Support\Progression;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'ac_classes';
    protected $fillable = ['instructorID','courseID','academicPeriodID'];
    protected $primaryKey = 'id';
    use HasFactory;

    public function academicPeriod()
    {
        return $this->belongsTo(AcademicPeriods::class, 'academicPeriodID','id');
    }

    // Define the relationship with ac_courses table
    public function course()
    {
        return $this->belongsTo(Courses::class, 'courseID','id');
    }
    public function classUser()
    {
        return $this->hasMany(User::class, 'userID', 'id');
    }
    public function assessments()
    {
        return $this->hasMany(ClassAssessment::class, 'classID','id');
    }
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructorID', 'id');
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'classID','id');
    }
    public function academics()
    {
        return $this->belongsTo(AcademicPeriods::class, 'academicPeriodID');
    }
    public function students()
    {
        return $this->belongsToMany(User::class, 'ac_enrollments', 'classID', 'userID')->orderBy('student_id', 'desc');
    }

    public static function data($id, $withoutStudents = 0, $user_id = null)
    {

        $class  = Classes::find($id);
        $marks  = Progression::calculateTotalGrade($user_id, $id);


        if (!empty($marks)) {
            $pIDs = DB::table('ac_userPrograms')
                ->where('userID', '=', $user_id)
                ->pluck('programID');
            if ($pIDs->contains(32)) {
                $marks['type'] = 0;
            } else {
                $marks['type'] = 1;
            }
            $mark      = $marks['mark'];
            $grade     = Progression::score($marks);
            $gradeType = $marks['type'];
        } else {
            $mark       = 'NE';
            $grade      = 'NE';
            $gradeType  = '';
        }


        if ($withoutStudents == 1) {
            foreach ($class->students as $_student) {
                $_student = General::jsondata($_student->id);
                $students[] = $_student;
            }
            if (empty($students)) {
                $students = [];
            }

            $_class = [
                'key'                       => $class->id,
                'courseID'                  => $class->courseID,
                'course_code'               => $class->course->code,
                'course_name'               => $class->course->name,
                'academicPeriod'            => $class->academicPeriod,
                'room'                      => $class->roomID,
                'instructor_email'          => $class->instructor->email,
                'instructor'                => $class->instructor->first_name . ' ' . $class->instructor->middle_name . ' ' . $class->instructor->last_name,
                'students'                  => $students,
                'enrolledStudentsCount'     => $class->students->count(),
                'instructorAvatar'          => '/user_photos/' . $class->instructor->image,
            ];
            return $_class;
        } else {

            $currentAPID = General::getCurrentAcademicPeriodID($user_id);
            $balance     = Accounting::user_balance($user_id);


//            if ($balance > 0 && $currentAPID == $class->academicPeriodID) {
//
//                 $grade = 'Results Held';
//            }

            $prerequisiteCode =[];

            $pCourses= Prerequisite::where('courseID',$class->courseID)->get();

            if(!empty($pCourses)){

                foreach($pCourses as  $pCourse){

                    if(!empty($pCourse->prerequisiteID)){
                        $mCourse = Courses::where('id',$pCourse->prerequisiteID)->first();

                        $prerequisiteCode[] = [
                            'id' => $mCourse->id,
                            'prerequisiteCode' =>$mCourse->code
                        ];

                    }else{

                        $prerequisiteCode = '';
                    }



                }

            }else{
                $prerequisiteCode =[];

            }



            return [
                'key'                       => $class->id,
                'course_code'               => $class->course->code,
                'courseID'                  => $class->courseID,
                'course_name'               => $class->course->name,
                'academicPeriod'            => $class->academicPeriod,
                'room'                      => $class->roomID,
                'instructor_email'          => $class->instructor->email,
                'instructor'                => $class->instructor->first_name . ' ' . $class->instructor->middle_name . ' ' . $class->instructor->last_name,
                'enrolledStudentsCount'     => $class->students->count(),
                'instructorAvatar'          => "/user_photos/" . $class->instructor->image,
                'total_score'               => $mark,
                'grade'                     => $grade,
                'gradeType'                 => $gradeType,
                'prerequisiteCode'          => $prerequisiteCode,
            ];




        }
    }

    public static function dataMini($id, $withoutStudents = 0, $user_id = null)
    {

        $class  = Classes::find($id);
        if (!empty($class)) {
            $marks  = Progression::calculateTotalGrade($user_id, $id);

            if (!empty($marks)) {
                $mark   = $marks['mark'];
                $grade  = Progression::score($marks);
            } else {
                $mark   = 'NE';
                $grade  = 'NE';
            }


            if ($withoutStudents == 1) {
                foreach ($class->students as $_student) {
                    $_student = General::jsondataMini($_student->id);
                    $students[] = $_student;
                }
                if (empty($students)) {
                    $students = [];
                }

                $_class = [
                    'key'                       => $class->id,
                    'courseID'                  => $class->courseID,
                    'course_code'               => $class->course->code,
                    'course_name'               => $class->course->name,
                    'academicPeriod'            => $class->academicPeriod,
                    'room'                      => $class->roomID,
                    'instructor_email'          => $class->instructor->email,
                    'instructor'                => $class->instructor->first_name . ' ' . $class->instructor->middle_name . ' ' . $class->instructor->last_name,
                    'students'                  => $students,
                    'enrolledStudentsCount'     => $class->students->count(),
                    'instructorAvatar'          => '/user_photos/' . $class->instructor->image,
                ];
                return $_class;
            } else {

                if ($class && $class->course->prerequisite) {
                    $prerequisiteCode =  $class->course->prerequisite->code;
                } else {
                    $prerequisiteCode = '';
                }

                $_class = [
                    'key'                       => $class->id,
                    'course_code'               => $class->course->code,
                    'courseID'                  => $class->courseID,
                    'course_name'               => $class->course->name,
                    'academicPeriod'            => $class->academicPeriod,
                    'room'                      => $class->roomID,
                    'instructor_email'          => $class->instructor->email,
                    'instructor'                => $class->instructor->first_name . ' ' . $class->instructor->middle_name . ' ' . $class->instructor->last_name,
                    'enrolledStudentsCount'     => $class->students->count(),
                    'instructorAvatar'          => "/user_photos/" . $class->instructor->image,
                    'total_score'               => $mark,
                    'grade'                     => $grade,
                    'prerequisiteCode'          => $prerequisiteCode,
                ];
                return $_class;
            }
        }
    }

    public static function dataMiniByProgram($id, $withoutStudents = 0, $user_id = null, $programID = null)
    {

        $class  = Classes::find($id);
        $marks  = Progression::calculateTotalGrade($user_id, $id);

        if (!empty($marks)) {
            $mark   = $marks['mark'];
            $grade  = Progression::score($marks);
        } else {
            $mark   = 'NE';
            $grade  = 'NE';
        }


        if ($withoutStudents == 1) {
            foreach ($class->students as $_student) {
                $userProgram = UserProgram::where('userID', $_student->id)->where('programID', $programID)->get()->first();
                if ($userProgram && $userProgram->programID == $programID) {
                    $_student = General::jsondataMini($_student->id);
                    $students[] = $_student;

                    if ($_student['paymentPlanData'] && $_student['paymentPlanData']['canAttendClass'] == 1) {
                        $studentsPaid[] = $_student;
                    } else {
                        $studentsUnPaid[] = $_student;
                    }
                }
            }
            if (empty($students)) {
                $students = [];
                $studentsCount = 0;
            } else {
                $studentsCount = count($students);
            }

            if (empty($studentsPaid)) {
                $studentsPaid = [];
            }
            if (empty($studentsUnPaid)) {
                $studentsUnPaid = [];
            }

            $_class = [
                'key'                       => $class->id,
                'courseID'                  => $class->courseID,
                'course_code'               => $class->course->code,
                'course_name'               => $class->course->name,
                'academicPeriod'            => $class->academicPeriod,
                'room'                      => $class->roomID,
                'instructor_email'          => $class->instructor->email,
                'instructor'                => $class->instructor->first_name . ' ' . $class->instructor->middle_name . ' ' . $class->instructor->last_name,
                'students'                  => $students,
                'studentsPaid'              => $studentsPaid,
                'studentsUnPaid'            => $studentsUnPaid,
                'enrolledStudentsCount'     => $studentsCount,
                'instructorAvatar'          => '/user_photos/' . $class->instructor->image,
            ];
            return $_class;
        } else {

            foreach ($class->students as $_student) {
                $userProgram = UserProgram::where('userID', $_student->id)->where('programID', $programID)->get()->first();
                if ($userProgram && $userProgram->programID == $programID) {
                    $_student = User::find($_student->id);
                    $students[] = $_student;
                }
            }
            if (empty($students)) {
                $students = [];
                $studentsCount = 0;
            } else {
                $studentsCount = count($students);
            }


            if ($class->course->prerequisite) {
                $prerequisiteCode =  $class->course->prerequisite->code;
            } else {
                $prerequisiteCode = '';
            }

            $_class = [
                'key'                       => $class->id,
                'course_code'               => $class->course->code,
                'courseID'                  => $class->courseID,
                'course_name'               => $class->course->name,
                'academicPeriod'            => $class->academicPeriod,
                'room'                      => $class->roomID,
                'instructor_email'          => $class->instructor->email,
                'instructor'                => $class->instructor->first_name . ' ' . $class->instructor->middle_name . ' ' . $class->instructor->last_name,
                'enrolledStudentsCount'     => $studentsCount,
                'instructorAvatar'          => "/user_photos/" . $class->instructor->image,
                'total_score'               => $mark,
                'grade'                     => $grade,
                'prerequisiteCode'          => $prerequisiteCode,
            ];
            return $_class;
        }
    }

    public static function dataMiniSorted($id, $withoutStudents = 0, $user_id = null, $programID = null)
    {

        $class  = Classes::find($id);
        $marks  = Progression::calculateTotalGrade($user_id, $id);

        if (!empty($marks)) {
            $mark   = $marks['mark'];
            $grade  = Progression::score($marks);
        } else {
            $mark   = 'NE';
            $grade  = 'NE';
        }


        if ($withoutStudents == 1) {
            foreach ($class->students as $_student) {
                $userProgram = UserProgram::where('userID', $_student->id)->get()->first();
                if ($userProgram) {
                    $_student = General::jsondataMini($_student->id);
                    $students[] = $_student;
                    if (!empty($_student['paymentPlanData'])) {
                        $paymentPlan = $_student['paymentPlanData'];
                        if ($_student['paymentPlanData']['canAttendClass'] == 1) {
                            $studentsPaid[] = $_student;
                        }
                        else{
                            $studentsUnPaid[] = $_student;
                        }
                    } else {
                        $studentsUnPaid[] = $_student;
                    }
                }
            }
            if (empty($students)) {
                $students = [];
                $studentsCount = 0;
            } else {
                $studentsCount = count($students);
            }

            if (empty($studentsPaid)) {
                $studentsPaid = [];
            }
            if (empty($studentsUnPaid)) {
                $studentsUnPaid = [];
            }

            $_class = [
                'key'                       => $class->id,
                'courseID'                  => $class->courseID,
                'course_code'               => $class->course->code,
                'course_name'               => $class->course->name,
                'academicPeriod'            => $class->academicPeriod,
                'room'                      => $class->roomID,
                'instructor_email'          => $class->instructor->email,
                'instructor'                => $class->instructor->first_name . ' ' . $class->instructor->middle_name . ' ' . $class->instructor->last_name,
                'students'                  => $students,
                'sortedList'                => Classes::sortStudentList($students),
                'studentsPaid'              => $studentsPaid,
                'studentsUnPaid'            => $studentsUnPaid,
                'enrolledStudentsCount'     => $studentsCount,
                'paymentPlan'               => $paymentPlan,
                'instructorAvatar'          => '/user_photos/' . $class->instructor->image,
            ];
            return $_class;
        } else {

            foreach ($class->students as $_student) {
                $userProgram = UserProgram::where('userID', $_student->id)->where('programID', $programID)->get()->first();
                if ($userProgram && $userProgram->programID == $programID) {
                    $_student = User::find($_student->id);
                    $students[] = $_student;
                }
            }
            if (empty($students)) {
                $students = [];
                $studentsCount = 0;
            } else {
                $studentsCount = count($students);
            }

            if ($class->course->prerequisite) {
                $prerequisiteCode =  $class->course->prerequisite->code;
            } else {
                $prerequisiteCode = '';
            }

            $_class = [
                'key'                       => $class->id,
                'course_code'               => $class->course->code,
                'courseID'                  => $class->courseID,
                'course_name'               => $class->course->name,
                'academicPeriod'            => $class->academicPeriod,
                'room'                      => $class->roomID,
                'instructor_email'          => $class->instructor->email,
                'instructor'                => $class->instructor->first_name . ' ' . $class->instructor->middle_name . ' ' . $class->instructor->last_name,
                'enrolledStudentsCount'     => $studentsCount,
                'instructorAvatar'          => "/user_photos/" . $class->instructor->image,
                'total_score'               => $mark,
                'grade'                     => $grade,
                'prerequisiteCode'          => $prerequisiteCode,
            ];
            return $_class;
        }
    }

    public static function sortStudentList($students)
    {
        foreach ($students as $student) {
            $programs[] = $student['currentProgramID'];
        }
        $uniquePrograms = array_unique($programs);

        if (!empty($uniquePrograms)) {

            foreach ($uniquePrograms as $up) {

                $canAttendClass    = [];
                $canNotAttendClass = [];

                foreach ($students as $student) {

                    if ($student['currentProgramID'] == $up) {
                        $identifiedStudents[] = $student;
                        if (!empty($student['paymentPlanData'])){
                            if ($student['paymentPlanData']['canAttendClass'] == 1) {
                                $canAttendClass[] = $student;
                            }else {
                                $canNotAttendClass[] = $student;
                            }
                        }

                    }

                }
                $program = Programs::find($up);
                $uProgram = [
                    'id'                        => $program->id,
                    'code'                      => $program->code,
                    'name'                      => $program->name,
                    'identifiedStudents'        => $identifiedStudents,
                    'identifiedStudentsTotal'   => count($identifiedStudents),
                    'canAttendClass'            => $canAttendClass,
                    'canAttendClassTotal'       => count($canAttendClass),
                    'canNotAttendClass'         => $canNotAttendClass,
                    'canNotAttendClassTotal'    => count($canNotAttendClass),
                ];
                unset($canAttendClass,$canNotAttendClass,$identifiedStudents);
                $sortedPrograms[] = $uProgram;

            }

        }
        return $sortedPrograms;
    }

    public static function dataMini2($id, $withoutStudents = 0, $user_id = null)
    {

        $class  = Classes::find($id);


        if ($class->course->prerequisite) {
            $prerequisiteCode =  $class->course->prerequisite->code;
        } else {
            $prerequisiteCode = '';
        }

        $_class = [
            'key'                       => $class->id,
            'course_code'               => $class->course->code,
            'courseID'                  => $class->courseID,
            'course_name'               => $class->course->name,
            'academicPeriod'            => $class->academicPeriod,
            'room'                      => $class->roomID,
            'instructor_email'          => $class->instructor->email,
            'instructor'                => $class->instructor->first_name . ' ' . $class->instructor->middle_name . ' ' . $class->instructor->last_name,
            'enrolledStudentsCount'     => $class->students->count(),
            'instructorAvatar'          => "/user_photos/" . $class->instructor->image,
            'prerequisiteCode'          => $prerequisiteCode,
        ];
        return $_class;
    }
}
