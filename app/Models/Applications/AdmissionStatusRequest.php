<?php

namespace App\Models\Applications;

use App\Models\Academics\Programs;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;


class AdmissionStatusRequest extends Model
{

    protected $table = "ac_admission_status_requests";
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }
    public function requester()
    {
        return $this->belongsTo(User::class, 'requestedBy', 'id');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approvedBy', 'id');
    }
    public static function data($id)
    {

        $admissionStatusChangeRequest = AdmissionStatusRequest::find($id);
        $program = Programs::dataMini($admissionStatusChangeRequest->programID);
        $requesterNames = '';
        $dateRequested  = '';
        $approverNames  = '';
        $dateApproved   = '';
        if ($admissionStatusChangeRequest->requester) {
            $requesterNames = $admissionStatusChangeRequest->requester->first_name . ' ' . $admissionStatusChangeRequest->requester->middle_name . ' ' . $admissionStatusChangeRequest->requester->last_name;
            $dateRequested    = $admissionStatusChangeRequest->dateProcessed;
        }

        if ($admissionStatusChangeRequest->approver) {
            $approverNames = $admissionStatusChangeRequest->approver->first_name . ' ' . $admissionStatusChangeRequest->approver->middle_name . ' ' . $admissionStatusChangeRequest->approver->last_name;
            $dateApproved    = $admissionStatusChangeRequest->dateApproved;
        }


        $applicationStatus = '';

        switch ($admissionStatusChangeRequest->status) {
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

        return [
            'id'                    => $admissionStatusChangeRequest->id,
            'key'                   => $admissionStatusChangeRequest->id,
            'userID'                => $admissionStatusChangeRequest->userID,
            'names'                 => $admissionStatusChangeRequest->user->first_name . ' ' . $admissionStatusChangeRequest->user->middle_name . ' ' . $admissionStatusChangeRequest->user->last_name,
            'programName'           => $program['fullname'],
            'type'                  => $admissionStatusChangeRequest->type,
            'requesterNames'        => $requesterNames,
            'applicationStatus'     => $applicationStatus,
            'approverNames'         => $approverNames,
            'dateApproved'          => $dateApproved,
            'recommendationNote'    => $admissionStatusChangeRequest->requesterNote,
            'dateRequested'         => $dateRequested,
            'created_at'            => $admissionStatusChangeRequest->created_at->toFormattedDateString(),
        ];
    }

    public static function requests($userID)
    {
        $admissionRequests = AdmissionStatusRequest::where('userID', $userID)->get();

        $admissionsRequestsData = [];
        foreach ($admissionRequests as $admissionRequest) {
            $admissionsRequestsData[] = AdmissionStatusRequest::data($admissionRequest->id);
        }
        return $admissionsRequestsData;
    }
}
