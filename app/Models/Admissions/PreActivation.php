<?php

namespace App\Models\Admissions;

use App\Models\Academics\Classes;
use App\Models\Accounting\Invoice;
use App\Models\Accounting\ProfomaInvoice;
use App\Models\Applications\StudentAdmissionRequest;
use App\Models\User;
use App\Traits\Finance\Accounting\Invoicing;
use Illuminate\Database\Eloquent\Model;

class PreActivation extends Model
{

    use Invoicing;
    protected $table = "ac_pre_activations";
    protected $guarded = ['id'];



    public function classes()
    {
        return $this->hasMany(PreActivationCourses::class, 'activationID', 'id');
    }


    public static function post($data)
    {

        $userID             = $data['userID'];
        $activatedByID      = $data['activatedByID'];
        $courses            = $data['courses'];
        $programID          = $data['programID'];
        $paymentPlanID      = $data['paymentPlanID'];
        $academicPeriodID   = $data['academicPeriodID'];
        $study_mode         = $data['study_mode'];
        $courses            = $data['courses'];

        $preActivation = PreActivation::where('userID', $userID)->get()->last();

        if ($preActivation) {
            $preActivation->delete();
        }

        PreActivation::create([
            'userID'            => $userID,
            'activatedBy'       => $activatedByID,
            'programID'         => $programID,
            'paymentPlanID'     => $paymentPlanID,
            'academicPeriodID'  => $academicPeriodID,
            'studyModeID'       => $study_mode['id'],
            'key'               => $userID . '-' . $academicPeriodID,
        ]);

        $preActivation = PreActivation::where('userID', $userID)->get()->last();

        foreach ($courses as $course) {

            $class = Classes::where('courseID', $course['id'])->where('academicPeriodID', $academicPeriodID)->first();

            // Check if course has been posted
            $preActivationCourse = PreActivationCourses::where('activationID', $preActivation->id)->where('classID', $class->id)->get()->first();
            if (empty($preActivationCourse) && empty($preActivationCourse->classID)) {
                PreActivationCourses::create([
                    'activationID' => $preActivation->id,
                    'classID'      => $class->id,
                    'key'          => $class->id . '-' . $preActivation->id,
                ]);
            }
        }


        $user = User::find($userID);
        $user->preActivated = 1;
        $user->save();

        return $user;
    }



    public static function invoiceUser($userID)
    {

        $user = User::find($userID);
        # Check if invoiced
        $invoice = Invoice::where('user_id', $user->id)->get()->last();
        if (empty($invoice) && empty($invoice->id) || !empty($invoice) && $invoice->details->sum('ammount') < 151.00) {
            if ($user->student_id < 1) {
                # Invoice the user from the information stored in pre activations
                $lastRow        = PreActivation::where('userID', $user->id)->get()->last();
                $profomaInvoice = ProfomaInvoice::where('userID', $user->id)->get()->last();
                $details        = $profomaInvoice->details;
                self::store($user->id, $details, $lastRow->academicPeriodID);
            }
        }

        // Check if student has applied for student readmission in new program
        $reAdmissionRequest = StudentAdmissionRequest::where('userID', $userID)->get()->last();
        if ($reAdmissionRequest && $reAdmissionRequest->enrolled == 0) {
            # Invoice the user from the information stored in pre activations
            $lastRow        = PreActivation::where('userID', $user->id)->get()->last();
            $profomaInvoice = ProfomaInvoice::where('userID', $user->id)->get()->last();
            if ($profomaInvoice) {
                $details        = $profomaInvoice->details;
                self::store($user->id, $details, $lastRow->academicPeriodID);
            }
        }
    }
}
