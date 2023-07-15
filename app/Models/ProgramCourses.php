<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramCourses extends Model
{
    use HasFactory;

    protected $table = 'ac_programCourses';
    protected $primaryKey = 'id';
    protected $fillable = ['level_id','courseID','programID'];
    public $timestamps = true;

    public function courses()
    {
        return $this->belongsTo(Courses::class, 'courseID');
    }

    public function programs()
    {
        return $this->belongsTo(Programs::class, 'programID');
    }

    public function levels()
    {
        return $this->belongsTo(CourseLevels::class, 'level_id');
    }


}
