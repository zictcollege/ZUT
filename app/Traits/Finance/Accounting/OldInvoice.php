<?php

namespace App\Traits\Finance\Accounting;

use App\Models\Academic\Program;
use App\Models\Accounting\Invoice;
use App\Models\Accounting\InvoiceDetail;
use App\Models\Accounting\Quotation;
use App\Models\Accounting\QuotationDetail;
use App\Models\Accounting\Statement;
use App\Models\Admissions\UserProgram;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;


trait OldInvoice
{
  //
  //protected $guarded = ['id'];



  public static function revenueDataPerInvoice($invoiceID)
  {
    $invoice = Invoice::find($invoiceID);

    if ($invoice->user->student_id) {
      $student_id = $invoice->user->student_id;
    } else {
      $student_id = $invoice->user->guest_id;
    }

    $userProgram = UserProgram::where('userID', $invoice->user->id)->get()->last();

    if ($userProgram && $userProgram->id) {

      $program     = Program::find($userProgram->programID);
      $programName = $program->code . ' - ' . $program->name;
    } else {
      $programName = " ";
    }

    $values = [
      'invoice_id' => $invoice->id,
      'names'      => $invoice->user->first_name . ' ' . $invoice->user->middle_name . ' ' . $invoice->user->last_name,
      'student_id' => $student_id,
      'program'    => $programName,
      'total'      => $invoice->details->sum('ammount'),
      'date'       => $invoice->created_at->toFormattedDateString(),
    ];

    $plucked = $invoice->details->pluck('ammount', 'chart_of_account_id');
    $results = $plucked->merge($values);
    $results->all();
    return $results;
  }



  public function receipts()
  {
    return $this->hasMany(Receipt::class);
  }

  public static function description($id)
  {
    $invoice = Invoice::find($id);

    $description = InvoiceDetail::where('invoice_id', $invoice->id)->get()->first();
    return $description->description;
  }

