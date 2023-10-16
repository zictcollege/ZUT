<?php

namespace App\Traits\User;


use App\Models\Academics\AcademicPeriods;
use App\Models\Accounting\CreditNote;
use App\Models\Accounting\FailedTransaction;
use App\Models\Accounting\Invoice;
use App\Models\Accounting\Fee;
use App\Models\Accounting\NonCashPaymentRequest;
use App\Models\Accounting\PaymentPlan;
use App\Models\Accounting\PaymentPlanDetail;
use App\Models\Accounting\ProfomaInvoice;
use App\Models\Accounting\Quotation;
use App\Models\Accounting\Receipt;
use App\Models\Accounting\Statement;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;


trait Accounting
{

  public function useraccounting($id)
  {
    $accounting = self::accounting($id);
    return $accounting;
  }
  public static function accounting($id)
  {
    $user  = User::find($id);
    if ($user->student_id) {
      $student_id = $user->student_id;
    } else {
      $student_id = $user->guest_id;
    }


    $quotations         = Quotation::where('user_id', $id)->get();
    $invoices           = Invoice::where('user_id', $id)->get();
    $proformaInvoices   = ProfomaInvoice::where('userID', $id)->get();
    $proformaInvoice    = ProfomaInvoice::where('userID', $id)->get()->last();
    $receipts           = Receipt::where('user_id', $id)->get();
    $creditNotes        = CreditNote::where('user_id', $id)->get();
    $statements         = self::user_statement($id);
    $failedTransactions = FailedTransaction::where('userID', $id)->get();

    foreach ($failedTransactions as $failedTransaction) {
      $ft = [
        'id'             => $failedTransaction->id,
        'results'        => $failedTransaction->results,
        'transaction_id' => $failedTransaction->transactionID,
        'response_codes' => $failedTransaction->responsecodes,
        'currency'       => $failedTransaction->currency,
        'ammounts'       => env('BILLING_CURRENCY') . ' ' . $failedTransaction->amounts,
        'email'          => $failedTransaction->email,
        'date'           => date('d-M-Y', strtotime($failedTransaction->created_at)),
      ];

      $FailedTransactions[] = $ft;
    }

    if (empty($FailedTransactions)) {
      $FailedTransactions = [];
    }
    $totalPaid = 0;
    foreach ($receipts as $receipt) {

      if (!empty($receipt->collectedBy->first_name)) {
        $collectedBy = $receipt->collected_by->first_name . ' ' . $receipt->collected_by->middle_name . ' ' . $receipt->collected_by->last_name;
      } else {
        $collectedBy = '';
      }

      if ($receipt->dateDeposited) {
        $dd = date('d-M-Y', strtotime($receipt->dateDeposited));
      } else {
        $dd = '';
      }

      $rcpt = [
        'id'             => $receipt->id,
        'invoice_id'     => $receipt->invoice_id,
        'payment_method' => $receipt->payment_method,
        'ammount_paid'   => env('BILLING_CURRENCY') . ' ' . $receipt->ammount_paid,
        'date'           => date('d-M-Y', strtotime($receipt->created_at)),
        'date_deposited' => $dd,
        'collectedBy'    => $collectedBy,
      ];

      $Receipts[] = $rcpt;
      $totalPaid  = $totalPaid + $receipt->ammount_paid;
    }

    if (empty($Receipts)) {
      $Receipts = [];
    }
    $CreditNotes = [];
    foreach ($creditNotes as $creditNote) {

      if ($creditNote) {
        $cn = CreditNote::data($creditNote->id);
        $CreditNotes[] = $cn;
      }
    }
    foreach ($quotations as $quote) {

      $Quote = [
        'id'          => $quote->id,
        'names'       => $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name,
        'student_id'  => $student_id,
        'date'        => $quote->created_at->toFormattedDateString(),
        'total'       => env('BILLING_CURRENCY') . ' ' . number_format($quote->details->sum('ammount')),
      ];
      $Quotations[] = $Quote;
    }

    if (empty($Quotations)) {
      $Quotations = [];
    }

    foreach ($invoices as $inv) {
      $in_ap      = Invoice::where('id', $inv->id)->value('academicPeriodID');
      $id         = Invoice::where('id', $inv->id)->value('raisedBy');
      $user_fname = User::where('id', $id)->value('first_name');
      $user_lname = User::where('id', $id)->value('last_name');
      $full_name  = $user_fname . ' ' . $user_lname;

      $academicPeriod  = AcademicPeriods::where('id', $in_ap)->value('code');
      if (empty($academicPeriod)) {
        $academicPeriod = 'Unassigned';
      }

      $Inv = [
        'id'              => $inv->id,
        'names'           => $inv->user->first_name . ' ' . $inv->user->middle_name . ' ' . $inv->user->last_name,
        'student_id'      => $student_id,
        'academicPeriod'  => $academicPeriod,
        'raisedby'        => $full_name,
        'date'            => $inv->created_at->toFormattedDateString(),
        'total'           => env('BILLING_CURRENCY') . ' ' . number_format($inv->details->sum('ammount')),
        'totalClear'      => $inv->details->sum('ammount'),
      ];
      $Invoices[] = $Inv;
    }

    if (empty($Invoices)) {
      $Invoices = [];
    }

    foreach ($proformaInvoices as $pinv) {
      $Inv = ProfomaInvoice::data($pinv->id);
      $pInvoices[] = $Inv;
    }

    if (empty($pInvoices)) {
      $pInvoices = [];
    }

    if ($proformaInvoice) {
      $activation = PreActivation::where('userID', $user->id)->get()->last();
      $pInvoice = ProfomaInvoice::data($proformaInvoice->id);
      if ($activation) {
        $paymentPlan  = PaymentPlan::breakDown($activation->paymentPlanID);
        $installment = PaymentPlanDetail::where('paymentPlanID', $activation->paymentPlanID)->where('hitNumber', 1)->get()->first();
        $paymentData = [
          'firstPaymentPercentage' => $installment->percentage,
          'fistPaymentAmount'      => env('BILLING_CURRENCY') . ' ' . ($installment->percentage / 100) * $pInvoice['totalPlain'],
          'fistPaymentAmountInt'   => ($installment->percentage / 100) * $pInvoice['totalPlain'],
          'total'                  => env('BILLING_CURRENCY') . ' ' . $pInvoice['total'],
          'totalInt'               => $pInvoice['total'],
          'deadline'               => PaymentPlan::calculateFirstSettlementDays($pInvoice['id'], $activation->paymentPlanID),
        ];
      } else {
        $paymentData = [];
      }
    } else {
      $pInvoice = [];
    }

    if (empty($paymentData)) {
      $paymentData = [];
    }


    foreach ($statements as $statement) {

      if (!empty($statement->invoice_id)) {
        $reference = 'INV ' . $statement->invoice_id;
      }
      if ($statement->receipt_id > 0) {
        $reference = 'RCT ' . $statement->receipt_id;
      }
      if (!empty($statement->creditnote_id)) {
        $reference = 'CN ' . $statement->creditnote_id;
      }

      if (empty($reference)) {
        $reference = '';
      }

      if ($statement->credit == 0) {
        $credit = '';
      } else {
        $credit = $statement->credit;
      }

      $Statement = [
        'date'              => date('d-M-Y', strtotime($statement->created_at)),
        'reference'         => $reference,
        'description'       => $statement->description,
        'debit'             => $statement->debit,
        'credit'            => $credit,
        'balance'           => $statement->balance,
      ];

      $Statements[] = $Statement;
    }

    if (empty($Statements)) {
      $Statements = [];
    }

    // Non cash payments
    $nonCashPayments = NonCashPaymentRequest::usersNoncashPayments($user->id);

    $accounting = [
      'quotations'          => $Quotations,
      'nonCashPayments'     => $nonCashPayments,
      'invoices'            => $Invoices,
      'receipts'            => $Receipts,
      'profomaInvoices'     => $pInvoices,
      'lastProfomaInvoice'  => $pInvoice,
      'credit_notes'        => $CreditNotes,
      'statement'           => $Statements,
      'failed_transactions' => $FailedTransactions,
      'paymentData'         => $paymentData,
      'totalPaid'           => $totalPaid,
    ];

    return $accounting;
  }
  public function daysAging($userID)
  {

    /*$user = User::find($userID);

    # run first check
    $result = Invoice::aging($user->invoice, 1);
    if ($result > 0) {
      return $feedback = '30';
    }
    $result = Invoice::aging($user->invoice, 2);
    if ($result > 0) {
      return $feedback = '60';
    }
    $result = Invoice::aging($user->invoice, 3);
    if ($result > 0) {
      return $feedback = '90';
    }
    $result = Invoice::aging($user->invoice, 4);
    if ($result > 0) {
      return $feedback = '180';
    }*/

    return [];
  }

