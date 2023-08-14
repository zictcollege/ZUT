<?php

namespace App\Models\Accounting;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PaymentPlanDetail extends Model
{
    protected $table = "fn_payment_plan_details";
    protected $guarded = ['id'];

    public function paymentPlan() {
        return $this->belongsTo(PaymentPlan::class,'id','paymentPlanID');
    }

    public static function data($id) {

        $installment = PaymentPlanDetail::find($id);

        $date = strtotime($installment->dateDue, $baseTimestamp = null);
        return [
            'id'        => $installment->id,
            'key'       => $installment->id,
            'dueDate'   => Carbon::createFromTimestamp($date)->toFormattedDateString(),
            'name'      => $installment->hitNumber,
            'percentage'=> $installment->percentage,
            'created_at'=> $installment->created_at,
        ];

    }

}
