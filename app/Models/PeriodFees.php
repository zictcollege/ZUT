<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodFees extends Model
{
    protected $table = 'ac_academicPeriodFees';
    protected $fillable = ['academicPeriodID', 'feeID', 'amount','added_by_id'];
    use HasFactory;

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
