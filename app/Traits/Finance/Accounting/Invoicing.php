<?php

namespace App\Traits\Finance\Accounting;

use App\Models\Accounting\Invoice;

use App\Models\Accounting\InvoiceDetail;
use App\Models\Accounting\ProfomaInvoice;
use App\Models\Accounting\ProfomaInvoiceDetails;
use App\Models\Accounting\Quotation;
use App\Models\Accounting\QuotationDetail;
use App\Models\Accounting\Statement;
use App\Models\Admissions\UserStudyModes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


trait Invoicing
{
    /**
     * Quote invoice and update user accounting information
     *
     * @param  array  $user_id, $details
     *
     */
    public static function store($user_id, $details, $academicPeriodID)
    {



        //$savedInvoice = Invoice::where('user_id', $user_id)->where('academicPeriodID', $academicPeriodID)->get()->first();
        $savedInvoice = Invoice::where('user_id', $user_id)->where('academicPeriodID', $academicPeriodID)->get()->first();

        if ($savedInvoice) {

            // total up the found saved invoice
            $total = 0;
            foreach ($details as $detail) {
                $total = $total + $detail['amount'];
            }

            // to avoid duplicates check if we have an invoice for
            // the selected AP that totals up to amount of the new invoice being raised.
            if ($savedInvoice->details->sum('ammount') == $total) {
                return;
            }
        }

        try {
            DB::beginTransaction();
            # Create Quotation

            $quotation          = new Quotation();
            $quotation->user_id = $user_id;
            $quotation->save();

            foreach ($details as $detail) {

                if ($detail['name']) {

                    if ($detail['chart_of_account_id']) {
                        QuotationDetail::create([
                            'quotation_id'          => $quotation->id,
                            'description'           => $detail['name'],
                            'ammount'               => $detail['amount'],
                            'chart_of_account_id'   => $detail['chart_of_account_id'],
                            'feeID'                 => $detail['id'],
                        ]);
                    } else {
                        QuotationDetail::create([
                            'quotation_id'          => $quotation->id,
                            'description'           => $detail['name'],
                            'ammount'               => $detail['amount'],
                            'chart_of_account_id'   => $detail['chartOfAccountID'],
                            'feeID'                 => $detail['id'],
                        ]);
                    }
                } else {


                    if ($detail->chart_of_account_id) {
                        QuotationDetail::create([
                            'quotation_id'          => $quotation->id,
                            'description'           => $detail->description,
                            'ammount'               => $detail->amount,
                            'chart_of_account_id'   => $detail->chart_of_account_id,
                            'feeID'                 => $detail->feeID,
                        ]);
                    } else {
                        QuotationDetail::create([
                            'quotation_id'          => $quotation->id,
                            'description'           => $detail->description,
                            'ammount'               => $detail->amount,
                            'chart_of_account_id'   => $detail->chartOfAccountID,
                            'feeID'                 => $detail->feeID,
                        ]);
                    }
                }
            }

            $invoice                    = new Invoice();
            $invoice->quotation_id      = $quotation->id;
            $invoice->user_id           = $user_id;
            $invoice->academicPeriodID  = $academicPeriodID;
            $invoice->save();


            $invoice = Invoice::where('user_id', $user_id)->get()->last();


            foreach ($quotation->details as $quotation_detail) {

                $existingRow = InvoiceDetail::where('invoice_id', $invoice->id)->where('feeID', $quotation_detail->feeID)->get()->first();

                if (empty($xistingRow)) {
                    InvoiceDetail::create([
                        'invoice_id'            => $invoice->id,
                        'ammount'               => $quotation_detail->ammount,
                        'description'           => $quotation_detail->description,
                        'chart_of_account_id'   => $quotation_detail->chart_of_account_id,
                        'feeID'                 => $quotation_detail->feeID,
                    ]);
                }
            }

            $statement = new Statement();
            $statement->invoice_id  = $invoice->id;
            $statement->user_id     = $user_id;
            $statement->description = "Invoice";
            $statement->debit       = $invoice->details->sum('ammount');
            $statement->save();


            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);
        }
    }


    public static function storePerfomaInvoice($user_id, $details, $academicPeriodID)
    {

        try {

            DB::beginTransaction();
            # Create Profoma Invoice

            $invoice                   = new ProfomaInvoice();
            $invoice->userID           = $user_id;
            $invoice->academicPeriodID = $academicPeriodID;
            $invoice->save();

            foreach ($details as $detail) {

                if ($detail['name']) {

                    ProfomaInvoiceDetails::create([
                        'proformaInvoiceID'     => $invoice->id,
                        'description'           => $detail['name'],
                        'amount'                => $detail['amount'],
                        'feeID'                 => $detail['id'],
                        'chartOfAccountID'      => $detail['chart_of_account_id'],
                    ]);
                } else {

                    ProfomaInvoiceDetails::create([
                        'proformaInvoiceID'     => $invoice->id,
                        'description'           => $detail->description,
                        'amount'                => $detail->amount,
                        'feeID'                 => $detail->id,
                        'chartOfAccountID'      => $detail->chart_of_account_id,
                    ]);
                }
            }




            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);
        }
    }



    public static function storeTemp($user_id, $details, $academicPeriodID)
    {

        try {
            DB::beginTransaction();
            # Create Quotation

            $ac = AcademicPeriod::find($academicPeriodID);

            $quotation             = new Quotation();
            $quotation->user_id    = $user_id;
            $quotation->created_at = $ac->acStartDate;
            $quotation->save();

            foreach ($details as $detail) {

                if ($detail['name']) {

                    if ($detail['chart_of_account_id']) {
                        QuotationDetail::create([
                            'quotation_id'          => $quotation->id,
                            'description'           => $detail['name'],
                            'ammount'               => $detail['amount'],
                            'chart_of_account_id'   => $detail['chart_of_account_id'],
                            'feeID'                 => $detail['id'],
                            'created_at'            => $ac->acStartDate,
                        ]);
                    } else {
                        QuotationDetail::create([
                            'quotation_id'          => $quotation->id,
                            'description'           => $detail['name'],
                            'ammount'               => $detail['amount'],
                            'chart_of_account_id'   => $detail['chartOfAccountID'],
                            'feeID'                 => $detail['id'],
                            'created_at'            => $ac->acStartDate,
                        ]);
                    }
                } else {

                    if ($detail->chart_of_account_id) {
                        QuotationDetail::create([
                            'quotation_id'          => $quotation->id,
                            'description'           => $detail->description,
                            'ammount'               => $detail->amount,
                            'chart_of_account_id'   => $detail->chart_of_account_id,
                            'feeID'                 => $detail->feeID,
                            'created_at'            => $ac->acStartDate,
                        ]);
                    } else {
                        QuotationDetail::create([
                            'quotation_id'          => $quotation->id,
                            'description'           => $detail->description,
                            'ammount'               => $detail->amount,
                            'chart_of_account_id'   => $detail->chartOfAccountID,
                            'feeID'                 => $detail->feeID,
                            'created_at'            => $ac->acStartDate,
                        ]);
                    }
                }
            }

            $invoice                    = new Invoice();
            $invoice->quotation_id      = $quotation->id;
            $invoice->user_id           = $user_id;
            $invoice->created_at        = $ac->acStartDate;
            $invoice->academicPeriodID  = $academicPeriodID;
            $invoice->save();

            $invoice = Invoice::where('user_id', $user_id)->get()->last();

            foreach ($quotation->details as $quotation_detail) {

                $existingRow = InvoiceDetail::where('invoice_id', $invoice->id)->where('feeID', $quotation_detail->feeID)->get()->first();

                if (empty($xistingRow)) {
                    InvoiceDetail::create([
                        'invoice_id'            => $invoice->id,
                        'ammount'               => $quotation_detail->ammount,
                        'description'           => $quotation_detail->description,
                        'chart_of_account_id'   => $quotation_detail->chart_of_account_id,
                        'feeID'                 => $quotation_detail->feeID,
                        'created_at'            => $ac->acStartDate,
                    ]);
                }
            }

            $statement = new Statement();
            $statement->invoice_id  = $invoice->id;
            $statement->user_id     = $user_id;
            $statement->description = "Invoice";
            $statement->created_at    = $ac->acStartDate;
            $statement->debit       = $invoice->details->sum('ammount');
            $statement->save();


            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'errors' => $e->getMessage()
            ], 422);
        }
    }



    public static function checkIfInvoiceHasMadetoryFees($userID, $academicPeriodID)
    {

        $invoices  = Invoice::where('academicPeriodID', $academicPeriodID)->where('user_id', $userID)->get();
        $userMode = UserStudyModes::where('userID', $userID)->get()->first();

        $check = 0; // no mandetory fees
        if (!empty($userMode) && $userMode->studyModeID == 2) {
            if ($invoices) {
                foreach ($invoices as $invoice) {
                    foreach ($invoice->details as $detail) {
                        if ($detail->description == "Examination Fee") {
                            $check = 0; // has mandtory Fees
                        }
                    }
                }
            } else {
                $check = 0;
            }
        }

        return $check;
    }
}
