<?php

namespace App\Models;

use App\Models\Academics\ClassAssessment;
use App\Models\Academic\GradeBookImport;
use App\Support\Progression;
use Illuminate\Database\Eloquent\Model;

class GradeBook extends Model
{
  protected $table = "ac_gradeBooks";
  protected $guarded = ['id'];

  public function classAssessment()
  {
    return $this->hasMany(ClassAssessment::class, 'id');
  }
  public function users()
  {
    return $this->hasMany(User::class, '', 'id');
  }

  public static function importData($id,$programID)
  {

    $data = GradeBookImport::find($id);

    $user = User::where('student_id', $data->studentID)->get()->first();
    $wError = 0;
    if ($user) {
      //$user_ = General::jsondataBasic($user->id);
      //$names = $user_['names'];
      $names = $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name;
      $status = '';
    } else {
      $names = 'No Identify';
      $status = 'Wrong Student ID';
      $wError = 1;
    }
    if ($programID == 32){
        $mark = [
            'type'  => 0,
            'mark'  => $data->total,
        ];
    }else{
        $mark = [
            'type'  => 1,
            'mark'  => $data->total,
        ];
    }


    $finalStatus = '';
    if ($data->published == 0) {
      $finalStatus = 'Not Published';
    }

    if ($data->published == -1) {
      $finalStatus = 'Course Not enrolled';
    }

    if ($data->published == -2) {
      $finalStatus = 'Not Enrolled in Course';
    }

    return [
      'key'             => $data->id,
      'id'              => $data->id,
      'studentID'       => $data->studentID,
      'names'           => $names,
      'score'           => Progression::score($mark),
      'total'           => $data->total,
      'courseCode'      => $data->code,
      'courseTitle'     => $data->title,
      'programID'       => $data->programID,
      'academicPeriodID' => $data->academicPeriodID,
      'status'          => $status,
      'finalStatus'     => $finalStatus,
      'withError'       => $wError,
    ];
  }
}
