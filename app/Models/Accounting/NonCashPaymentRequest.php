<?php

namespace App\Models\Accounting;

use App\User;
use Illuminate\Database\Eloquent\Model;

class NonCashPaymentRequest extends Model
{
    protected $table = "fn_non_cash_payment_requests";
    protected $guarded = ['id'];

    public static function data($id)
    {

        $nonCashPayment = NonCashPaymentRequest::find($id);
        $approvedBy = '';
        $raisedBy   = '';

        if ($nonCashPayment->raisedByUser) {
            $raisedBy = $nonCashPayment->raisedByUser->first_name . ' ' . $nonCashPayment->raisedByUser->last_name;
        }

        if ($nonCashPayment->approvedByUser) {
            $approvedBy = $nonCashPayment->approvedByUser->first_name . ' ' . $nonCashPayment->approvedByUser->last_name;
        }

        if ($nonCashPayment->user) {

            $studentNames = $nonCashPayment->user->first_name . ' ' . $nonCashPayment->user->last_name;
            if ($nonCashPayment->user->student_id) {
                $studentID    = $nonCashPayment->user->student_id;
            }else {
                $studentID    = $nonCashPayment->user->guest_id;
            }
            
        }

        if ($nonCashPayment->status == 0) {
            $status = 'Pending';
        }


        if ($nonCashPayment->status == -1) {
            $status = 'Declined';
        }


        if ($nonCashPayment->status == 1) {
            $status = 'Approved';
        }

        return [
            'id'                    => $nonCashPayment->id,
            'key'                   => $nonCashPayment->id,
            'studentID'             => $studentID,
            'studentNames'          => $studentNames,
            'amount'                => $nonCashPayment->amount,
            'raisedBy'              => $raisedBy,
            'approvedBy'            => $approvedBy,
            'invoiceID'             => $nonCashPayment->invoiceID,
            'discountPercentage'    => $nonCashPayment->discountPercentage,
            'comment'               => $nonCashPayment->comment,
            'status'                => $status,
            'posted'                => $nonCashPayment->posted,
            'notifiedApprover'      => $nonCashPayment->notifiedApprover,
            'notifiedRequester'     => $nonCashPayment->notifiedRequester,
            'date'                  => date('d-M-Y', strtotime($nonCashPayment->created_at)),
        ];
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'userID');
    }
    public function raisedByUser()
    {
        return $this->hasOne(User::class, 'id', 'raisedBy');
    }

    public function approvedByUser()
    {
        return $this->hasOne(User::class, 'id', 'approvedBy');
    }


    public static function usersNoncashPayments($userID)
    {
    
        $nonCashPayments = NonCashPaymentRequest::where('userID',$userID)->get();

        foreach ($nonCashPayments as $ncp) {
            $nonCashPaymentsData[] = NonCashPaymentRequest::data($ncp->id);
        }


        if(empty($nonCashPaymentsData)) {
            $nonCashPaymentsData = [];
        }

        return $nonCashPaymentsData;

    }
}
