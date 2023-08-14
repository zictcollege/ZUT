<?php

namespace App\Models\Accounting;

use App\Models\Academics\AcademicPeriods;
use App\Models\Academics\Classes;
use App\Models\Academic\AcademicPeriod;
use App\Models\Academic\AcademicPeriodFee;
use App\Models\Academic\AcClass;
use App\Models\Academic\StudyMode;
use App\Models\PeriodFees;
use App\Traits\Finance\Accounting\ChartOfAccounts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Fee extends Model
{
    protected $table = "ac_fees";
    protected $guarded = ['id'];



    public static function data($id,$academicPeriodID,$classID = 0) {

    $registration = AcademicPeriods::find($academicPeriodID);
    $penalty = DB::table('ac_fees')
        ->where('name' ,'=','Late Registration')
        ->first();
        if(!empty($academicPeriodID)){
          if($penalty->id == $id){
            if (strtotime($registration->lateRegistrationDate) > strtotime(date("Y-m-d". ' +10 day')))
            {

            }else{
              return [];
            }

          }

        }

                 //I
        $fee = Fee::find($id);



        if ($academicPeriodID && $fee) {

            if ($classID > 0) {
                $acFee = PeriodFees::where('feeID',$fee->id)->where('academicPeriodID',$academicPeriodID)->where('class_id',$classID)->get()->first();
            }else {
                $acFee = PeriodFees::where('feeID',$fee->id)->where('academicPeriodID',$academicPeriodID)->get()->first();
            }


            if ($acFee) {



                if ($acFee->publishedBy) {
                    $published_by = $acFee->publishedBy->first_name .' '. $acFee->publishedBy->middle_name .' '. $acFee->publishedBy->last_name;
                }else {
                    $published_by  = '';
                }

                if ($acFee->published == 1) {
                    $status = "Published";
                }else {
                    $status = "Pending";
                }

                if ($acFee->once_off == 1) {
                    $once_off = "Once Off Fee";
                }else if($acFee->p_f ==1){

                    $once_off = "Penalty Fee";
                }
                else {
                    $once_off = "Recurring Fee";
                }
                if ($acFee->crf) {
                    $once_off = "Course Repeat Fee";
                }



                if ($classID > 0) {
                    $_class      = Classes::find($classID);
                    if ($_class && $_class->id) {
                        $class       = Classes::data($_class->id,0,null);
                        $course_name = $class['course_name'];
                        $course_code = $class['course_code'];
                        $name        = $class['course_code'] .' - '. $class['course_name'];
                    }else {
                        $name  = $fee->name;
                    }


                }else {

                    $course_name = '';
                    $course_code = '';
                    $class       = [];
                    $name        = $fee->name;
                }

                if (empty($class)) {
                    $course_name = '';
                    $course_code = '';
                    $class       = [];
                    $name        = $fee->name;
                }

                $_fee = [
                    'key'                   => $fee->id,
                    'id'                    => $fee->id,
                    'name'                  => $name,
                    'study_mode'            => $acFee->studymode->name,
                    'chart_of_account_name' => $fee->coa->account_name,
                    'chart_of_account_id'   => $fee->coa->account_id,
                    'chart_of_account_type' => $fee->coa->account_type,
                    'amount'                => $acFee->amount,
                    'added_by'              => $acFee->addedBy->first_name .' '. $acFee->addedBy->middle_name .' '. $acFee->addedBy->last_name,
                    'status'                => $status,
                    'published_by'          => $published_by,
                    'once_off'              => $once_off,
                    'crf'                   => $acFee->crf,
                    'is_tuition'            => $fee->coa->is_tuition,
                    'class'                 => $class,
                    'course_name'           => $course_name,
                    'course_code'           => $course_code,
                ];



                return $_fee;
            }



        }else {
            $_fee = [
                'key'                   => $fee->id,
                'id'                    => $fee->id,
                'name'                  => $fee->name,
                'chart_of_account_name' => $fee->coa->account_name,
                'chart_of_account_id'   => $fee->coa->account_id,
                'chart_of_account_type' => $fee->coa->account_type,
                'is_tuition'            => $fee->coa->is_tuition,
            ];
            return $_fee;

        }




    }

    public function coa() {
     return $this->belongsTo(ChartOfAccount::class,'chart_of_account_id','account_id');
    }

    public function academicPeriod()
    {
      return $this->belongsTo(PeriodFees::class,'id');
    }


}
