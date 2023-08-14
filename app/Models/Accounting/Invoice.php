<?php

namespace App\Models\Accounting;


use App\Models\Academics\AcademicPeriods;
use App\Models\User;
use App\Traits\Finance\Accounting\Invoicing;
use Illuminate\Database\Eloquent\Model;
use App\Models\Accounting\InvoiceDetail;
use App\Traits\Finance\Accounting\OldInvoice;
use App\Traits\Finance\Accounting\AgeAnalysis;

class Invoice extends Model
{

    use Invoicing;
    use OldInvoice;
    use AgeAnalysis;

    protected $table = "invoices";
    protected $guarded = ['id'];

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class)->orderBy('ammount', 'DESC');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function creditNote()
    {
        return $this->hasOne(CreditNote::class, 'invoice_id', 'id');
    }

    public static function data($id)
    {
        $academicPeriod = [];
        $academicPeriodName = 'Not Attached';
        $inv   = Invoice::find($id);
        $user  = User::find($inv->user->id);

        if ($inv->creditNote) {
            $cn = $inv->creditNote;
            $invoiceStatus = 'Canceled';
        } else {
            $invoiceStatus = 'Submited';
        }

        if ($inv->academicPeriodID) {
            $academicPeriod = AcademicPeriods::dataMini($inv->academicPeriodID);
            $academicPeriodName = $academicPeriod['name'];
        }

        if ($user->student_id) {
            $student_id = $user->student_id;
        } else {
            $student_id = $user->guest_id;
        }

        $invoiceDetails = [];

        foreach ($inv->details as $detail) {
            $dtl = [
                'id'                    => $detail->id,
                'key'                   => $detail->id,
                'invoice_id'            => $detail->invoice_id,
                'description'           => $detail->description,
                'ammount'               => $detail->ammount,
                'chart_of_account_id'   => $detail->chart_of_account_id,
                'feeID'                 => $detail->feeID,
            ];

            $invoiceDetails[]  = $dtl;
            
        }

        

        return  [
            'id'                    => $inv->id,
            'names'                 => $inv->user->first_name . ' ' . $inv->user->middle_name . ' ' . $inv->user->last_name,
            'student_id'            => $student_id,
            'date'                  => $inv->created_at->toFormattedDateString(),
            'total'                 => number_format($inv->details->sum('ammount')),
            'totalClear'            => $inv->details->sum('ammount'),
            'details'               => $inv->details,
            'invoiceDetails'        => $invoiceDetails,
            'invoiceStatus'         => $invoiceStatus,
            'academicPeriodName'    => $academicPeriodName,
            'academicPeriod'        => $academicPeriod,
        ];
    }
}
