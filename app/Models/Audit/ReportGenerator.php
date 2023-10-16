<?php

namespace App\Models\Audit;

use App\Models\Academic\AcademicPeriod;
use App\Models\Academic\Program;
use App\Models\Academic\StudyMode;
use App\Report;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ReportGenerator extends Model
{

    public $table = "ad_reports_audit";
    protected $guarded = ['id'];


    public static function data($id)
    {

        $report            = ReportGenerator::find($id);
        $requestParameters = json_encode($report->requestParameters);
        $user              = User::find($report->userID); 
        
        
        switch ($report->emailed) {
            case '0':
                $status = 'Pending';
                break;
            case '1':
                $status = 'Sent';
                break;
            case '-1':
                $status = 'Failed';
                break;
            default:
                $status = 'Pending';
                break;
        }

        

        $requestParameters = json_decode($report->requestParameters);
        $program = Program::dataMini($requestParameters->programID);
        $academicPeriod = AcademicPeriod::dataMini($requestParameters->academicPeriodID);
        $studyMode      = StudyMode::find($requestParameters->studyModeID);
        return [
            'id'                => $report->id,
            'requestParameters' => $requestParameters,
            'programID'         => $requestParameters->programID,
            'programName'       => $program['fullname'],
            'academicPeriodName'=> $academicPeriod['name'],
            'acaedmicPeriodCode'=> $academicPeriod['code'],
            'studyModeName'     => $studyMode->name,
            'admissionStatus'   => $requestParameters->admissionStatus,
            'names'             => $user->first_name .' '. $user->middle_name. ' '. $user->last_name,
            'userID'            => $user->id,
            'ipAddress'         => $report->ipAddress,
            'userAgent'         => $report->userAgent,
            'module'            => $report->module,
            'subModule'         => $report->subModule,
            'description'       => $report->description,
            'emailed'           => $report->emailed,
            'emailedStatus'     => $status,
            'emailDate'         => $report->emailDate,
            'created_at'        => $report->created_at,
            'date'              => $report->created_at->toDateTimeString(),
            'key'               => $report->key,
        ];

    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public static function storeRequest($data)
    {

        // array to string 
        $data['requestParameters'] = json_encode($data['requestParameters']);

        ReportGenerator::create([
            'userID'            => $data['userID'],
            'ipAddress'         => $data['ipAddress'],
            'requestParameters' => $data['requestParameters'],
            'userAgent'         => $data['userAgent'],
            'module'            => $data['module'],
            'subModule'         => $data['subModule'],
            'description'       => $data['description'],
            'key'               => $data['userID'] .'-'.$data['ipAddress'].'-'. Carbon::now()
        ]);
    }
}
