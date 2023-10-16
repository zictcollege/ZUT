<?php

namespace App\Models\Accounting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    protected $table = 'credit_notes';
    use HasFactory;
    protected $guarded = ['id'];

    public function details()
    {
        return $this->hasMany(CreditnoteDetail::class);
    }
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by', 'id');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function authorizedBy()
    {
        return $this->belongsTo(User::class, 'authorized_by', 'id');
    }
    public function mainAuthorizer()
    {
        return $this->belongsTo(User::class, 'authorizedByMain', 'id');
    }

    public static function data($id)
    {

        $creditnote = CreditNote::find($id);
        $status = '';
        if ($creditnote->authorized == 1) {
            $authorized = "Authorized";
        }
        if ($creditnote->authorized == 0) {
            $authorized = "Pending";
        }
        if ($creditnote->authorized == -1) {
            $authorized = "Declined";
            $status = "Declined";
        }

        if ($creditnote->issuedBy) {
            $ib = $creditnote->issuedBy->first_name . ' ' . $creditnote->issuedBy->middle_name . ' ' . $creditnote->issuedBy->last_name;
        } else {
            $ib = '';
        }

        if ($creditnote->authorizedBy) {
            $ab = $creditnote->authorizedBy->first_name . ' ' . $creditnote->authorizedBy->middle_name . ' ' . $creditnote->authorizedBy->last_name;
        } else {
            $ab = '';
        }
        if (!empty($creditnote->authorizedByMain) && $creditnote->mainAuthorizer) {
            $ma = $creditnote->mainAuthorizer->first_name . ' ' . $creditnote->mainAuthorizer->middle_name . ' ' . $creditnote->mainAuthorizer->last_name;
        } else {
            $ma = '';
        }

        if ($creditnote->authorizedMain == 1) {
            $status = 'Approved';
        }
        if($creditnote->authorizedMain == -1) {
            $status = 'Declined';
        }
        if($creditnote->authorizedMain == 0)
        {
            if ($status != 'Declined') {
                $status = 'Pending';
            }
        }


        return $creditNote = [
            'key'             => $creditnote->id,
            'id'             => $creditnote->id,
            'invoice_id'     => $creditnote->invoice->id,
            'studentid'      => $creditnote->user->student_id,
            'name'           => $creditnote->user->first_name . ' ' . $creditnote->user->middle_name . ' ' . $creditnote->user->last_name,
            'note'           => $creditnote->comment,
            'issued_by'      => $ib,
            'comment'        => $creditnote->comment,
            'approved'       => $creditnote->approved,
            'amount'         => $creditnote->invoice->details->sum('ammount'),
            'date_requested' => $creditnote->created_at->toFormattedDateString(),
            'invoiceDetails' => $creditnote->invoice->details,
            'authorized'     => $authorized,
            'authorized_by'  => $ab,
            'status'         => $status,
            'mainAuth'       => $ma,
            'total'          => env('BILLING_CURRENCY') . ' ' . $creditnote->invoice->details->sum('ammount'),

        ];
    }


    public static function checkSignatoryLevel($userID) {
        $authUser = User::find($userID);
        $signatoryLevel = 0;
        $director = User::where('id',$userID)->role('Executive Director')->get()->first();
        # Chech for authenticated signatory
        $userRoles = $authUser->roles;

        foreach ($userRoles as $role) {

            if ($role->slug == "director") {
                $signatoryLevel = 2;
            }
            if ($role->slug == "finance-executive") {
                $signatoryLevel = 1;
            }
            if ($role->slug == "hfa") {
                $signatoryLevel = 1;
            }
        }

        return $signatoryLevel;
    }
}
