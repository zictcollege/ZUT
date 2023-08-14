<?php

namespace App\Traits\User;

use App\Models\Academic\AcademicPeriod;
use App\Models\Academic\AcClass as AcademicAcClass;
use App\Models\Academic\Course;
use App\Models\Academic\Exemption as AcademicExemption;
use App\Models\Academic\Enrollment;
use App\Models\Academic\ExemptionAttachment;
use App\Models\Academic\ExemptionCourse;
use App\Models\Academic\Program;
use App\Traits\Enrollment\ClassEnrollment;
use App\Traits\User\General;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


trait Exemption
{

    public function getProgramCourses(Request $request)
    {
        return Program::data(request('programID'));
    }
    public function startExemptionProcess(Request $request)
    {
    }
    public static function exemptionLevel($exemptionID)
    {
        $exemption          = AcademicExemption::find($exemptionID);
        $exemptionCourses   = ExemptionCourse::where('exemptionID', $exemption->id)->get();
        $program            = Program::data($exemption->programID);
        $eEourses           = [];

        if ($exemptionCourses) {
            foreach ($exemptionCourses as $exemptionCourse) {
                $eEourses[] = Course::data($exemptionCourse->courseID);
            }
        }


        $exemptionLevel = [];

        foreach ($eEourses as $exemptionCourse_) {

            foreach ($program['levels'] as $level) {
                # Check if courses exists in level 
                foreach ($level['courses'] as $lcourse) {
                    if (!empty($lcourse['code']) && !empty($exemptionCourse_['code'])) {
                        if ($lcourse['code'] == $exemptionCourse_['code']) {
                            $exemptionLevel = $level['name'];
                        }
                    }
                }
            }
        }

        return $exemptionLevel;
    }
    public function uploadAttachment(Request $request)
    {

        $file    = request('attachment');
        $fileURL = $file->store('public/exemption_attachments');
        ExemptionAttachment::create([
            'userID'  => request('user'),
            'url'     => $fileURL,
        ]);
        $uploads =  ExemptionAttachment::where('userID', request('user'))->get();
        if (!empty($uploads)) {
            foreach ($uploads as $upload) {
                $myUploads[] = ExemptionAttachment::data($upload->id);
            }
        } else {
            $myUploads = [];
        }
        return $myUploads;
    }
    public function getAttachments($userID)
    {
        $uploads =  ExemptionAttachment::where('userID', $userID)->get();
        if (!empty($uploads)) {
            foreach ($uploads as $upload) {
                $myUploads[] = ExemptionAttachment::data($upload->id);
            }
        } else {
            $myUploads = [];
        }

        if (empty($myUploads)) {
            $myUploads = [];
        }
        return $myUploads;
    }

    public function makeRecommendation(Request $request)
    {


        $this->Validate($request, array(
            'status'         => 'required',
            'note'           => 'required',
            'exemption'      => 'required',
        ));

        if (count(request('selectedRowKeys')) == 0 && request('status') == 1) {
            $errors = [
                'restriction' => 'Select the courses that you recommend for this Exemption Application.'
            ];
            return response()->json([
                'status' => 'error',
                'errors' => $errors,
            ], 422);
        }


        $exemption                          = AcademicExemption::find(request('exemption')['id']);
        $exemption->recommendationStatus    = request('status');
        $exemption->note                    = request('note');
        $exemption->recommendedBy           = request('authUser');
        $exemption->recommendationDate      = Carbon::now();
        $exemption->save();

        // Update the courses being exempted as recommended. 
        if (request('status') == '1') {
            ExemptionCourse::where('exemptionID', $exemption->id)->delete();
            foreach (request('selectedRowKeys') as $courseID) {
                ExemptionCourse::create([
                    'courseID'      => $courseID,
                    'exemptionID'   => $exemption->id,
                    'key'           => $courseID . '-' . $exemption->id,
                ]);
            }
        }

        return AcademicExemption::data($exemption->id);
    }

