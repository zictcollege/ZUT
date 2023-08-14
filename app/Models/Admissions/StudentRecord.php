<?php

namespace App\Models\Admissions;

use App\Models\Academics\AcademicPeriods;
use App\Models\Academics\CourseLevels;
use App\Models\Academics\Intakes;
use App\Models\Academics\period_types;
use App\Models\Academics\Programs;
use App\Models\Academics\studyModes;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRecord extends Model
{
    protected $table = 'ac_student_records';
    protected $fillable = ['user_id', 'intakeID', 'level_id', 'typeID','student_id','year_admitted'];
    use HasFactory;

    public function userinfo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function intake(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Intakes::class, 'intakeID');
    }
    public function levels()
    {
        return $this->belongsTo(CourseLevels::class, 'level_id');
    }
    public function periodType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(period_types::class, 'typeID');
    }

    protected $primaryKey = 'id';

    // Relationship with academic period
    public function academicPeriod()
    {
        return $this->belongsTo(AcademicPeriods::class, 'intakeID', 'intakeID');
    }

}
