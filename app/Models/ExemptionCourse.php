<?php

namespace App\Models\Academic;

use App\Local;
use Illuminate\Database\Eloquent\Model;

class ExemptionCourse extends Model
{
  protected $table = "ac_exemption_courses";
  protected $guarded = ['id'];

  public function exemption()
  {
    return $this->belongsTo(User::class, 'exemptionID', 'id');
  }
  
  public function course() {
    return $this->belongsTo(Course::class,'courseID','id');
  }

}
