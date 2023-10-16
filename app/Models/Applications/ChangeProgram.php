<?php

namespace App\Models\Applications;

use App\Http\Requests\StudyMode\StudyMode;
use App\Models\Academics\Programs;
use App\Models\Academics\StudyModes;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ChangeProgram extends Model
{
    protected $table = "ac_change_program_requests";
    protected $guarded = ['id'];

    public function users()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }
    public function firstReviewer()
    {
        return $this->belongsTo(User::class, 'firstReviewerID', 'id');
    }
    public function secondReviewer()
    {
        return $this->belongsTo(User::class, 'secondReviewerID', 'id');
    }
    public function financeUser()
    {
        return $this->belongsTo(User::class, 'finanaceUserID', 'id');
    }

    public static function data($id)
    {

        $changeProgramRequest   = ChangeProgram::find($id);
        $newProgramData         = Programs::dataMini($changeProgramRequest->programID);
        $newStudyMode           = StudyModes::find($changeProgramRequest->studyModeID);
        $student                = User::find($changeProgramRequest->userID);
        
        $previousProgramData    = Programs::dataMini($changeProgramRequest->previousProgramID);
        $previousStudyMode      = StudyModes::find($changeProgramRequest->previousStudyModeID);

        $status                         = '';
        $firstReviewerNames             = '';
        $firstReviewer                  = '';
        $secondReviewerNames            = '';
        $dateSecondReviewerProcessed    = '';
        $processedByFirstApprover       = 0;
        $processedBySecondApprover      = 0;
        $firstReviewerNote              = '';
        $secondreviewerNote              = '';
        $dateFirstReviewerProcessed     = '';
        $firstReviewerStatus            = 'Pending Review';


        
        if ($status == -1) {
            $status = 'Declined By Reviewer';
        }
        
 

        if ($changeProgramRequest->status == 0) {
            $status = 'Pending Review';
        }
        if ($changeProgramRequest->status == 1) {
            $status = 'Reviewed Awaiting Approval';
        }
        if ($changeProgramRequest->status == 2) {
            $status = 'Approved';
        }


        // First reviewer Status 
        if ($changeProgramRequest->firstReviewerStatus == 1) {
            $firstReviewerStatus = 'Recommended';
        }
        
        if ($changeProgramRequest->firstReviewerStatus == -1) {
            $firstReviewerStatus = 'Not Recommended';
        }
        
        if ($changeProgramRequest->firstReviewerID) {
            $firstReviewerNames = $changeProgramRequest->firstReviewer->first_name . ' ' . $changeProgramRequest->firstReviewer->middle_name . ' ' . $changeProgramRequest->firstReviewer->last_name;
            $dateFirstReviewerProcessed    = $changeProgramRequest->dateFirstReviewed;
            $processedByFirstApprover      = 1;
            $firstReviewerNote              = $changeProgramRequest->firstReviewerNote;
        }

        if ($changeProgramRequest->secondReviewerID) {
            $secondReviewerNames = $changeProgramRequest->secondReviewer->first_name . ' ' . $changeProgramRequest->secondReviewer->middle_name . ' ' . $changeProgramRequest->secondReviewer->last_name;
            $dateSecondReviewerProcessed    = $changeProgramRequest->dateSecondReviewed;
            $processedBySecondApprover      = 1;
            $secondreviewerNote              = $changeProgramRequest->secondreviewerNote;
        }

        return [
            'id'                            => $changeProgramRequest->id,
            'key'                           => $changeProgramRequest->id,
            'studentID'                     => $student->student_id,
            'newProgramName'                => $newProgramData['fullname'],
            'userNames'                     => $student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name,
            'newProgramID'                  => $newProgramData['id'],
            'userID'                        => $student->id,
            'newProgramData'                => $newProgramData,
            'newProgramQualification'       => $newProgramData['qualification'],
            'previousProgramData'           => $previousProgramData,
            'previousProgramName'           => $previousProgramData['fullname'],
            'previousProgramQualification'  => $previousProgramData['qualification'],
            'newStudyModeID'                => $newStudyMode->id,
            'newStudyModeName'              => $newStudyMode->name,
            'previousStudyID'               => $previousStudyMode->id,
            'previousStudyName'             => $previousStudyMode->name,
            'status'                        => $status,
            'reason'                        => $changeProgramRequest->reason,
            'reasonExplained'               => $changeProgramRequest->reasonExplained,

            'firstReviewer'                 => $firstReviewer,
            'firstReviewerNames'            => $firstReviewerNames,
            'firstApproverStatus'           => $changeProgramRequest->firstApproverStatus,
            'firstReviewerNote'             => $firstReviewerNote,
            'firstReviewerStatus'           => $firstReviewerStatus,
            'firstReviewerStatusInt'        => $changeProgramRequest->firstReviewerStatus,
            'processedByFirstApprover'      => $processedByFirstApprover,
            'dateFirstReviewerProcessed'    => $dateFirstReviewerProcessed,

            'secondReviewerNames'           => $secondReviewerNames,
            'processedBySecondApprover'     => $processedBySecondApprover,
            'dateSecondReviewerProcessed'   => $dateSecondReviewerProcessed,
            'secondreviewerNote'             => $secondreviewerNote,
            'secondApproverStatusInt'       => $changeProgramRequest->secondApproverStatus,

            'date'                          => $changeProgramRequest->created_at->toFormattedDateString(),
            'createdAt'                     => $changeProgramRequest->created_at->toFormattedDateString(),
            'createdAt'                     => $changeProgramRequest->updated_at->toFormattedDateString(),
        ];
    }

    public static function usersChangeProgramRequests($userId)
    {

        $changeProgramRequests = ChangeProgram::where('userID', $userId)->get();
        $data = [];
        foreach ($changeProgramRequests as $changeProgramRequest) {
            $data[] = ChangeProgram::data($changeProgramRequest->id);
        }
        return $data;
    }

    public static function jobCardData()
    {

        $requests               = ChangeProgram::orderBy('created_at', 'desc')->get();
        $pendingApplications    = [];
        $declinedApplications   = [];
        $approvedApplications   = [];

        foreach ($requests as $request) {
            if ($request->status == 0 || $request->status == 1) {
                $pendingApplications[] = ChangeProgram::data($request->id);
            }
            if ($request->status == -1) {
                $declinedApplications[] = ChangeProgram::data($request->id);
            }
            if ($request->status == 2) {
                $approvedApplications[] = ChangeProgram::data($request->id);
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
