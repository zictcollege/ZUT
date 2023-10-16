<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditNoteApproval extends Model
{
    protected $guarded = ['id'];

    protected $table 	= "credit_note_approvals";
    public function invoice()
    {
      return $this->belongsTo(Invoice::class,'invoice_id','id');
    }
    public function user()
    {
      return $this->belongsTo(User::class,'user_id','id');
    }
}
