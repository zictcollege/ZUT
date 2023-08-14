<?php

namespace App\Models\Accounting;

use App\Http\Requests\StudyMode\StudyMode;
use App\Models\Academic\AcademicPeriod;
use App\Models\Academics\AcademicPeriods;
use App\Models\User;
use App\Traits\Finance\Accounting\PaymentPlan as AppPaymentPlan;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Finance\Accounting\PaymentPlanTrait;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentPlan extends Model
{
    use PaymentPlanTrait;
    use \App\Traits\User\Accounting;
    protected $table = "fn_payment_plans";
    protected $guarded = ['id'];

    public static function data($id)
    {

        $payPlan = PaymentPlan::find($id);

        if ($payPlan->active == 1) {
            $status = 'Active';
        } else {
            $status = 'Inactive';
        }

        $rawInstallments = PaymentPlanDetail::where('paymentPlanID', $payPlan->id)->orderBy('dateDue', 'ASC')->get();

        if ($rawInstallments) {
            foreach ($rawInstallments as $installment) {
                $installments[] = PaymentPlanDetail::data($installment->id);
            }
            if (!empty($installments)) {
                $installments;
            }
        }

        if (empty($installments)) {
            $installments = [];
            $installmentCount = 0;
        }

        if ($payPlan->createdByID) {
            $user  = User::find($payPlan->createdByID);
            if ($user) {
                $names = $user->first_name . ' ' . $user->last_name;
            }
        } else {
            $names = '';
        }

        $studyMode     = [];
        $studyModeID   = '';
        $studyModeName = '';
        $academicPeriodID = '';
        $code= '';

        if ($payPlan->studyModeID != NULL) {
            $studyMode      = StudyMode::find($payPlan->studyModeID);
            $studyModeID    = $payPlan->studyModeID;
            $studyModeName  = $studyMode['name'];
        }
        if ($payPlan->academicPeriodId != NULL) {
            $academicPeriodID    = $payPlan->academicPeriodId;
            $code = DB::table('ac_academicPeriods')->select('code')->where('id','=',$academicPeriodID)->value('code');
        }


        $payment_plan = [
            'key'                       => $payPlan->id,
            'id'                       => $payPlan->id,
            'name'                      => $payPlan->name,
            'description'               => $payPlan->description,
            'status'                    => $status,
            'studyModeID'               => $studyModeID,
            'studyModeName'             => $studyModeName,
            'studyMode'                 => $studyMode,
            'date'                      => $payPlan->created_at->toFormattedDateString('D/M/Y'),
            'installments'              => $installments,
            'createdBy'                 => $names,
            'academicPeriodId'          => $academicPeriodID,
            'code'                      => $code,
        ];

        return $payment_plan;
    }

    public function details()
    {
        return $this->belongsTo(PaymentPlanDetail::class, 'paymentPlanID', 'id');
    }


    public static function invoicePaymentPlanDetails($userID, $academicPeriodID, $paymentPlanID)
    {
        $installments   = [];
        $user           = User::find($userID);
        $balance        = (new PaymentPlan)->balance($user->id);
        $invoices       = Invoice::where('user_id', $userID)->where('academicPeriodID', $academicPeriodID)->get();
        $total          = 0.00;
        $todayTimeStamp = Carbon::parse(Carbon::now())->timestamp;

        foreach ($invoices as $invoice) {
            $total = $total + $invoice->details->sum('ammount');
        }

        $whatsPaid   = $total - $balance;
        $paymentPlan = PaymentPlan::find($paymentPlanID);
        $details     = PaymentPlanDetail::where('paymentPlanID', $paymentPlan->id)->orderBy('dateDue', 'ASC')->get();

        foreach ($details as $detail) {
            $amountDue      = $detail->percentage / 100 * $total;
            $hitDetails     = PaymentPlanDetail::where('paymentPlanID', $paymentPlan->id)->where('hitNumber', '<=', $detail->hitNumber)->get();

            $hitCurrentAmountDue = 0;
            foreach ($hitDetails as $hitDetail) {
                $hitTotal = $hitDetail->percentage / 100 * $total;
                $hitCurrentAmountDue = $hitCurrentAmountDue + $hitTotal;
            }

            if ($whatsPaid >= $hitCurrentAmountDue) {
                $status = 'Paid';
                $expectedToPay = '';
                $installmentDueDateTimeStamp = $detail;
            } else {
                $expectedToPay = $hitCurrentAmountDue - $whatsPaid;
                $status = 'Not Paid, pay ' . number_format($expectedToPay);
                $installmentDueDateTimeStamp = '';
                $canAttendClass = 0;
            }

            $date = strtotime($detail->dateDue, $baseTimestamp = null);

            $data = [
                'id'                    => $detail->id,
                'paymentPlanID'         => $detail->paymentPlanID,
                'installment'           => $detail->hitNumber,
                'dueDate'               => Carbon::createFromTimestamp($date)->toFormattedDateString(),
                'amountDue'             => number_format(round($amountDue, 2)),
                'percentage'            => $detail->percentage,
                'status'                => $status,
                'expectedToPay'         => $expectedToPay,
                'todayTimeStamp'        => $todayTimeStamp,
                'installmentTimeStamp'  => strtotime($detail->dateDue, $baseTimestamp = null),
                'installmentDueDateTimeStamp' => $detail->dateDue,
                'hitCurrentAmountDue'   => $hitCurrentAmountDue,
            ];
            $installments[] = $data;
            unset($amountDue, $canAttendClass, $status, $installmentDueDateTimeStamp);
        }


        if (!empty($installments)) {

            foreach ($installments as $installment) {

                if ($installment['status'] == 'Paid') {
                    $lastpaidInstallment = $installment;

                    if ($todayTimeStamp <= $installment['installmentTimeStamp']) {
                        $cat = 1;
                    } else {
                        $cat = 0;
                    }
                } else {
                    // Unpaid installments
                    $cat = 0;
                }
            }
        } else {
            $lastpaidInstallment = [];
            $cat = 0;
        }

        if (empty($lastpaidInstallment)) {
            $lastpaidInstallment = [];
            //$cat = 0;
        }

        if ($lastpaidInstallment) {
            $paymentPlanDetailNext = PaymentPlanDetail::where('hitNumber', '>', $lastpaidInstallment['installment'])->where('paymentPlanID', $lastpaidInstallment['paymentPlanID'])->get()->first();

            if (!empty($paymentPlanDetailNext)) {
                $ppTimestamp = strtotime($paymentPlanDetailNext->dateDue, $baseTimestamp = null);
                if ($todayTimeStamp <= $ppTimestamp) {
                    $cat = 1;
                }
            }
        }

        // Check if academic period is active.
        $academicPeriod = AcademicPeriods::find($academicPeriodID);
        $academicPeriodStatus = 'Closed';
        $academicPeriodStatusValue = 0;

        if ($academicPeriod) {
            if (strtotime($academicPeriod->acEndDate) > strtotime(date("Y-m-d"))) {
                $academicPeriodStatus = 'Open';
                $academicPeriodStatusValue = 1;
            } else {
                $academicPeriodStatus = 'Closed';
                $academicPeriodStatusValue = 0;
            }
        }



        return [
            'paymentPlanName'               => $paymentPlan->name,
            'total'                         => number_format($total),
            'installments'                  => $installments,
            'lastpaidInstallment'           => $lastpaidInstallment,
            'canAttendClass'                => $cat,
            'academicPeriodStatusValue'    => $academicPeriodStatusValue,
            'academicPeriodStatus'          => $academicPeriodStatus,
            'whatsPaid'                     => number_format($whatsPaid),
        ];
    }
}
