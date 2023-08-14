<?php

namespace App\Models\Academics;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassAssessment extends Model
{
    protected $table = 'ac_classAssesments';
    protected $fillable = ['assesmentID','classID','total'];
    use HasFactory;

    public function classes()
    {
        return $this->belongsTo(Classes::class, 'classID');
    }

    public function assessments()
    {
        return $this->belongsTo(AssessmentTypes::class, 'assesmentID');
    }
}
