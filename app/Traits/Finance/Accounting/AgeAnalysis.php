<?php

namespace App\Traits\Finance\Accounting;

use App\CreditNote;
use App\Models\Accounting\Invoice;
use App\Models\Accounting\Statement;
use App\Receipt;
use App\Traits\Finance\Accounting\PaymentPlan as AppPaymentPlan;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Finance\Accounting\PaymentPlanTrait;
use App\Traits\User\General;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait AgeAnalysis
{

    public function generateAgeAnalysis(Request $request)
    {


        $users = User::where('student_id', '!=', 'NULL')->get();

        $startDate = request('dates')[0];
        $endDate   = request('dates')[1];
        $today     = Carbon::now();

        /*$statement = Statement::selectRaw('SUM(debit) - SUM(credit) as balance')->whereBetween('created_at', [$startDate, $endDate])->where('user_id', 3118)->groupBy('user_id')->get();
        dd($statement[0]->balance);*/


        foreach ($users as $user) {

            /* $statement = Statement::selectRaw('SUM(debit) - SUM(credit) as balance')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('user_id', $user->id)->groupBy('user_id')->get();*/

            $balance = DB::select('SELECT (sum(credit)-sum(debit)) as balance FROM statements WHERE user_id = ? AND created_at > ? AND created_at < ? GROUP BY user_id', [$user->id, $startDate, $endDate]);

            if ($balance) {
                foreach ($balance as $key) {
                    $balance = $key->balance;
                }
                # if a student is owing. the balance is in negative. which is not the case. we need to have the balance in a positive value.
                if (empty($balance)) {
                    $balance = 0;
                }

                if ($balance < 0) {
                    $balance = abs($balance);
                } else {
                    $balance = -1 * abs($balance);
                }

                if (empty($balance)) {
                    $balance = 0.00;
                }

                // check for the last invoice in this period | Question.. what if there is no invoice in this period?

                $invoice = Invoice::whereBetween('created_at', [$startDate, $endDate])->where('user_id', $user->id)->get()->last();

                if (!$invoice) {
                    $invoice = Invoice::where('user_id', $user->id)->get()->last();
                }

                if ($invoice && $balance) {

                    $daysAging = Invoice::aging($invoice, $balance, $endDate);



                    if ($daysAging > 1 && $daysAging < 31) {
                        // enddate - daysAging 
                        $reportingEndDate = Carbon::parse($endDate)->subDays($daysAging);

                        // get the balance that was available at this period
                        $newBalance = DB::select('SELECT (sum(credit)-sum(debit)) as balance FROM statements WHERE user_id = ?  AND created_at < ? GROUP BY user_id', [$user->id, $reportingEndDate]);

                        foreach ($newBalance as $key) {
                            $newBalance = $key->balance;
                        }
                        # if a student is owing. the balance is in negative. which is not the case. we need to have the balance in a positive value.
                        if ($newBalance < 0) {
                            $newBalance = abs($newBalance);
                        } else {
                            $newBalance = -1 * abs($newBalance);
                        }

                        if (empty($newBalance)) {
                            $newBalance = 0.00;
                        }

                        $newInv = Invoice::where('user_id', $user->id)->where('created_at', '<=', $reportingEndDate)->get()->last();
                        if ($newInv) {
                            $newBalance = $newInv->details->sum('ammount');
                        }


                        $one     = $newBalance;
                        $two     = '';
                        $three   = '';
                        $four    = '';
                        $diff    = '';
                    }
                    if ($daysAging > 30 && $daysAging < 60) {

                        $reportingEndDate = Carbon::parse($endDate)->subDays($daysAging);

                        // get the balance that was available at this period
                        $newBalance = DB::select('SELECT (sum(credit)-sum(debit)) as balance FROM statements WHERE user_id = ?  AND created_at < ? GROUP BY user_id', [$user->id, $reportingEndDate]);

                        foreach ($newBalance as $key) {
                            $newBalance = $key->balance;
                        }
                        # if a student is owing. the balance is in negative. which is not the case. we need to have the balance in a positive value.
                        if ($newBalance < 0) {
                            $newBalance = abs($newBalance);
                        } else {
                            $newBalance = -1 * abs($newBalance);
                        }

                        if (empty($newBalance)) {
                            $newBalance = 0.00;
                        }

                        $newInv = Invoice::where('user_id', $user->id)->where('created_at', '<=', $reportingEndDate)->get()->last();
                        if ($newInv) {
                            $newBalance = $newInv->details->sum('ammount');
                        }

                        //$one     = '';
                        if (empty($one)) {
                            $one = '';
                        }
                        $two     = $newBalance;
                        $three   = '';
                        $four    = '';
                        $diff    = '';

                        // if two is not empty perform another calculation if an invoice exits 


                    }
                    if ($daysAging >= 60 && $daysAging <= 90) {

                        $reportingEndDate = Carbon::parse($endDate)->subDays($daysAging);

                        // get the balance that was available at this period
                        $newBalance = DB::select('SELECT (sum(credit)-sum(debit)) as balance FROM statements WHERE user_id = ?  AND created_at < ? GROUP BY user_id', [$user->id, $reportingEndDate]);

                        foreach ($newBalance as $key) {
                            $newBalance = $key->balance;
                        }
                        # if a student is owing. the balance is in negative. which is not the case. we need to have the balance in a positive value.
                        if ($newBalance < 0) {
                            $newBalance = abs($newBalance);
                        } else {
                            $newBalance = -1 * abs($newBalance);
                        }

                        if (empty($newBalance)) {
                            $newBalance = 0.00;
                        }

                        $newInv = Invoice::where('user_id', $user->id)->where('created_at', '<=', $reportingEndDate)->get()->last();
                        if ($newInv) {

                            $newInv = Invoice::where('user_id', $user->id)->where('created_at', '<=', $reportingEndDate)->where('id', '!=', $newInv->id)->get()->last();
                            if ($newInv) {
                                $newBalance = $newInv->details->sum('ammount');
                            } else {
                                $newBalance = $newBalance;
                            }
                        }

                        if (empty($one)) {
                            $one = '';
                        }
                        if (empty($two)) {
                            $two = '';
                        }
                        //$one     = '';
                        //$two     = '';
                        $three   = $newBalance;
                        $four    = '';
                        $diff    = '';
                    }
                    if ($daysAging > 90) {

                        $reportingEndDate = Carbon::parse($endDate)->subDays($daysAging);

                        // get the balance that was available at this period
                        $newBalance = DB::select('SELECT (sum(credit)-sum(debit)) as balance FROM statements WHERE user_id = ?  AND created_at < ? GROUP BY user_id', [$user->id, $reportingEndDate]);

                        foreach ($newBalance as $key) {
                            $newBalance = $key->balance;
                        }
                        # if a student is owing. the balance is in negative. which is not the case. we need to have the balance in a positive value.
                        if ($newBalance < 0) {
                            $newBalance = abs($newBalance);
                        } else {
                            $newBalance = -1 * abs($newBalance);
                        }

                        if (empty($newBalance)) {
                            $newBalance = 0.00;
                        }

                        if (empty($one)) {
                            $one = '';
                        }
                        if (empty($two)) {
                            $two = '';
                        }
                        if (empty($three)) {
                            $three = '';
                        }
                        //$one     = '';
                        //$two     = '';
                        //$three   = '';
                        $four    = $newBalance;
                        $diff    = '';
                    } elseif ($daysAging == 0) {
                        $one     = '';
                        $two     = '';
                        $three   = '';
                        $four    = '';
                        $diff    = '';
                    }
                } else {

                    $one     = '';
                    $two     = '';
                    $three   = '';
                    $four    = '';
                    $diff    = '';
                }






                $program = General::currentProgramData($user->id);

                $_users[] = [
                    'name'          => $user->first_name . ' ' . $user->middle_name,
                    'surname'       => $user->last_name,
                    'gender'        => $user->gender,
                    'student_id'    => $user->student_id,
                    'program'       => $program['currentProgramName'],
                    'mode'          => $program['currentModeName'],
                    'qualification' => $program['qualification'],
                    'one'           => $one,
                    'two'           => $two,
                    'three'         => $three,
                    'four'          => $four,
                    'current'       => $balance,
                    'dayDiff'       => $diff,
                    'daysAging'     => $daysAging,

                ];

                $one     = '';
                $two     = '';
                $three   = '';
                $four    = '';
                $diff    = '';
            }
        }

        if (empty($_users)) {
            $_users = [];
        }

        return $_users;
    }


    public static function balances($id)
    {

        $balance = DB::select('SELECT (sum(credit)-sum(debit)) as balance
                  FROM statements
                  WHERE
                  user_id = ?
                  GROUP BY user_id', [$id]);

        foreach ($balance as $key) {
            $balance = $key->balance;
        }
        # if a student is owing. the balance is in negative. which is not the case. we need to have the balance in a positive value.
        if (empty($balance)) {
            $balance = 0;
        }

        if ($balance < 0) {
            $balance = abs($balance);
        } else {
            $balance = -1 * abs($balance);
        }

        if (empty($balance)) {
            return "0.00";
        }
        return $balance;
    }
}
