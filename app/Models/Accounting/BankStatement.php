<?php

namespace App\Models\Accounting;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class BankStatement extends Model
{

    //use Searchable;

    protected $table = "fn_bank_deposits";
    protected $guarded = ['id'];


    public function uploadedByUser() {
        return $this->belongsTo(User::class,'uploadedBy','id');
    }

    public static function data($id) {
        
        $rowStatement = BankStatement::find($id);




        switch ($rowStatement->status) {
            case '0':
                $status = 'Pending Reconcilication';
                break;
            case '1':
                $status = 'Reconciled';
                break;
            case '-1':
                $status = 'Failed to Reconcile';
                break;
            case '-2':
                $status = 'Duplicated Transaction Code';
                break;
        }

        if ($rowStatement->receiptID != NULL) {
            $receiptNumber = $rowStatement->receiptID;
        } else {
            $receiptNumber = '';
        }
        
         return [
            'txnBranchCode'     => $rowStatement->txnBranchCode,
            'txnBranchName'     => $rowStatement->txnBranchName,
            'txnDate'           => Carbon::parse($rowStatement->txnDate)->toFormattedDateString('D/M/Y'),
            'description'       => $rowStatement->description,
            'transactionCode'   => $rowStatement->transactionCode,
            'depositAmount'     => $rowStatement->depositAmount,
            'uploadedBy'        => $rowStatement->uploadedByUser->first_name .' '. $rowStatement->uploadedByUser->last_name,
            'status'            => $status,
            'receiptNumber'     => $receiptNumber, 
        ];

 
    }

}
