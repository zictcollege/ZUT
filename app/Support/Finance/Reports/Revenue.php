<?php

namespace App\Traits\Finance\Reports;
use App\Models\Accounting\Invoice;
use Illuminate\Http\Request;


trait Revenue
{

    # Return revenue analysis report view
    public function revenueAnalysisView() {
        return view('app.backend.actors.finance.reports.revenue-analysis.index');
    }
    public function invoices(Request $request) {
        
        $from  = request('from');
        $to    = request('to');
        $invs = [];
        $invoices = Invoice::whereBetween('created_at', [$from,$to])->get();

        foreach ($invoices as $invoice) {
            $inv    = Invoice::revenueDataPerInvoice($invoice->id);
            $invs[] = $inv;
        }
        return $invs;

    }


}


