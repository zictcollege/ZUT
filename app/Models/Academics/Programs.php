<?php

namespace App\Models\Academics;

use App\Http\Requests\StudyMode\StudyMode;
use App\Models\Admissions\ProgramCourses;
use App\Models\Admissions\UserProgram;
use App\Models\Admissions\UserStudyModes;
use App\Support\ClassEnrollment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programs extends Model
{
    protected $table = 'ac_programs';
    protected $primaryKey = 'id';
    use HasFactory;
    protected $fillable = ['code','name','departmentID','qualification_id','description'];

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Departments::class, 'departmentID');
    }

    public function courses()
    {
        return $this->belongsToMany(Courses::class, 'ac_programCourses', 'programID', 'courseID')->orderBy('code', 'asc');
    }
    public function qualification(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Qualifications::class, 'qualification_id');
    }

    public function programCourses()
    {
        return $this->hasMany(ProgramCourses::class, 'programID','id');
    }
    public static function data($id, $academicPeriodID = null, $userProgramID = null)
    {

        $program = Programs::find($id);

        if (!empty($program)) {

            if (!empty($program->qualification)) {
                $qualification =  $program->qualification->name;
            } else {
                $qualification = '';
            }

            $levels = ProgramCourses::where('programID', $program->id)->get()->unique('level_id');

            foreach ($levels as $level) {

                $levelCourses   = CourseLevels::courses($level->level_id, $program->id);
                $thisLevel      = CourseLevels::find($level->level_id);

                $_level = [
                    'name'      => $thisLevel->name,
                    'courses'   => $levelCourses,
                ];

                $mylevels[] = $_level;
            }


            if (empty($mylevels)) {
                $mylevels = [];
            }


            foreach ($program->courses->unique('code') as $course) {
                $_course = Courses::data($course->id, $id);
                $_courses[] = $_course;
            }

            if (empty($_courses)) {
                $_courses = [];
            }


            # Get number of enrollments
            if ($academicPeriodID) {
                $enrollments = ClassEnrollment::academicPeriodProgramEnrollments($program->id, $academicPeriodID);
                if (!empty($enrollments)) {
                    $enrollmentsCount = $enrollments->count();
                } else {
                    $enrollmentsCount = 0;
                }
            }

            if (empty($enrollments)) {
                $enrollments      = [];
                $enrollmentsCount = 0;
            }

            if ($userProgramID > 0) {
                $userProgram     = UserProgram::find($userProgramID);
                $userMode        = UserStudyModes::where('userID', $userProgram->userID)->get()->first();

                if (!empty($userMode)) {
                    $mode            = UserStudyModes::find($userMode->studyModeID);
                    $userStudymode   = $mode->name;
                } else {
                    $userStudymode   = 'No Study Mode Set';
                }
            } else {
                $userStudymode   = '';
            }

            if ($program) {
                // Allowed Study Modes
                $programStudyModes = ProgramStudyMode::where('program_id', $program->id)->get();
                foreach ($programStudyModes as $programStudyMode) {
                    $studyModes[] = UserStudyModes::find($programStudyMode->study_mode_id);
                }
            }
            $intakes = $program->intakes;
            if (empty($intakes)) {
                $intakes = [];
            }

            return $_program = [
                'userProgramID'      => $userProgramID,
                'id'                 => $program->id,
                'name'               => $program->name,
                'fullname'           => $program->code . ' - ' . $program->name,
                'description'        => $program->description,
                'code'               => $program->code,
                'slug'               => $program->slug,
                'qualification'      => $qualification,
                'intakes'            => $intakes,
                "intakeData"         => Programs::intakeData($program->id),
                'courses'            => $_courses,
                'department'         => $program->department->name,
                'levels'             => $mylevels,
                'enrolledStudents'   => $enrollmentsCount,
                'allowedStudyModes'  => $studyModes,
                'userStudymode'      => $userStudymode,
            ];
        } else {
            return [];
        }
    }

    public static function dataMini($id, $academicPeriodID = null, $userProgramID = null)
    {
        $userStudymode    = '';
        $enrollmentsCount = [];
        $_courses         = [];
        $studyModes       = [];
        $qualification    = '';
        $program          = Programs::find($id);

        if (!empty($program->qualification)) {
            $qualification =  $program->qualification->name;
        }

        if ($program) {
            // Allowed Study Modes
            $programStudyModes = ProgramStudyMode::where('program_id', $program->id)->get();
            foreach ($programStudyModes as $programStudyMode) {
                $studyModes[] = StudyModes::find($programStudyMode->study_mode_id);
            }
        }

        return  [
            'userProgramID'      => $userProgramID,
            'id'                 => $program->id,
            'name'               => $program->name,
            'fullname'           => $program->code . ' - ' . $program->name,
            'code'               => $program->code,
            'qualification'      => $qualification,
            'courses'            => $_courses,
            'department'         => $program->department->name,
            'levels'             => [],
            'enrolledStudents'   => $enrollmentsCount,
            'allowedStudyModes'  => $studyModes,
            'userStudymode'      => $userStudymode,
        ];
    }


    public function studymodes()
    {
        return $this->belongsToMany(StudyMode::class, 'ac_program_study_modes', 'program_id', 'study_mode_id');
    }
    public function levels()
    {
        return $this->belongsToMany(CourseLevels::class, 'ac_programCourses', 'programID', 'level_id');
    }
    public function levels_()
    {
        return $this->hasmanythrough(CourseLevels::class, ProgramCourses::class, 'programID', 'id')->orderBy('name', 'asc');
    }


    public static function viewEnrolledStudentsDataStatic($academicPeriodID, $programID)
    {
        $knownCourseIDS  = [];
        $students = ClassEnrollment::viewFullProgramEnrollmentsByProgramID($programID, $academicPeriodID);
        $program  = Program::data($programID);


        foreach ($students as $student) {
            $progressionYears[] = $student['progression']['currentLevelName'];
        }

        if (!empty($progressionYears)) {
            $progressionYearsUnique = array_unique($progressionYears);

            sort($progressionYearsUnique);

            foreach ($progressionYearsUnique as $py) {

                foreach ($students as $thisStudent) {
                    if ($py == $thisStudent['progression']['currentLevelName']) {
                        $levelStudents[] = $thisStudent;
                    }
                }

                // find classes runing on this level
                $courseLevel         = CourseLevel::where('name', $py)->get()->first();
                $knownProgramCourses = [];
                if ($courseLevel && $courseLevel->id) {
                    $knownProgramCourses = ProgramCourse::where('level_id', $courseLevel->id)->where('programID', $programID)->get();
                }


                if (empty($knownProgramCourses)) {
                    $knownCourseIDS = [];
                }

                // check for these classes created and running in the provided academic period
                foreach ($knownProgramCourses as $knownProgramCourse) {
                    $knownCourseIDS[] = $knownProgramCourse->courseID;
                }

                if (!empty($knownCourseIDS)) {
                    // check for the created classes under the provided academic period
                    $acClasses  =  AcClass::whereIn('courseID', $knownCourseIDS)->where('academicPeriodID', $academicPeriodID)->get();
                    if ($acClasses) {


                        if (empty($levelStudents)) {
                            $totalStudents = 0;
                        } else {
                            $totalStudents  = count($levelStudents);
                        }

                        if ($levelStudents) {

                            foreach ($levelStudents as $levelStudent) {

                                if ($levelStudent['paymentPlanData'] && $levelStudent['paymentPlanData']['canAttendClass'] == 1) {
                                    $studentsPaid[] = $levelStudent;
                                } else {
                                    $studentsUnPaid[] = $levelStudent;
                                }
                            }
                            # code...
                        }

                        if (empty($studentsPaid)) {
                            $studentsPaid = [];
                        }
                        if (empty($studentsUnPaid)) {
                            $studentsUnPaid = [];
                        }

                        $levels[] = [
                            'name'          => $py,
                            'students'      => $levelStudents,
                            'studentsPaid'  => $studentsPaid,
                            'studentsUnPaid' => $studentsUnPaid,
                            'total'         => $totalStudents,
                        ];

                        $classes = [];
                        $acClass = [];
                        unset($studentsPaid, $studentsUnPaid);
                        unset($classes, $acClasses, $totalStudents);
                    }
                }


                unset($levelStudents, $classes, $acClasses, $knownCourseIDS);
            }
        }

        if (empty($students)) {
            $studentsCount = 0;
        } else {
            $studentsCount = count($students);
        }

        $data = [
            'progressionYears'  => $progressionYearsUnique,
            'program'           => $program,
            'levels'            => $levels,
            'studentsCount'     => $studentsCount,
            'students'          => $students,
        ];

        return $data;
    }

    public static function intakeData($programID)
    {

        $intakes = Intakes::where('programID', $programID)->get();
        $_intakes = [];
        $mylevels = [];
        if ($intakes) {
            foreach ($intakes as $intake) {

                // Find courses attached to this intake
                $levels = ProgramCourses::where('programID', $programID)->where('programIntakeID', $intake->id)->get()->unique('level_id')->unique('programIntakeID');

                foreach ($levels as $level) {

                    $levelCourses = CourseLevels::courses($level->level_id, $programID, $intake->id);

                    $thisLevel    = CourseLevels::find($level->level_id);

                    $_level = [
                        'name'        => $thisLevel->name,
                        'courses'     => $levelCourses,
                    ];

                    $mylevels[] = $_level;
                }


                $_intakes[] = [
                    'name'        => $intake->name,
                    'levels'      => $mylevels,
                    'intakeJson'  => $intake,
                ];
                $mylevels = [];
                $levels   = [];
                unset($levels, $levelCourses, $_level, $thisLevel);
            }

            return $_intakes;
        }
    }

}
