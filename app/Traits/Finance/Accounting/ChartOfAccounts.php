<?php

namespace App\Traits\Finance\Accounting;

use App\Models\Accounting\ChartOfAccount;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


trait ChartOfAccounts
{

    # index
    # returns main chart of accounts view
    public function chartOfAccountsIndex() {
        return view('app.backend.actors.finance.chartofaccounts.index');
    }
    public function chartOfAccountsShow($id) {
        $coa = ChartOfAccount::where('account_id',$id)->get()->first();
        return view('app.backend.actors.finance.chartofaccounts.show',compact('coa'));
    }
    public function dataCOA() {
        $data = ChartOfAccount::all();
        return $data;
    }
    public function getCOA($id) {
        $data = ChartOfAccount::where('account_id',$id)->get()->first();
        return $data;
    }
    public function storeCOA(Request $request) {

        $this->Validate($request, array(
            'account_name'      => 'required',
            'account_type'      => 'required',
            'account_id'        => 'required|unique:fn_chart_of_accounts',
        ));

        try {
            DB::beginTransaction();

            $coa = new ChartOfAccount();
            $coa->account_name = request('account_name');
            $coa->account_type = request('account_type');
            $coa->account_id   = request('account_id');
            $coa->description  = request('description');
            $coa->save();

            $data = ChartOfAccount::all();

            DB::commit();

            return $data;

          } catch (\Exception $e) {

            DB::rollback();
            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);

          }

    }
    public function updateCOA(Request $request) {

        $this->Validate($request, array(
            'account_name'      => 'required',
            'account_type'      => 'required',
        ));

        try {
            DB::beginTransaction();

            $coa =  ChartOfAccount::where('account_id',request('account_id'))->get()->first();
            $coa->account_name = request('account_name');
            $coa->account_type = request('account_type');
            $coa->description  = request('description');
            $coa->save();

            $data = ChartOfAccount::where('account_id',request('account_id'))->get()->first();

            DB::commit();

            return $data;

          } catch (\Exception $e) {

            DB::rollback();
            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);

          }


    }

    public function columns() {

        $coas = ChartOfAccount::all();

        foreach ($coas as $coa) {

            $column = [
                'title'      => $coa->account_name,
                'dataIndex'  => $coa->account_id,
            ];

            $columns[] = $column;

        }

        $column = [
            'title'      => $coa->account_name,
            'dataIndex'  => $coa->account_id,
        ];

        $otherColumns = [

            $column_ = [
                'title' =>  'Invoice ID',
                'dataIndex' => 'invoice_id',
            ],
            $column_ = [
                'title' =>  'Names ID',
                'dataIndex' => 'names',
            ],
            $column_ = [
                'title' =>  'Programe',
                'dataIndex' => 'program',
            ],
            $column_ = [
                'title' =>  'Student ID',
                'dataIndex' => 'student_id',
            ],
            $column_ = [
                'title' =>  'Total',
                'dataIndex' => 'total',
            ],
            $column_ = [
                'title' =>  'Date Created',
                'dataIndex' => 'date',
            ],

        ];

        $otherColumns = collect($otherColumns);

        $results = $otherColumns->merge($columns);

        $results->all();

#        $results = array_merge($otherColumns,$columns);
        return $results;

    }



}


