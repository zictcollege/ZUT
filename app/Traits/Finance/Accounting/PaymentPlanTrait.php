<?php

namespace App\Traits\Finance\Accounting;

use App\Console\Commands\Admissions\PaymentPlanInitiator;
use App\Models\Accounting\PaymentPlan;
use App\Models\Accounting\PaymentPlanDetail;
use App\Models\Accounting\ProfomaInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;


trait PaymentPlanTrait
{
    public function plans()
    {
        $plans = PaymentPlan::all();

        foreach ($plans as $plan) {
            $_plan    = PaymentPlan::data($plan->id);
            $_plans[] = $_plan;
        }

        if (empty($_plans)) {
            $_plans = [];
        }
        return $_plans;
    }
    public function store(Request $request)
    {

        $this->Validate($request, array(
            'name'                  => 'required|unique:fn_payment_plans',
            'studyModeID'           => 'required',
            'academicPeriodId'      => 'required'
        ));

        try {
            DB::beginTransaction();

            $paymentPlan                         = new PaymentPlan();
            $paymentPlan->name                   = request('name');
            $paymentPlan->description            = request('description');
            $paymentPlan->createdByID            = request('authUser');
            $paymentPlan->studyModeID            = request('studyModeID');
            $paymentPlan->academicPeriodId       = request('academicPeriodId');
            $paymentPlan->save();

            DB::commit();

            $plans = PaymentPlan::all();

            foreach ($plans as $plan) {
                $_plan    = PaymentPlan::data($plan->id);
                $_plans[] = $_plan;
            }

            if (empty($_plans)) {
                $_plans = [];
            }
            return $_plans;
        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    public function updatePaymentPlan(Request $request)
    {

        $this->Validate($request, array(
            'name'                  => 'required',
            'studyModeID'           => 'required',
            'academicPeriodId'      => 'required',
        ));

        try {
            DB::beginTransaction();

            $paymentPlan                         = PaymentPlan::find(request('id'));
            $paymentPlan->name                   = request('name');
            $paymentPlan->description            = request('description');
            $paymentPlan->studyModeID            = request('studyModeID');
            $paymentPlan->academicPeriodId       = request('academicPeriodId');

            $paymentPlan->save();

            DB::commit();

            return PaymentPlan::data(request('id'));

        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    public function storeInstallment(Request $request)
    {

        $dueDate = Carbon::parse(request('dueDate'));

        $this->Validate($request, array(
            'installmentNumber'    => 'required|integer',
            'dueDate'              => 'required',
            'percentage'           => 'required:integer',
        ));

        try {
            DB::beginTransaction();


            $paymentPlan                = PaymentPlan::find(request('paymentPlanID'));

            $installment                = new PaymentPlanDetail();
            $installment->paymentPlanID = $paymentPlan->id;
            $installment->hitNumber     = request('installmentNumber');
            $installment->dateDue       = $dueDate;
            $installment->percentage    = request('percentage');
            $installment->save();

            DB::commit();
            return PaymentPlan::data($paymentPlan->id);

        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    public function updateInstallment(Request $request)
    {
        $date = Carbon::parse(request('dueDate'))->timestamp;
        $dueDate = Carbon::createFromTimestamp($date);

        //$dueDate = Carbon::parse(request('dueDate'));

        $this->Validate($request, array(
            'name'    => 'required|integer',
            'dueDate'              => 'required',
            'percentage'           => 'required:integer',
        ));

        try {
            DB::beginTransaction();

            $installment                = PaymentPlanDetail::find(request('id'));
            $installment->hitNumber     = request('name');
            $installment->dateDue       = $dueDate;
            $installment->percentage    = request('percentage');
            $installment->save();

            DB::commit();
            return PaymentPlan::data($installment->paymentPlanID);

        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    public function deleteInstallment(Request $request) {

        $installment = PaymentPlanDetail::find(request('id'));
        $paymentPlanID = $installment->paymentPlanID;
        $installment->delete();
        return PaymentPlan::data($paymentPlanID);

    }




    public function updates(Request $request)
    {


        try {
            DB::beginTransaction();

            $paymentPlan                         = PaymentPlan::find(request('key'));
            $paymentPlan->name                   = request('name');
            $paymentPlan->description            = request('description');

            $paymentPlan->save();

            DB::commit();

            $plans = PaymentPlan::all();

            foreach ($plans as $plan) {
                $_plan    = PaymentPlan::data($plan->id);
                $_plans[] = $_plan;
            }

            if (empty($_plans)) {
                $_plans = [];
            }
            return $_plans;
        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    public function activate(Request $request)
    {

        $paymentPlan                         = PaymentPlan::find(request('key'));
        $paymentPlan->active = 1;
        $paymentPlan->save();

        DB::commit();

        $plans = PaymentPlan::all();

        foreach ($plans as $plan) {
            $_plan    = PaymentPlan::data($plan->id);
            $_plans[] = $_plan;
        }

        if (empty($_plans)) {
            $_plans = [];
        }
        return $_plans;
    }

    public static function breakDown($id)
    {

        $paymentPlan = PaymentPlan::find(2);
        $breakDown = explode(',', $paymentPlan->percentageBreakdown);
        return $breakDown;
    }

    public static function calculateFirstSettlementDays($invoiceID, $paymentPlanID)
    {

        $pInvoice     = ProfomaInvoice::find($invoiceID);
        $paymentPlan  = PaymentPlan::find($paymentPlanID);
        $date         = Carbon::parse($pInvoice->created_at)->addDays(7);
        $date         = date("M d ,Y", strtotime($date));
        return $date;
    }
}