  public static function paymentPercentage($id)
  {

    $lastInvoice = Invoice::where('user_id', $id)->get()->last();

    $balance = User::mybalance($id);

    $paymentPercentage = $balance / $lastInvoice->payment * 100;

    $paymentPercentage = 100 - $paymentPercentage;
    $paymentPercentage = round($paymentPercentage);

    return $paymentPercentage;
  }

  public static function mybalance($id)
  {
    $statement  = Statement::where('user_id', $id)->get()->last();

    if (empty($statement)) {
      return 0;
    } else {
      return $balance = $statement->balance;
    }
  }
  public static function overdueBalance($id)
  {
    $statement  = Statement::where('user_id', $id)->get()->last();

    if (empty($statement)) {
      return 0;
    } else {
      return $balance = $statement->balance;
    }
  }
  public static function user_balance($id)
  {

    $balance = DB::select('SELECT (sum(credit)-sum(debit)) as balance
                  FROM statements
                  WHERE
                  user_id = ?
                  GROUP BY user_id', [$id]);

    foreach ($balance as $key) {
      $balance = $key->balance;
    }
    if (empty($balance)) {
      $balance = 0;
    }
    # if a student is owing. the balance is in negative. which is not the case. we need to have the balance in a positive value.
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
  public function balance($id)
  {

    $balance = DB::select('SELECT (sum(credit)-sum(debit)) as balance
                  FROM statements
                  WHERE
                  user_id = ?
                  GROUP BY user_id', [$id]);

    foreach ($balance as $key) {
      $balance = $key->balance;
    }
    if (empty($balance)) {
      $balance = 0;
    };        # if a student is owing. the balance is in negative. which is not the case. we need to have the balance in a positive value.
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
  public static function examslipBalance($id)
  {

    $balance = DB::select('SELECT (sum(credit)-sum(debit)) as balance
                  FROM statements
                  WHERE
                  user_id = ?
                  GROUP BY user_id', [$id]);

    foreach ($balance as $key) {
      $balance = $key->balance;
    }
    if (empty($balance)) {
      $balance = 0;
    }
    # if a student is owing. the balance is in negative. which is not the case. we need to have the balance in a positive value.
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
  public function minimum_payment($value)
  {
    $expected = 50 / 100 * $value;
    return $expected;
  }
  // check amount that has been paid and fail the process if the amount is less than 70 percent
  public static function checkPayment($amount, $payable)
  {

    // find the minimum amount expected to be paid
    $expected = 70 / 100 * $payable;
    // lets say expected is 4000 of 5000

    if ($amount < $expected) {
      // if the amount paid is less than the expected amount
      return 0;
    } else {
      return 1;
    }
  }
  public static function user_statement($id)
  {

    $transactions = DB::select('SELECT x.id
                           , x.created_at
                           , x.receipt_id
                           , x.invoice_id
                           , x.debit
                           , x.description
                           , x.credit
                           , SUM(y.bal) balance
                        FROM
                           (
                             SELECT *,debit-credit bal FROM statements where user_id = ?
                           ) x

                        JOIN
                           (
                             SELECT *,debit-credit bal FROM statements where user_id = ?
                           ) y
                          ON y.id <= x.id
                       GROUP
                          BY x.id
                       ORDER
                          BY x.created_at asc
                          ', [$id, $id]);
    return $transactions;
  }

  public function receipt($id)
  {
    return $receipt = Receipt::data($id);
  }
  public function quotation($id)
  {
    return Quotation::data($id);
  }
  public function invoice($id)
  {
    return Invoice::data($id);
  }


  public static function AcademicPaymentPercentage($userID, $AcademicPeriodID)
  {

    //$invoices = Invoice::where('user_id', $userID)->where('academicPeriodID', $AcademicPeriodID)->get();
      $invoices = Invoice::where('user_id', $userID)->get();
    $invoiceTotal = 0.00;

    if ($invoices) {

      foreach ($invoices as $invoice) {
        $invoiceTotal = $invoiceTotal + $invoice->details->sum('ammount');
      }

      # Get invoice total
      $paymentPercentage = 0;
      # Get user balance
      $balance = self::user_balance($userID);

      $payment = $invoiceTotal - $balance;
      if ($invoiceTotal < 1) {
        $paymentPercentage = 0;
      } else {
        $paymentPercentage = ($payment / $invoiceTotal) * 100;
      }
      return $paymentPercentage;
    } else {
      return 0;
    }
  }
}
