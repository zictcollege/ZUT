<?php

namespace App\Traits\Finance\Reports;

use App\CreditNote;
use Illuminate\Http\Request;


trait CreditNoteTrait
{

    public function indexCreditNote() {
        return view('app.modules.accounting.reports.creditnotes.index');
    }

    public function fetchCreditNotes(Request $request) {

        $from  = request('date_from');
        $to    = request('date_to');

        $creditNotes = CreditNote::whereBetween('created_at', [$from,$to])->get();

        foreach ($creditNotes as $cn) {
            $creditNote = CreditNote::data($cn->id);
            $creditnotes[] = $creditNote;
        }
        return $creditnotes;
    }

}


