<?php

namespace App\Traits\Academics;

use App\Models\Academics\AcademicPeriods;
use App\Models\Academics\Classes;
use App\Models\Admissions\ProgramCourses;
use App\Models\Applications\AcAddDropCourseRequests;
use App\Models\Enrollment;
use App\Traits\User\General;
use Carbon\Carbon;
use Illuminate\Http\Request;


trait AddDropCourseTrait
{
    use General;
    public function getStudentAddDropCourseData(Request $request)
    {
        $courseIDs                  = [];
        $availableClassIDs          = [];
        $availableClasses           = [];
        $programRunningCourseIDs    = [];

        $currentAPID                = self::getCurrentAcademicPeriodID(request('userID'));
        $allRunningClasses          = Classes::where('academicPeriodID', $currentAPID)->get();
        $programCourses             = ProgramCourses::where('programID', request('programID'))->get();

        foreach ($allRunningClasses as $class) {
            $courseIDs[] = $class->courseID;
        }
        foreach ($programCourses as $programCourse) {
            $programRunningCourseIDs[] = $programCourse->courseID;
        }
        if ($programRunningCourseIDs) {
            $availableClassIDs = Classes::whereIn('courseID', $programRunningCourseIDs)->where('academicPeriodID', $currentAPID)->get();
            if (!empty($availableClassIDs)) {
                foreach ($availableClassIDs as $availableClassID) {
                    if ($availableClassID && $availableClassID->id) {
                        $availableClasses[] = Classes::data($availableClassID->id);
                    }
                }
            }
        }
        return [
            'availableClasses'  => $availableClasses,
            'currentClasses'    => AcademicPeriods::myclasses(request('userID'), $currentAPID),
        ];
    }
    public function cancelApplication(Request $request)
    {
        $application = AcAddDropCourseRequests::find(request('id'));
        if ($application->status == 0) {
            $application->delete();
        }

        return AcAddDropCourseRequests::userRequests(request('userID'));
    }
    public function submitStudentRequestForm(Request $request)
    {
        $selectedAddCourseIDs  = '';
        $selectedDropCourseIDs = '';

        if (empty(request('addCoursesSelected')) && empty(request('dropCoursesSelected'))) {
            $this->Validate($request, array(
                'addCoursesSelected'  => 'required',
                'dropCoursesSelected' => 'required',
            ));
        }

        if (count(request('addCoursesSelected')) > 0) {
            $selectedAddCourseIDs  = implode(":", request('addCoursesSelected'));
        }

        if (count(request('dropCoursesSelected')) > 0) {
            $selectedDropCourseIDs = implode(":", request('dropCoursesSelected'));
        }

        AcAddDropCourseRequests::create([
            'userID'            => request('userID'),
            'academicPeriodID'  => self::getCurrentAcademicPeriodID(request('userID')),
            'programID'         => request('programID'),
            'addClassIDs'       => $selectedAddCourseIDs,
            'dropClassIDs'      => $selectedDropCourseIDs,
            'key'               => request('userID') . '-' . request('programID') . '-' . request('programID'),
            'raisedBy'          => request('authUserID'),
        ]);

        return AcAddDropCourseRequests::userRequests(request('userID'));
    }

    public function processApplication(Request $request)
    {

        $this->Validate($request, array(
            'status'                => 'required',
            'selectedApplication'   => 'required',
            'authUserID'            => 'required',
        ));

        $adr                = AcAddDropCourseRequests::find(request('selectedApplication')['id']);
        $adr->approverID    = request('authUserID');
        $adr->dateApproved  = Carbon::now();
        $adr->status        = request('status');
        $adr->save();


        $data = [
            'userID'            => $adr->userID,
            'apID'              => $adr->academicPeriodID,
            'authUserID'        => request('authUserID'),
            'addClassRequests'  => request('selectedApplication')['addClassRequests'],
            'dropClassRequests' => request('selectedApplication')['dropClassRequests'],
        ];


        if (request('status') == 1) {
            if (!empty(request('selectedApplication')['addClassRequests'])) {
                foreach (request('selectedApplication')['addClassRequests'] as $class) {
                    // return $class['key'];
                    $enrollment = Enrollment::where('userID', $adr->userID)->where('classID', $class['key'])->get()->first();
                    if (empty($enrollment)) {
                        Enrollment::create([
                            'userID'    => $adr->userID,
                            'classID'   => $class['key'],
                            'key'       => $adr->userID . '-' . $class['key'] . '-' . $adr->academicPeriodID,
                        ]);
                    }
                }
            }
            if (!empty(request('selectedApplication')['dropClassRequests'])) {
                foreach (request('selectedApplication')['dropClassRequests'] as $class) {
                    $enrollment = Enrollment::where('userID', $adr->userID)->where('classID', $class['key'])->get()->first();
                    if ($enrollment) {
                        $enrollment->delete();
                    }
                }
            }
        }
        return [
            'applications'          => AcAddDropCourseRequests::userRequests($adr->userID),
            'selectedApplication'   => AcAddDropCourseRequests::data($adr->id),
        ];
    }
}
