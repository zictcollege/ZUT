<?php

namespace App\Models\Applications;

use App\Models\Academics\AcademicPeriods;
use App\Models\Academics\Classes;
use App\Models\Academics\Programs;
use App\Models\Academics\StudyModes;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ChangeStudyModeRequest extends Model
{

    public $table      = "ac_change_study_mode_requests";
    protected $guarded = ['id'];

    public function firstReviewer()
    {
        return $this->belongsTo(User::class, 'firstApproverID', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }
    public function secondReviewer()
    {
        return $this->belongsTo(User::class, 'secondApproverID', 'id');
    }

    public function thirdReviewer()
    {
        return $this->belongsTo(User::class, 'thirdApproverID', 'id');
    }

    public function fourthReviewer()
    {
        return $this->belongsTo(User::class, 'fourthApproverID', 'id');
    }

    public static function data($id)
    {

        $request            = ChangeStudyModeRequest::find($id);
        $program            = Programs::dataMini($request->programID);
        $currentStudyMode   = StudyModes::find($request->currentStudyModeID);
        $proposedStudyMode  = StudyModes::find($request->proposedStudyModeID);
        $academicPeriod     = AcademicPeriods::dataMini($request->academicPeriodID);

        $status                         = '';
        $firstReviewerNames             = '';
        $firstReviewer                  = '';
        $secondReviewerNames            = '';
        $thirdReviewerNames             = '';
        $dateFirstReviewerProcessed     = '';
        $dateSecondReviewerProcessed    = '';
        $processedByFirstApprover       = 0;
        $processedBySecondApprover      = 0;
        $processedByThirdApprover       = 0;
        $dateThirdReviewerProcessed     = '';
        $dateFourthReviewerProcessed    = '';
        $processedByFourthApprover      = '';
        $fourthReviewerNames            = '';
        $firstreviewerNote              = '';
        $secondreviewerNote             = '';
        $thirdreviewerNote              = '';

        if ($request->status == 0) {
            $status = 'Submitted';
        }
        if ($request->status == 1) {
            $status = 'Approved by First Reviewer, Awaiting second reviewer';
        }
        if ($request->status == 2) {
            $status = 'Approved by Second Reviewer, Awaiting Final Reviewer';
        }
        if ($request->status == 3) {
            $status = 'Approved by Final Reviewer, awaiting final processing';
        }
        if ($request->status == 4) {
            $status = 'Processed';
        }
        if ($request->status == -2) {
            $status = 'Canceled By Student';
        }
        if ($request->status == -3) {
            $status = 'Declined by First reviewer';
        }
        if ($request->status == -4) {
            $status = 'Declined by Second reviewer';
        }
        if ($request->status == -5) {
            $status = 'Declined by Final reviewer';
        }

        if ($request->firstApproverID) {
            $firstReviewerNames             = $request->firstReviewer->first_name . ' ' . $request->firstReviewer->middle_name . ' ' . $request->firstReviewer->last_name;
            $dateFirstReviewerProcessed     = $request->dateFirstReviewerProcessed;
            $processedByFirstApprover       = 1;
            $firstreviewerNote              = $request->firstApproverNote;
        }

        if ($request->secondApproverID) {
            $secondReviewerNames            = $request->secondReviewer->first_name . ' ' . $request->secondReviewer->middle_name . ' ' . $request->secondReviewer->last_name;
            $dateSecondReviewerProcessed    = $request->dateFirstReviewerProcessed;
            $processedBySecondApprover      = 1;
            $secondreviewerNote             = $request->secondreviewerNote;
        }

        if ($request->thirdApproverID) {
            $thirdReviewerNames            = $request->thirdReviewer->first_name . ' ' . $request->thirdReviewer->middle_name . ' ' . $request->thirdReviewer->last_name;
            $dateThirdReviewerProcessed    = $request->dateThirdReviewerProcessed;
            $processedByThirdApprover      = 1;
            $thirdreviewerNote             = $request->thirdreviewerNote;
        }

        if ($request->fourthApproverID) {
            $fourthReviewerNames            = $request->fourthReviewer->first_name . ' ' . $request->fourthReviewer->middle_name . ' ' . $request->fourthReviewer->last_name;
            $dateFourthReviewerProcessed    = $request->dateFourthReviewerProcessed;
            $processedByFourthApprover      = 1;
        }

        if ($request->userID) {
            $userNames   = $request->user->first_name . ' ' . $request->user->middle_name . ' ' . $request->user->last_name;
            $studentID   = $request->user->student_id;
        }


        $selectedClasses            = [];
        $proposedAcademicPeriod     = [];
        $proposedAcademicPeriodName = [];
        if (!empty($request->selectedClassIDs)) {
            $selectedClassIDs = explode(',', $request->selectedClassIDs);
            foreach ($selectedClassIDs as $classID) {
                $selectedClasses[] = Classes::dataMini($classID);
            }

            $proposedAcademicPeriod     = AcademicPeriods::dataMini($request->proposedAcademicPeriodID);
            $proposedAcademicPeriodName = $proposedAcademicPeriod['name'];
        }

        return [
            'id'                          => $request->id,
            'key'                         => $request->id,
            'userID'                      => $request->userID,
            'studentID'                   => $studentID,
            'userNames'                   => $userNames,
            'program'                     => $program,
            'programName'                 => $program['fullname'],
            'currentStudyModeName'        => $currentStudyMode->name,
            'currentStudyMode'            => $currentStudyMode,
            'proposedStudyModeName'       => $proposedStudyMode->name,
            'proposedStudyMode'           => $proposedStudyMode,
            'academcPeriodName'           => $academicPeriod['name'],
            'reasonForApplication'        => $request->reasonForApplication,
            'status'                      => $status,
            'selectedClasses'             => $selectedClasses,
            'proposedAcademicPeriod'      => $proposedAcademicPeriod,
            'proposedAcademicPeriodID'    => $request->proposedAcademicPeriodID,
            'proposedAcademicPeriodName'  => $proposedAcademicPeriodName,
            
            'firstReviewer'               => $firstReviewer,
            'firstReviewerNames'          => $firstReviewerNames,
            'firstApproverStatus'         => $request->firstApproverStatus,
            'firstreviewerNote'            => $firstreviewerNote,
            'processedByFirstApprover'    => $processedByFirstApprover,

            'secondReviewerNames'         => $secondReviewerNames,
            'processedBySecondApprover'   => $processedBySecondApprover,
            'dateSecondReviewerProcessed' => $dateSecondReviewerProcessed,
            'secondreviewerNote'           => $secondreviewerNote,
            'secondApproverStatus'         => $request->secondApproverStatus,

            'thirdReviewerNames'          => $thirdReviewerNames,
            'processedByThirdApprover'    => $processedByThirdApprover,
            'dateThirdReviewerProcessed'  => $dateThirdReviewerProcessed,
            'thirdreviewerNote'            => $thirdreviewerNote,

            'fourthReviewerNames'         => $fourthReviewerNames,
            'dateFourthReviewerProcessed' => $dateFourthReviewerProcessed,
            'processedByFourthApprover'   => $processedByFourthApprover,

            'date'                        => $request->created_at->toDateTimeString(),
            'dateFirstReviewerProcessed'  => $dateFirstReviewerProcessed,
        ];
    }

    public static function requests($userID)
    {
        $userRequests = [];
        $requests = ChangeStudyModeRequest::where('userID', $userID)->get();
        foreach ($requests as $request) {
            $userRequests[] = ChangeStudyModeRequest::data($request->id);
        }
        return $userRequests;
    }

    public static function jobCardData()
    {

        $requests               = ChangeStudyModeRequest::orderBy('created_at', 'desc')->get();
        $pendingApplications    = [];
        $declinedApplications   = [];
        $approvedApplications   = [];

        foreach ($requests as $request) {
            if ($request->status == 1 || $request->status == 2 || $request->status == 3) {
                $pendingApplications[] = ChangeStudyModeRequest::data($request->id);
            }
            if ($request->status == -2 || $request->status == -3 || $request->status == -4 || $request->status == -5) {
                $declinedApplications[] = ChangeStudyModeRequest::data($request->id);
            }
            if ($request->status == 4) {
                $approvedApplications[] = ChangeStudyModeRequest::data($request->id);
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
