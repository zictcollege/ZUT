<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class ProfomaInvoiceDetails extends Model
{
    protected $table = "fn_proforma_invoie_details";
    protected $guarded = ['id'];

    public function invoice() {
        return $this->belongsTo(ProfomaInvoice::class,'proformaInvoiceID','id');
    }
}
