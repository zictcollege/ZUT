<?php

namespace App\Traits\Finance\Reports;

use App\FailedTransaction;
use App\Models\Accounting\Invoice;
use App\User;
use Illuminate\Http\Request;


trait Transactions
{

    public function failedTransactionsview() {
        return view('app.modules.accounting.reports.transactions.failedtransactions');
    }
    public function failedTranscations(Request $request) {

        $from = request('from');
        $to   = request('to');

        $failedTransactions = FailedTransaction::whereBetween('created_at', [$from, $to])->get();


        foreach ($failedTransactions as $transaction) {

            $user  = $transaction->user;
            if ($user->student_id) {
                $student_id = $user->student_id;
            } else {
                $student_id = $user->guest_id;
            }

            $ft = [
                'id'             => $transaction->id,
                'results'        => $transaction->results,
                'student_id'     => $student_id,
                'user'           => $transaction->user->first_name .' '. $transaction->user->middle_name .' ' . $transaction->user->last_name,
                'transaction_id' => $transaction->transactionID,
                'response_codes' => $transaction->responsecodes,
                'currency'       => $transaction->currency,
                'ammounts'       => number_format($transaction->amounts),
                'email'          => $transaction->email,
                'date'           => date('d-M-Y', strtotime( $transaction->created_at)),
            ];

            $FailedTransactions[] = $ft;

        }

        if (empty($FailedTransactions)) {
            $FailedTransactions = [];
        }

        return $FailedTransactions;

    }


}


