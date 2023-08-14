<?php

namespace App\Models\Accounting;


use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    protected $table = "invoice_details";
    protected $guarded = ['id'];

    public function invoice() {
        return $this->belongsTo(Invoice::class,'invoice_id','id');
    }

}