    public function submit(Request $request)
    {

        $this->Validate($request, array(
            'programID'         => 'required',
            'userid'            => 'required',
            'selectedCourses'   => 'required',
        ));

        $uploads =  ExemptionAttachment::where('userID', request('userid'))->get();

        if ($uploads->count() == 0) {
            // Can not submit an exemption without qualifications
            $errors = [
                'restriction' => 'Attach Statement/Transcript of previous Academic results'
            ];

            return response()->json([
                'status' => 'error',
                'errors' => $errors,
            ], 422);
        }

        $exitstingExemption = AcademicExemption::where('key', request('userid') . '-' . request('programID'))->get()->first();

        if (!empty($exitstingExemption)) {
            $errors = [
                'restriction' => 'You have already applied for an exemption in this program.'
            ];

            return response()->json([
                'status' => 'error',
                'errors' => $errors,
            ], 422);
        }

        AcademicExemption::create([
            'userID'    => request('userid'),
            'programID' => request('programID'),
            'key'       => request('userid') . '-' . request('programID'),
        ]);

        $exemption = AcademicExemption::get()->last();

        foreach (request('selectedCourses') as $courseID) {
            ExemptionCourse::create([
                'courseID'      => $courseID,
                'exemptionID'   => $exemption->id,
                'key'           => $courseID . '-' . $exemption->id,
            ]);
        }
        return Exemption::myExemptions(request('userid'));
    }
    public static function myExemptions($userID)
    {
        $exemptions = AcademicExemption::where('userID', $userID)->get();
        if (!empty($exemptions)) {
            foreach ($exemptions as $exemption) {
                $myExemptions[] = AcademicExemption::data($exemption->id);
            }
        }
        if (empty($myExemptions)) {
            $myExemptions = [];
        }
        return $myExemptions;
    }
    public function process(Request $request)
    {

        $this->Validate($request, array(
            'selectedExemption'  => 'required',
            'exemptionStatus'    => 'required',
            'authenticatedUser'  => 'required',
        ));

        $selectedExemption = request('selectedExemption');
        $exemption               = AcademicExemption::find($selectedExemption['key']);
        $user                    = User::find($exemption->userID);

        switch (request('exemptionStatus')) {
            case 'Accepted':

                $exemption->status      = 1;
                $exemption->processedBy = request('authenticatedUser');
                $exemption->save();

                $academicPeriodID       = General::getCurrentAcademicPeriodID($user->id);
                $exemptionCourses       = ExemptionCourse::where('exemptionID', $exemption->id)->get();

                # Find classes that the user is supposed to be exempted in and enroll, if not create new classes. 
                foreach ($exemptionCourses as $exemptionCourse) {
                    // Courses that the student will be exempted in 
                    $newCourses[] = $exemptionCourse->course;
                    $courseIDs[]  = $exemptionCourse->course->id;
                }

                // Are these courses running in the current academic period. 
                $existingClasses = AcademicAcClass::wherein('courseID', $courseIDs)->where('academicPeriodID', $academicPeriodID)->get();


                // Continue if all the classes exempted are running in the current academic period. 💀
                if ($existingClasses) { // $existingClasses->count() == $exemptionCourses->count()
                    # Remove old enrollments 
                    Enrollment::where('userID', $user->id)->delete();
                    ClassEnrollment::enroll($user->id, $academicPeriodID, $newCourses);

                    $enrollments = Enrollment::where('userID', $user->id)->get();

                    foreach ($enrollments as $enrollment) {
                        AcademicExemption::updateGradeBook($enrollment->id);
                    }

                    $newClasses = AcademicPeriod::nextClasses($user->id, $academicPeriodID);

                    foreach ($newClasses as $newClass) {
                        $newCourses_[] = Course::find($newClass['courseID']);
                    }
                    ClassEnrollment::enroll($user->id, $academicPeriodID, $newCourses_);

                    return [
                        'exemption' => AcademicExemption::data($exemption->id),
                        'user'      => General::jsondata($user->id),
                    ];
                } else {

                    return "Falied";
                }

                break;
            case 'Declined':

                $exemption->status      = -1;
                $exemption->processedBy = request('authenticatedUser');
                $exemption->save();

                return [
                    'exemption' => AcademicExemption::data($exemption->id),
                    'user'      => General::jsondata($user->id),
                ];

                break;
        }
    }
    public function cancleExemption(Request $request)
    {

        $application = AcademicExemption::find(request('key'));
        $courses     = ExemptionCourse::where('exemptionID', request('key'))->get();

        if ($application) {
            $application->delete();
            if ($courses) {
                foreach ($courses as $course) {
                    $course->delete();
                }
            }
        }

        return AcademicExemption::data(request('user')['id']);
    }
}
