<?php

namespace App\Models\Applications;



use App\Models\Academics\AcademicPeriods;
use App\Models\Academics\Classes;
use App\Models\Academics\Programs;
use App\Models\Academics\StudyModes;
use App\Models\Accounting\PaymentPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StudentAdmissionRequest extends Model
{

    protected $table = "ac_student_admisson_requests";
    protected $guarded = ['id'];

    public function approver()
    {
        return $this->belongsTo(User::class, 'approverID', 'id');
    }

    public static function data($id)
    {
        $approver       = [];
        $dateProccessed = '';
        $approverNames  = '';
        $request        = StudentAdmissionRequest::find($id);
        $program        = Programs::dataMini($request->programID);
        $studyMode      = StudyModes::find($request->studyModeID);

        if ($request->approver) {
            $approverNames      = $request->approver->first_name . ' ' . $request->approver->middle_name . ' ' . $request->approver->last_name;
            $approver           = $request->approver;
            $dateProccessed     = $request->dateProccessed;
        }

        switch ($request->status) {
            case '0':
                $status = "Pending";
                break;
            case '1':
                $status = "Approved";
                break;
            case '-1':
                $status = "Declined";
                break;
        }

        // Find the possible upcoming academic period 
        $possibleAcademicPeriod     = [];
        $possibleAcademicPeriodID   = '';
        $paymentPlanID              = '';

        $programCourses     = ProgramCourse::where('programID', $request->programID)->get();
        if ($programCourses) {
            foreach ($programCourses as $programCourse) {
                $pCourseIDs[] =  $programCourse->courseID;
            }
        }
        $academicPeriods = AcademicPeriods::where('studyModeIDAllowed', $request->studyModeID)->get();
        if ($academicPeriods) {
            foreach ($academicPeriods as $ap) {
                $aps[] = AcademicPeriods::dataMini($ap->id);
            }
            if (!empty($aps)) {
                foreach ($aps as $apJson) {

                    if ($apJson && $apJson['id'] && !empty($pCourseIDs)) {
                        $class = Classes::whereIn('courseID', $pCourseIDs)->where('academicPeriodID', $apJson['id'])->get()->first();

                        if (!empty($class->id) && $apJson['status'] = 'Open') {
                            $possibleAcademicPeriod   = $apJson;
                            $possibleAcademicPeriodID = $apJson['id'];
                            if ($studyMode) {
                                $paymentPlan   = PaymentPlan::where('studyModeID', $studyMode->id)->get()->first();
                                if (!empty($paymentPlan)) {
                                    $paymentPlanID = $paymentPlan->id;
                                }
                            }
                            
                        }
                    }
                }
            }
        }

        return [
            'id'                        => $request->id,
            'key'                       => $request->key,
            'userID'                    => $request->userID,
            'programName'               => $program['fullname'],
            'program'                   => $program,
            'programID'                 => $program['id'],
            'studyModeName'             => $studyMode->name,
            'studyMode'                 => $studyMode,
            'studyModeID'               => $studyMode->id,
            'approver'                  => $approver,
            'status'                    => $status,
            'paymentPlanID'             => $paymentPlanID,
            'possibleAcademicPeriod'    => $possibleAcademicPeriod,
            'academicPeriodID'          => $possibleAcademicPeriodID,
            'date'                      => $request->created_at->toFormattedDateString(),
            'approverNames'             => $approverNames,
            'dateProccessed'            => $dateProccessed,
        ];
    }
    public static function requests($userID)
    {
        $data       = [];
        $requests   = StudentAdmissionRequest::where('userID', $userID)->get();

        if ($requests) {
            foreach ($requests as $request) {
                $data[] = StudentAdmissionRequest::data($request->id);
            }
        }
        
        return $data;
    }
}