  public static function feeIdentifier($fee, $value)
  {

    switch ($fee->fee_identifier) {
      case '1':
        return $fee->ammount;
        if (!empty($fee->ammount)) {
          return $fee->ammount;
        } else {
          return " ";
        }

        break;

      case '1':
        return $fee->ammount;
        if (!empty($fee->ammount)) {
          return $fee->ammount;
        } else {
          return " ";
        }

        break;

      case '3':
        return $fee->ammount;
        if (!empty($fee->ammount)) {
          return $fee->ammount;
        } else {
          return " ";
        }

        break;

      default:
        // code...
        break;
    }
  }

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }

  public static function viewAccomodation()
  {

    $invoices = Invoice::where('created_at', '>', '2018-05-05')->get();

    return $invoices;
  }

  public static function users_invoices($id)
  {
    $invoices = Invoice::where('user_id', $id)->get();
    return $invoices;
  }


  public function invoice_total($id)
  {
    return $total = InvoiceDetail::where('invoice_id', $id)->sum('ammount');
  }
  /*

		These functions help with the generation of financial reports
		with all invoice activities

		prepared by @wmwewa


    */



  // show invoices genereted in a day

  public static function todaysInvoices()
  {
    // figure out what day it is
    $today    = Carbon::today()->toDateString();

    // find todays invoices
    $invoices = Invoice::where('created_at', '>=', $today)->get();

    //foreach invoice find the invoice details and totalout the ammount
    $ammount  = $invoices->sum('payment');
    return $ammount;
  }

  public static function dueDate($invoice)
  {

    # The invoice due date is calculated as 30 days from the day it was created.
    $created_at = $invoice->created_at;
    $dueDate    = $created_at->addMonths(1); # return is created at plus 30 days.

    return $dueDate;
  }

  public static function daysOD($dueDate)
  {

    # Days overdue is calculated by getting the number of days inbetween day and the day the invoice was due.
    $today  = Carbon::now();

    $daysOD = $dueDate->diffInDays($today);
    return $daysOD;
  }

  public static function amountOwing($user)
  {
    # Find the total amount of money that has been invoiced.

    $invoices = $user->invoices;
    foreach ($invoices as $inv) {
      $total[] = $inv->details->sum('ammount');
    }
    $total = array_sum($total);

    return $total;
  }

  public static function invoiceBalance($invoice)
  {

    $statement = Statement::where('invoice_id', $invoice->id)->where('user_id', $invoice->user->id)->get()->last();

    if (!empty($statement)) {
      $balance   = $statement->balance;
    }


    if (!empty($balance)) {
      return $balance;
    } else {
      return " ";
    }
  }

  public static function calculateAging($invoice, $previousAging, $monthsOverdue)
  {

    # House keepping
    $user = $invoice->user;

    # if months overdue is greater that 180
    if ($monthsOverdue >= 6) {

      $prev  = $invoice->created_at->addMonths($previousAging);
      $next  = $invoice->created_at->addMonths($monthsOverdue);

      #dd($prev .' pre - and -next '. $next);
      $statement = Statement::where('user_id', $user->id)->where('balance', '>', 0)->where('created_at', '>', $prev)->get()->last();
    } else {

      $prev  = $invoice->created_at->addMonths($previousAging);
      $next  = $invoice->created_at->addMonths($monthsOverdue);

      #dd($prev .' pre - and -next '. $next);
      $statement = Statement::where('user_id', $user->id)->where('balance', '>', 0)->whereBetween('created_at', [$prev, $next])->get()->last();
    }




    if (!empty($statement)) {

      return $statement->balance;
    } else {
      return " ";
    }
  }



  public static function getNeeded($user, $course, $sub_study_level_id)
  {

    $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->where('sub_study_level_id', $sub_study_level_id)->get()->first();

    $date = $enrollment->created_at->toDateString();

    $invoice = Invoice::where('user_id', $user->id)->where('created_at', '>=', $date)->get()->first();

    return $invoice;
  }

  public static function getNeededProfessional($user, $course)
  {
    #dd($user);
    $enrollment = Enrollment::where('user_id', $user->id)->where('professional_course_id', $course->id)->get()->first();


    $invoice = Quotation::where('user_id', $user->id)->where('created_at', $enrollment->created_at)->get()->first();
    return $invoice;
  }

  public static function aging($invoice, $balance, $date)
  {

    #$invoice = Invoice::where('user_id',$user->id)->where('paid',0)->get()->last();
    $daysAge = $invoice->daysAging;

    if (empty($daysAge)) {
      return "";
    }

    $now = Carbon::now();

    $diff = $invoice->created_at->diffInDays($date);


    $user    = User::find($invoice->user_id);


    if ($diff > 1 && $diff < 31) {
      return $diff;
    }
    if ($daysAge > 30 && $daysAge < 60) {
      return $diff;
    }
    if ($daysAge >= 60 && $daysAge <= 90) {
      return $diff;
    }
    if ($daysAge > 90) {
      return $diff;
    }




    /*
    switch ($range) {
      case '1':

          if ($daysAge > 1 && $daysAge < 31) {
            return $balance;
          }
          else {
            return "";
          }

      break;

      case '2':

          if ($daysAge > 30 && $daysAge < 60) {
            return $balance;
          }
          else {
            return "";
          }

      break;

      case '3':

          if ($daysAge >= 60 && $daysAge <= 90) {
            return $balance;
          }
          else {
            return "";
          }

      break;

      case '4':

          if ($daysAge > 90) {
            return $balance;
          }

      break;

      default:
        // code...
        break;
    }*/
  }


  public static function agingAll($range)
  {

    # find all students owing
    $students = User::where('role_id', 2)->get();

    foreach ($students as $student) {

      $balance = $student->balance($student->id);
      if ($balance > 0) {
        # find the last invoice
        $invoice = Invoice::where('user_id', $student->id)->get()->last();
        $invoices[] = $invoice;
      }
    }


    switch ($range) {
      case '1':

        foreach ($invoices as $invoice) {

          if ($invoice->daysAging > 1 && $invoice->daysAging < 31) {
            #find the user
            $user = User::find($invoice->user_id);
            $balance = $user->balance($user->id);
            $balances[] = $balance;
          }
        }
        $balances = array_sum($balances);
        return $balances;

        break;

      case '2':

        foreach ($invoices as $invoice) {

          if ($invoice->daysAging > 30 && $invoice->daysAging < 61) {
            #find the user
            $user = User::find($invoice->user_id);
            $balance = $user->balance($user->id);
            $balances[] = $balance;
          }
        }
        $balances = array_sum($balances);
        return $balances;


        break;

      case '3':

        foreach ($invoices as $invoice) {

          if ($invoice->daysAging > 61 && $invoice->daysAging < 91) {
            #find the user
            $user = User::find($invoice->user_id);
            $balance = $user->balance($user->id);
            $balances[] = $balance;
          }
        }
        $balances = array_sum($balances);
        return $balances;


        break;

      case '4':

        foreach ($invoices as $invoice) {

          if ($invoice->daysAging > 91) {
            #find the user
            $user = User::find($invoice->user_id);
            $balance = $user->balance($user->id);
            $balances[] = $balance;
          }
        }
        $balances = array_sum($balances);
        return $balances;


        break;

      default:
        // code...
        break;
    }
  }

  public static function getApiFormat($id)
  {
    $invoice = Invoice::find($id);

    if ($invoice->user->student_id) {
      $studentID = $invoice->user->student_id;
    } else {
      $studentID = " ";
    }

    if (!empty($invoice->user->enrollment)) {

      if (!empty($invoice->user->enrollment->course->certification)) {
        $course = $invoice->user->enrollment->course->certification->type . ' - ' . $invoice->user->enrollment->course->name;
      } else {
        $course = $invoice->user->enrollment->period;
      }
    } else {
      $course = " ";
    }

    $image = $invoice->user->image;

    $user  = $invoice->user;

    if ($user->programs) {
      foreach ($user->programs as $program) {
        $_program = Program::data($program->id);
        $_programs[] = $_program;
      }

      $userProgram = UserProgram::where('userID', $user->id)->get()->last();
      if ($userProgram) {
        $currentProgram = Program::data($userProgram->programID);
        $currentProgramName = $currentProgram['qualification'] . ' - ' . $currentProgram['name'];
      }
    }
    if (empty($_programs)) {
      $_programs = [];
      $currentProgram = [];
      $currentProgramName = '';
    }

    if (!empty($invoice->creditNote) && $invoice->creditNote->authorized == 1) {
      $state = 'Canceled';
    } else {
      $state = 'Active';
    }

    $invoice = [
      'invoiceID'      => $invoice->id,
      'first_name'     => $invoice->user->first_name,
      'middle_name'    => $invoice->user->middle_name,
      'last_name'      => $invoice->user->last_name,
      'names'          => $invoice->user->first_name . ' ' . $invoice->user->middle_name . ' ' . $invoice->user->last_name,
      'studentID'      => $studentID,
      'state'          => $state,
      'program'        => $currentProgramName,
      'user_img'       => "/user_photos/" . '' . $image,
      'amount'         => number_format($invoice->details->sum('ammount')),
      'date_posted'    => $invoice->created_at->toFormattedDateString(),
    ];

    return $invoice;
  }
}
