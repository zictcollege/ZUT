<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\academicPeriods
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicPeriods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicPeriods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicPeriods query()
 * @mixin \Eloquent
 */
class AcademicPeriods extends Model
{
    use HasFactory;
    protected $table = 'ac_academicPeriods';
    protected $fillable = ['code', 'registrationDate', 'lateRegistrationDate', 'acStartDate', 'acEndDate', 'periodID', 'resultsThreshold', 'registrationThreshold', 'type', 'examSlipThreshold', 'studyModeIDAllowed'];

    public function periodType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(period_types::class, 'type');
    }
    public function studyMode(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(studyModes::class, 'studyModeIDAllowed');
    }
    public function intake(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Intakes::class, 'intakeID');
    }
    public function classes()
    {
        return $this->hasMany(Classes::class, 'academicPeriodID');
    }
    public function Periodfees()
    {
        return $this->hasMany(PeriodFees::class, 'academicPeriodID');
    }


}
