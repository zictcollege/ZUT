<?php

namespace App\Models\Accounting;

use App\User;
use Illuminate\Database\Eloquent\Model;

class FailedTransaction extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }



}
