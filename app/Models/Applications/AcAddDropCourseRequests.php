<?php

namespace App\Models\Applications;



use App\Models\Academics\AcademicPeriods;
use App\Models\Academics\Classes;
use App\Models\Academics\Programs;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AcAddDropCourseRequests extends Model
{
    public $table       = "ac_add_drop_course_requests";
    protected $guarded  = ['id'];

    public function approver()
    {
        return $this->belongsTo(User::class, 'approverID', 'id');
    }

    public function raiser()
    {
        return $this->belongsTo(User::class, 'raisedBy', 'id');
    }

    public static function data($id)
    {
        $addClassRequests       = [];
        $dropClassRequests      = [];
        $approver               = [];
        $raiserNames            = '';
        $raiser                 = [];
        $dateRaised             = [];
        $dateApproved           = '';
        $approverNames          = '';
        $request                = AcAddDropCourseRequests::find($id);
        $program                = Programs::dataMini($request->programID);
        $academicPeriod         = AcademicPeriods::dataMini($request->academicPeriodID);
        $user                   = User::find($request->userID);

        if ($request->approver) {
            $approverNames      = $request->approver->first_name . ' ' . $request->approver->middle_name . ' ' . $request->approver->last_name;
            $approver           = $request->approver;
            $dateApproved       = $request->dateApproved;
        }

        if ($request->raisedBy) {
            $raiserNames      = $request->raiser->first_name . ' ' . $request->raiser->middle_name . ' ' . $request->raiser->last_name;
            $raiser           = $request->approver;
        } elseif (!empty($request->user)) {
            $raiserNames      = $request->user->first_name . ' ' . $request->user->middle_name . ' ' . $request->user->last_name;
        }

        switch ($request->status) {
            case '0':
                $status = 'Pending Review';
                break;
            case '1':
                $status = 'Approved';
                break;
            case '-1':
                $status = 'Declined';
                break;
        }

        if (!empty($request->addClassIDs)) {
            $addClassIDs  = explode(':', $request->addClassIDs);
            foreach ($addClassIDs as $addClassID) {
                $addClassRequests[] = Classes::dataMini($addClassID);
            }
        }
        if (!empty($request->dropClassIDs)) {
            $dropClassIDs = explode(':', $request->dropClassIDs);
            foreach ($dropClassIDs as $dropClassID) {
                $dropClassRequests[] = Classes::dataMini($dropClassID);
            }
        }

        return [
            'id'                    => $request->id,
            'key'                   => $request->id,
            'programName'           => $program['fullname'],
            'academcPeriodName'     => $academicPeriod['name'],
            'academicPeriodID'      => $request->academicPeriodID,
            'addClassRequests'      => $addClassRequests,
            'studentID'             => $request->student_id,
            'userNames'             => $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name,
            'dropClassRequests'     => $dropClassRequests,
            'userID'                => $request->userID,
            'status'                => $status,
            'statusValue'           => $request->status,
            'approverNames'         => $approverNames,
            'approver'              => $approver,
            'dateApproved'          => $dateApproved,
            'raiserNames'           => $raiserNames,
            'raiser'                => $raiser,
            'date'                  => $request->created_at->toFormattedDateString(),
            'dateUpdated'           => $request->updated_at->toFormattedDateString(),
        ];
    }

    public static function jobCardData()
    {

        $requests               = AcAddDropCourseRequests::orderBy('created_at', 'desc')->get();
        $pendingApplications    = [];
        $declinedApplications   = [];
        $approvedApplications   = [];

        foreach ($requests as $request) {
            if ($request->status == 0) {
                $pendingApplications[] = AcAddDropCourseRequests::data($request->id);
            }
            if ($request->status == -1) {
                $declinedApplications[] = AcAddDropCourseRequests::data($request->id);
            }
            if ($request->status == 1) {
                $approvedApplications[] = AcAddDropCourseRequests::data($request->id);
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

    public static function userRequests($userID)
    {
        $studentsRequests   = [];
        $requests           = AcAddDropCourseRequests::where('userID', $userID)->get();
        foreach ($requests as $request) {
            $studentsRequests[] = AcAddDropCourseRequests::data($request->id);
        }
        return $studentsRequests;
    }
}
