<?php

namespace App\Traits\Finance\Accounting;

use App\Models\Academic\AcademicPeriodFee;
use App\Models\Accounting\Fee;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;


trait FeeTrait
{


    public function store(Request $request) {

        $this->Validate($request, array(
            'coaID'    => 'required',
            'name'     => 'required|unique:ac_fees',
        ));

        $fee = new Fee();
        $fee->chart_of_account_id = request('coaID');
        $fee->name               = request('name');
        $fee->save();

        $_fees = Fee::where('archieved',0)->get();
        foreach ($_fees as $fee) {
            $_fee = Fee::data($fee->id,'');
            $fees[] = $_fee;
        }
        return $fees;
    }

    public function data() {

        $_fees = Fee::where('archieved',0)->get();
        foreach ($_fees as $fee) {
            $_fee = Fee::data($fee->id,'');
            $fees[] = $_fee;
        }
        return $fees;
    }

    public function update(Request $request) {

        $fee           = Fee::where('id',request('key'))->get()->first();
        $fee->name     = request('name');
        $fee->save();

        $_fees = Fee::where('archieved','0')->get();
        foreach ($_fees as $fee) {
            $_fee = Fee::data($fee->id,'');
            $fees[] = $_fee;
        }
        return $fees;

    }

    public function archieve(Request $request) {

        $fee             = Fee::where('id',request('key'))->get()->first();
        $fee->archieved  = 1;
        $fee->save();

        $_fees = Fee::where('archieved','0')->get();
        foreach ($_fees as $fee) {
            $_fee = Fee::data($fee->id,'');
            $fees[] = $_fee;
        }
        return $fees;

    }

    public function checkFeeType($id) {
        $fee = Fee::find($id);

        if ($fee->name == "Tuition") {
            return 1;
        }else {
            return 0;
        }

    }

}
