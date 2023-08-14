<?php

namespace App\Traits\Finance\Accounting;


use App\Models\Academic\AcademicPeriodFee;
use App\Models\Accounting\BankStatement;
use App\Models\Accounting\Fee;
use App\Receipt;
use App\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;


trait BankStatementTrait
{

    public function upload(Request $request) {

        //dd(request()->all());

        $path = $request->file('attachment')->getRealPath();
        $data = Excel::toCollection('', $path, null, \Maatwebsite\Excel\Excel::TSV)[0];


        $data->forget(0);

        foreach ($data as $row) {

            $sizeofRow = sizeof($row);
            //dd($sizeofRow);
            //dd($row);

            // Indo zambia bank 
            if ($sizeofRow == 9) {
                
                //dd($row);
                if ($row[3] == "CASH DEPOSIT" && !empty($row[6])) {

                    
                    $statementRow = BankStatement::where('transactionCode',$row[4])->get()->first();

                    if (empty($statementRow)) {

                        $statementRow = new BankStatement();
                        $statementRow->txnBranchCode    = 'Indo Zambia';
                        $statementRow->txnBranchName    = 'Indo Zambia';
                        $statementRow->txnDate          = $row[1];
                        $statementRow->description      = $row[2];
                        $statementRow->transactionCode  = $row[4];
                        $statementRow->depositAmount    = $row[6];
                        $statementRow->uploadedBy       = request('user');
                        $statementRow->save();
        
                    }              
                }

            }
            
            
            
            if ($sizeofRow == 13) {
                
                  
                if ($row[9] == "C" && !empty($row[11]) && $row[1] != "XAPIT BRANCH") {

                    //dd($row);
                    $statementRow = BankStatement::where('transactionCode',$row[7])->get()->first();

                    if (empty($statementRow)) {

                        $statementRow = new BankStatement();
                        $statementRow->txnBranchCode    = $row[0];
                        $statementRow->txnBranchName    = $row[1];
                        $statementRow->txnDate          = $row[2];
                        $statementRow->description      = $row[4];
                        $statementRow->transactionCode  = $row[7];
                        $statementRow->depositAmount    = $row[11];
                        $statementRow->uploadedBy       = request('user');
                        $statementRow->save();
        
                    }              
                }

            }

            if ($sizeofRow == 14) {
                
                 //dd($row);
                if ($row[9] == "C" && !empty($row[11]) && $row[1] != "XAPIT BRANCH" && !empty($row[4])) {

               // dd($row);
                    $statementRow = BankStatement::where('transactionCode',$row[7])->get()->first();

                    if (empty($statementRow)) {

                        $statementRow = new BankStatement();
                        $statementRow->txnBranchCode    = $row[0];
                        $statementRow->txnBranchName    = $row[1];
                        $statementRow->txnDate          = $row[2];
                        $statementRow->description      = $row[4];
                        $statementRow->transactionCode  = $row[7];
                        $statementRow->depositAmount    = $row[11];
                        $statementRow->uploadedBy       = request('user');
                        $statementRow->save();
        
                    }              
                }

            }
            
            
            if ($sizeofRow > 9 && $sizeofRow <= 18 && $sizeofRow != 13) {
                
                  
                if ($row[13] == "C" && !empty($row[15]) && empty($row[14]) && $row[4] != NULL) {

                    //dd($row);
                    $statementRow = BankStatement::where('transactionCode',$row[11])->get()->first();

                    if (empty($statementRow)) {

                        $statementRow = new BankStatement();
                        $statementRow->txnBranchCode    = $row[0];
                        $statementRow->txnBranchName    = $row[1];
                        $statementRow->txnDate          = $row[2];
                        $statementRow->description      = $row[4];
                        $statementRow->transactionCode  = $row[11];
                        $statementRow->depositAmount    = $row[15];
                        $statementRow->uploadedBy       = request('user');
                        $statementRow->save();

                         
        
                    }              
                }

            }
            
            if ($sizeofRow > 25) {
                # code...
           

                if ($row[27] == "C") {
                    $statementRow = BankStatement::where('transactionCode',$row[21])->get()->first();
    
                    if (empty($statementRow)) {
    
                        $statementRow = new BankStatement();
                        $statementRow->txnBranchCode    = $row[0];
                        $statementRow->txnBranchName    = $row[2];
                        $statementRow->txnDate          = $row[8];
                        $statementRow->description      = $row[14];
                        $statementRow->transactionCode  = $row[21];
                        $statementRow->depositAmount    = $row[30];
                        $statementRow->uploadedBy       = request('user');
                        $statementRow->save();
        
                    }
                    
                }


            }
                
                    
        }

        return BankStatement::all();

    }

    public function uploadedStatements() {
        $rowStatements =  BankStatement::all();

        $pendingCount    = 0;
        $reconciledCount = 0;
        $failedCount     = 0;

        foreach ($rowStatements as $rowStatement) {
            $statement = BankStatement::data($rowStatement->id);

            switch ($statement['status']) {
                case 'Pending Reconcilication':
                        $pending[] = $statement;
                        $pendingCount = $pendingCount + 1;
                        break;
                case 'Reconciled':
                        $reconciled[]       = $statement;
                        $reconciledCount    = $reconciledCount + 1;
                        break;
                case 'Failed to Reconcile':
                        $failed[] = $statement;
                        $failedCount = $failedCount + 1;
                        break;
            }

        }

        if (empty($pending)) {
            $pending = [];
        }

        if (empty($reconciled)) {
            $reconciled = [];
        }

        if (empty($failed)) {
            $failed = [];
        }

        return [
            'pending'           => $pending,
            'reconciled'        => $reconciled,
            'failed'            => $failed,
            'pendingCount'      => $pendingCount,
            'failedCount'       => $failedCount,
            'reconciledCount'   => $reconciledCount,
        ];

    }

    public function reconcileAll(Request $request) {

        $pendingStatements = BankStatement::where('status',0)->get();
        
        
        foreach($pendingStatements as $pendingStatement) {

            
            $user = User::search($pendingStatement->description)->get()->first();

            if ($user) {
                
                
                $postedReceipts = Receipt::where('user_id',$user->id)->get();

                foreach ($postedReceipts as $postedReceipt) {

                    $postedAmount = str_replace( ',', '', $pendingStatement->depositAmount );

                    dd($postedReceipt->ammount_paid .' - '. $postedAmount);
                    if ($postedReceipt == $pendingStatement->depositAmount) {
                        
                        dd($postedReceipt->ammount_paid .' - '. $postedAmount);
                    }

                }
                

            }


        }


    }
    

}
