<?php

namespace App\Models;


use App\Models\Academics\AssessmentTypes;
use App\Models\Academics\ClassAssessment;
use App\Models\Academics\Courses;
use App\Models\Academics\Programs;
use App\Models\Academic\ExemptionAttachment;
use App\Models\Academic\ExemptionCourse;
use Illuminate\Database\Eloquent\Model;
use App\Traits\User\Exemption as AcademicsExemption;

class Exemption extends Model
{
    use  AcademicsExemption;
    protected $table = "ac_exemptions";
    protected $guarded = ['id'];

    public function users()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }
    public function courses()
    {
        return $this->hasMany(ExemptionCourse::class, 'courseID', 'id');
    }
    public function processedByDetails()
    {
        return $this->belongsTo(User::class, 'processedBy', 'id');
    }
    public function recommendedByDetails()
    {
        return $this->belongsTo(User::class, 'recommendedBy', 'id');
    }

    public static function data($id)
    {
        $exemption   = Exemption::find($id);
        if ($exemption) {
            $program     = Programs::data($exemption->programID);
            $courses     = ExemptionCourse::where('exemptionID', $exemption->id)->get();
            $attachments = ExemptionAttachment::where('userID', $exemption->userID)->get();

            $atts = [];
            if ($attachments) {
                foreach ($attachments as $key) {
                    $atts[] = ExemptionAttachment::data($key->id);
                }
            }

            if ($courses) {
                $courseCount = $courses->count();
            } else {
                $courseCount = 0;
            }

            foreach ($courses as $course) {
                $exemtionCourses[] = Courses::data($course->courseID);
            }
            $processedByNames   = '';
            $dateProcessed      = '';
            $recommendedByNames = '';
            $dateRecommended    = '';

            if ($exemption->processedByDetails) {
                $processedByNames = $exemption->processedByDetails->first_name . ' ' . $exemption->processedByDetails->middle_name . ' ' . $exemption->processedByDetails->last_name;
                $dateProcessed    = $exemption->dateProcessed;
            }


            if ($exemption->recommendedByDetails) {
                $recommendedByNames = $exemption->recommendedByDetails->first_name . ' ' . $exemption->recommendedByDetails->middle_name . ' ' . $exemption->recommendedByDetails->last_name;
                $dateRecommended    = $exemption->recommendationDate;
            }


            $status = '';
            $recommendationStatus = '';
            switch ($exemption->status) {
                case '0':
                    $status = 'Pending Approval';
                    break;
                case '1':
                    $status = 'Approved';
                    break;
                case '-1':
                    $status = 'Declined';
                    break;
            }
            switch ($exemption->recommendationStatus) {
                case '0':
                    $recommendationStatus = 'Pending Review';
                    break;
                case '1':
                    $recommendationStatus = 'Approved';
                    break;
                case '-1':
                    $recommendationStatus = 'Declined';
                    break;
            }

            $user = User::find($exemption->userID);
            return [
                'key'                   => $exemption->id,
                'id'                    => $exemption->id,
                'userID'                => $user->id,
                'studentID'             => $user->student_id,
                'userNames'             => $user->first_name . ' ' . $user->last_name,
                'program'               => $program,
                'userProgramID'         => $program['id'],
                'programName'           => $program['fullname'],
                'processedBy'           => $processedByNames,
                'dateProcessed'         => $dateProcessed,
                'exemptionCourses'      => $exemtionCourses,
                'status'                => $status,
                'recommendationValue'   => $exemption->recommendationStatus,
                'approvalValue'         => $exemption->status,
                'recommendationStatus'  => $recommendationStatus,
                'recommendedByNames'    => $recommendedByNames,
                'dateRecommended'       => $dateRecommended,
                'recommendationNote'    => $exemption->note,
                'date'                  => $exemption->created_at->toFormattedDateString(),
                'courseCount'           => $courseCount,
                'user'                  => $user,
                'attachments'           => $atts,
                'exemptionTill'         => AcademicsExemption::exemptionLevel($exemption->id),
            ];
        } else {
            return [];
        }
    }

    public static function updateGradeBook($enrollmentID)
    {

        $enrollment     = Enrollment::find($enrollmentID);
        $classAssesment = ClassAssessment::where('classID', $enrollment->classID)->get()->first();

        if (empty($classAssesment)) {
            ClassAssessment::create([
                'assesmentID'   => AssessmentTypes::get()->first()->id,
                'classID'       => $enrollment->classID,
                'total'         => 100,
                'key'           => AssessmentTypes::get()->first()->id . '-' . $enrollment->classID,
            ]);
            $classAssesment = ClassAssessment::get()->last();
        }
        GradeBook::create([
            'userID'            => $enrollment->userID,
            'classAssessmentID' => $classAssesment->id,
            'grade'             => '-1',
        ]);
    }

    public static function jobCardData()
    {

        $requests               = Exemption::orderBy('created_at', 'desc')->get();
        $pendingApplications    = [];
        $declinedApplications   = [];
        $approvedApplications   = [];

        foreach ($requests as $request) {
            if ($request->status == 0 ) {
                $pendingApplications[] = Exemption::data($request->id);
            }
            if ($request->status == -1) {
                $declinedApplications[] = Exemption::data($request->id);
            }
            if ($request->status == 1) {
                $approvedApplications[] = Exemption::data($request->id);
            }
        }

        return [
            'pendingApplications'       => $pendingApplications,
            'pendingApplicationsCount'  => count($pendingApplications),
            'declinedApplications'       => $declinedApplications,
            'declinedApplicationsCount'  => count($declinedApplications),
            'approvedApplications'       => $approvedApplications,
            'approvedApplicationsCount'  => count($approvedApplications),
        ];
    }
}
