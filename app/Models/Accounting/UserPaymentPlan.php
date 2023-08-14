<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class UserPaymentPlan extends Model
{
    protected $table = "user_paymentplans";
    protected $guarded = ['id'];
    protected $fillable = ['userID','paymentPlanID','key'];
    
}
