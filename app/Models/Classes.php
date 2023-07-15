<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'ac_classes';
    protected $fillable = ['instructorID','courseID','academicPeriodID'];
    protected $primaryKey = 'id';
    use HasFactory;

    public function academicPeriod()
    {
        return $this->belongsTo(AcademicPeriods::class, 'academicPeriodID');
    }

    // Define the relationship with ac_courses table
    public function course()
    {
        return $this->belongsTo(Courses::class, 'courseID');
    }
}
