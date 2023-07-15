<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    protected $table = 'ac_courses';
    use HasFactory;
    protected $fillable = ['code', 'name'];

    public function programCourses()
    {
        return $this->hasMany(ProgramCourses::class, 'courseID');
    }
    // Define the relationship with ac_classes table
    public function classes()
    {
        return $this->hasMany(Classes::class, 'courseID');
    }
}
