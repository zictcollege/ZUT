<?php

namespace App\Models\Applications;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class WithDrawDeferment extends Model
{
    protected $table = "ac_withdraw_deferement";
    protected $guarded = ['id'];

    public function recommender()
    {
        return $this->belongsTo(User::class, 'recommenderID', 'id');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approverID', 'id');
    }

    public static function submissions($userID)
    {

        $withdrawDeferments = WithDrawDeferment::where('userID', $userID)->get();
        $submissions = [];
        foreach ($withdrawDeferments as $withdrawDeferment) {
            $submissions[] = WithDrawDeferment::data($withdrawDeferment->id);
        }
        return $submissions;
    }

    public static function data($id)
    {

        $withdrawDeferment = WithDrawDeferment::find($id);

        $studyMode          = UserMode::where('userID', $withdrawDeferment->userID)->get()->first();

        $applicationStatus = '';
        $recommendationStatusValue = '';
        switch ($withdrawDeferment->status) {
            case '0':
                $applicationStatus = 'Pending Approval';
                break;
            case '1':
                $applicationStatus = 'Approved';
                break;
            case '-1':
                $applicationStatus = 'Declined';
                break;
        }

        switch ($withdrawDeferment->recommendationStatus) {
            case '0':
                $recommendationStatusValue = 'Pending Recommendation';
                break;
            case '1':
                $recommendationStatusValue = 'Recommended';
                break;
            case '-1':
                $recommendationStatusValue = 'Declined';
                break;
        }

        $recommenderNames = '';
        $dateRecommended  = '';
        $approverNames    = '';
        $dateApproved     = '';

        if ($withdrawDeferment->recommender) {
            $recommenderNames = $withdrawDeferment->recommender->first_name . ' ' . $withdrawDeferment->recommender->middle_name . ' ' . $withdrawDeferment->recommender->last_name;
            $dateRecommended    = $withdrawDeferment->dateProcessed;
        }

        if ($withdrawDeferment->approver) {
            $approverNames = $withdrawDeferment->approver->first_name . ' ' . $withdrawDeferment->approver->middle_name . ' ' . $withdrawDeferment->approver->last_name;
            $dateApproved    = $withdrawDeferment->approvalDate;
        }

        $ap = AcademicPeriod::dataMini($withdrawDeferment->academicPeriodID);

        $runningClasses = AcClass::where('academicPeriodID', $withdrawDeferment->academicPeriodID)->get();
        $runningClassIDs = [];
        foreach ($runningClasses as $runningClass) {
            $runningClassIDs[] = $runningClass->id;
        }


        $enrollments = Enrollment::where('userID', $withdrawDeferment->userID)->whereIn('classID', $runningClassIDs)->get();

        $classes = [];
        foreach ($enrollments as $enrollment) {
            $classes[] = AcClass::dataMini($enrollment->classID);
        }

        return [
            'id'                        => $withdrawDeferment->id,
            'key'                       => $withdrawDeferment->id,
            'academicPeriodName'        => $ap['name'],
            'academicPeriodCode'        => $ap['code'],
            'classes'                   => $classes,
            'userID'                    => $withdrawDeferment->userID,
            'programName'               => Program::dataMini($withdrawDeferment->programID)['fullname'],
            'studyMode'                 => $studyMode->name,
            'reason'                    => $withdrawDeferment->reasonforApplication,
            'type'                      => $withdrawDeferment->type,
            'studentNote'               => $withdrawDeferment->studentNote,
            'applicationStatus'         => $applicationStatus,
            'date'                      => $withdrawDeferment->created_at->toFormattedDateString(),
            'dateRecommended'           => $dateRecommended,
            'recommenderNames'          => $recommenderNames,
            'recommendationDate'        => $withdrawDeferment->recommendationDate,
            'recommendationStatus'      => $recommendationStatusValue,
            'recommendationStatusKey'   => $withdrawDeferment->recommendationStatus,
            'approverNames'             => $approverNames,
            'dateApproved'              => $dateApproved,
            'approvalStatus'            => $withdrawDeferment->status,
            'notifiedRecommender'       => $withdrawDeferment->notifiedRecommender,
            'notifiedApprover'       => $withdrawDeferment->notifiedApprover,
            'notifiedFinance'           => $withdrawDeferment->notifiedFinance,
        ];
    }
}
