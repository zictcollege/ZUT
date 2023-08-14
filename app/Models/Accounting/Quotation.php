<?php

namespace App\Models\Accounting;


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
        return $this->hasOne(Quotation_detail::class);
    }

    // fetch all the quotations
    public function quotes()
    {
        return $this->hasMany(Quotation_detail::class, 'quotation_id', 'id');
    }

    public function quotation_total($id)
    {
        return $total = Quotation_detail::where('quotation_id', $id)->sum('ammount');
    }

    public static function last_quotation_total($id)
    {

        $quotation = Quotation::all()->where('user_id', $id)->last();
        $quotation_id = $quotation->id;
        return $total = Quotation_detail::where('quotation_id', $quotation_id)->sum('ammount');
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
            'names'    => $quote->user->first_name . ' ' . $quote->user->middle_name . ' ' . $quote->user->last_name,
            'student_id'  => $student_id,
            'date'     => $quote->created_at->toFormattedDateString(),
            'total'    => number_format($quote->details->sum('ammount')),
            'details'  => $quote->details,
        ];
    }
}
