<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $table = "fn_chart_of_accounts";
    protected $guarded = ['id'];
}
