<?php

namespace App\Models\Academics;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intakes extends Model
{
    protected $table = 'ac_program_intakes';
    protected $fillable = ['name'];
    use HasFactory;
    public function academicPeriods()
    {
        return $this->hasMany(AcademicPeriods::class, 'intakeID');
    }
}
