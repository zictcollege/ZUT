<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programs extends Model
{
    protected $table = 'ac_programs';
    use HasFactory;
    protected $fillable = ['code','name','departmentID','qualification_id','description'];

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Departments::class, 'departmentID');
    }

    public function qualification(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Qualifications::class, 'qualification_id');
    }

    public function programCourses()
    {
        return $this->hasMany(ProgramCourses::class, 'programID');
    }
}
