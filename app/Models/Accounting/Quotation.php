<?php

namespace App\Models\Accounting;


use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = "quotations";
    protected $guarded = ['id'];

    public function details()
    {
        return $this->hasMany(QuotationDetail::class, 'quotation_id', 'id')->orderBy('ammount', 'DESC');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function detail()
    {
        return $this->hasOne(QuotationDetail::class);
    }

    // fetch all the quotations
    public function quotes()
    {
        return $this->hasMany(QuotationDetail::class, 'quotation_id', 'id');
    }

    public function quotation_total($id)
    {
        return $total = QuotationDetail::where('quotation_id', $id)->sum('ammount');
    }

    public static function last_quotation_total($id)
    {

        $quotation = Quotation::all()->where('user_id', $id)->last();
        $quotation_id = $quotation->id;
        return $total = QuotationDetail::where('quotation_id', $quotation_id)->sum('ammount');
    }

    public static function data($id)
    {
        $quote = Quotation::find($id);

        $user  = User::find($quote->user->id);

        if ($user->student_id) {
            $student_id = $user->student_id;
        } else {
            $student_id = $user->guest_id;
        }

        return $Quote = [
            'id'       => $quote->id,
            'names'    => $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name,
            'student_id'  => $student_id,
            'date'     => $quote->created_at->toFormattedDateString(),
            'total'    => number_format($quote->details->sum('ammount')),
            'details'  => $quote->details,
        ];
    }
}
