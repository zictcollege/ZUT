<?php

namespace App\Models\Admissions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AdmissionStatus extends Model
{
    protected $table = "ac_admission_status";
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'userID');
    }

}
