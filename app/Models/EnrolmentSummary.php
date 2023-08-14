<?php

namespace App\Models;


use App\Models\Academics\AcademicPeriods;
use App\Models\Academics\Classes;
use App\Models\Academics\Programs;
use App\Models\Accounting\Invoice;
use App\Models\Admissions\UserProgram;
use App\Models\Admissions\UserStudyModes;
use Illuminate\Database\Eloquent\Model;



class EnrolmentSummary extends Model
{
    protected $table = "ac_enrolment_summaries";
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'userID');
    }

    public function program()
    {
        return $this->belongsTo(Programs::class, 'id', 'programID');
    }

    public function studyMode()
    {
        return $this->belongsTo(UserStudyModes::class, 'id', 'studyModeID');
    }

    public static function data($id) // UserProgramID
    {
        $program            = [];
        $programFullName    = '';
        $classes            = [];
        $studyModeName      = '';
        $academicPeriodName = '';
        $academicPeriod     = [];
        $enrolmentSummary   = EnrolmentSummary::find($id);

        if ($enrolmentSummary->programID) {
            $program            = Programs::dataMini($enrolmentSummary->programID);
            $programFullName    = $program['fullname'];
        }
        if ($enrolmentSummary->academicPeriodID) {
            $academicPeriod     = AcademicPeriods::dataMini($enrolmentSummary->academicPeriodID);
            $academicPeriodName = $academicPeriod['name'];
        }

        if ($enrolmentSummary->moodleSynced == 1) {
            $moodleSync = 'Synchronized';
        } else {
            $moodleSync = 'Not Synchronized';
        }


        $studyMode = UserStudyModes::find($enrolmentSummary->studyModeID);
        if ($studyMode) {
            $studyModeName = $studyMode->name;
        }

        if ($enrolmentSummary->mailedCopyToStudent == 1) {
            $mailedCopyToStudent = 'Emailed';
        } else {
            $mailedCopyToStudent = 'Pending';
        }

        if ($enrolmentSummary->moodleSyncedClasses) {

            $classIDsArray = explode(":", $enrolmentSummary->moodleSyncedClasses);
            foreach ($classIDsArray as $classID) {
                $classes[]  = Classes::dataMini($classID);
            }
        }


        if ($enrolmentSummary) {
            return [
                'id'                        => $enrolmentSummary->id,
                'key'                       => $enrolmentSummary->id,
                'userID'                    => $enrolmentSummary->userID,
                'moodleSync'                => $moodleSync,
                'studyModeID'               => $enrolmentSummary->studyModeID,
                'studyModeName'             => $studyModeName,
                'program'                   => $program,
                'programFullName'           => $programFullName,
                'moodleSyncedClassesKey'    => $enrolmentSummary->moodleSyncedClasses,
                'academicPeriodID'          => $enrolmentSummary->academicPeriodID,
                'academicPeriodName'        => $academicPeriodName,
                'mailedCopyToStudent'       => $mailedCopyToStudent,
                'classes'                   => $classes,
                'date'                      => $enrolmentSummary->created_at->toDateTimeString(),
            ];
        } else {
            return  [];
        }
    }

    public static function academicPeriod($academicPeriodID, $userID)
    {
        $enrolmentSummary = EnrolmentSummary::where('userID', $userID)->where('academicPeriodID', $academicPeriodID)->get()->first();
        if ($enrolmentSummary) {
            return EnrolmentSummary::data($enrolmentSummary->id);
        } else {
            return [];
        }
    }

    public static function post($data)
    {
        $enrolmentSummary                       = EnrolmentSummary::where('key', $data['userID'] . '-' . $data['academicPeriodID'])->get()->first();

        $invoice = Invoice::where('user_id', $data['userID'])->where('academicPeriodID', $data['academicPeriodID'])->get()->first();

        if (empty($enrolmentSummary)) {
            $enrolmentSummary                       = new EnrolmentSummary();
            $enrolmentSummary->userID               = $data['userID'];
            $enrolmentSummary->academicPeriodID     = $data['academicPeriodID'];
            $enrolmentSummary->studyModeID          = $data['studyModeID'];
            $enrolmentSummary->key                  = $data['userID'] . '-' . $data['academicPeriodID'];
            if ($invoice) {
                $enrolmentSummary->created_at = $invoice->created_at;
            }
            $enrolmentSummary->save();
        } else {

            $userProgram = UserProgram::where('userID', $data['userID'])->get()->last();
            if ($userProgram) {
                $enrolmentSummary->programID          = $userProgram->programID;
                $enrolmentSummary->save();
            }
        }
    }
}
