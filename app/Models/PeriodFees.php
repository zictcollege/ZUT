<?php

namespace App\Models;

use App\Models\Academics\AcademicPeriods;
use App\Models\Academics\CourseLevels;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodFees extends Model
{
    protected $table = 'ac_academicPeriodFees';
    protected $fillable = ['academicPeriodID', 'feeID', 'amount','added_by_id','p_f','crf','once_off'];
    use HasFactory;

    public function addedBy() {
        return $this->belongsTo(User::class,'added_by_id','id');
    }
    public function academicPeriods()
    {
        return $this->belongsTo(AcademicPeriods::class, 'academicPeriodID');
    }

    public function fees()
    {
        return $this->belongsTo(Fees::class, 'feeID');
    }

    public function studymode()
    {
        return $this->belongsTo(CourseLevels::class, 'studyModeID');
    }
}
