<?php

namespace App\Models\Accounting;


use Illuminate\Database\Eloquent\Model;

class QuotationDetail extends Model
{
    protected $table = "quotation_details";
    protected $guarded = ['id'];

    public function quotation() {
        return $this->belongsTo(Quotation::class);
    }


}
