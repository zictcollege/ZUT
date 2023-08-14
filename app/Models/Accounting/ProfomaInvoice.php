<?php

namespace App\Models\Accounting;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ProfomaInvoice extends Model
{
    protected $table = "fn_proforma_invoice";
    protected $guarded = ['id'];

    public function user() {
    	return $this->belongsTo(User::class,'userID','id');
    }

    public function details() {
        return $this->hasMany(ProfomaInvoiceDetails::class,'proformaInvoiceID','id')->orderBy('amount','DESC');
    }

    public static function data($id) {
        $inv   = ProfomaInvoice::find($id);
        $user  = User::find($inv->user->id);

        if ($user->student_id) {
            $student_id = $user->student_id;
        } else {
            $student_id = $user->guest_id;
        }
        return $invoice = [
            'id'          => $inv->id,
            'names'       => $inv->user->first_name .' ' . $inv->user->middle_name .' '. $inv->user->last_name,
            'student_id'  => $student_id,
            'date'        => $inv->created_at->toFormattedDateString(),
            'total'       => number_format($inv->details->sum('amount')),
            'totalPlain'  => $inv->details->sum('amount'),
            'details'     => $inv->details,
        ];

    }


}
