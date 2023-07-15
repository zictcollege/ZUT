<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLevels extends Model
{
    protected $table = 'ac_course_levels';
    use HasFactory;
    protected $fillable = ['name'];

    public function programCourses()
    {
        return $this->hasMany(ProgramCourses::class, 'level_id');
    }
}
