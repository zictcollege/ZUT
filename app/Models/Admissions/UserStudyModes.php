<?php

namespace App\Models\Admissions;

use App\Http\Requests\StudyMode\StudyMode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStudyModes extends Model
{
    use HasFactory;
    protected $table = "ac_userModes";
    protected $guarded = ['id'];
    protected $fillable = ['studyModeID','userID'];


    public function studymode()
    {
        return $this->belongsToMany(StudyMode::class,'ac_userModes','studyModeID','userID');
    }
    public function usermode()
    {
        return $this->belongsTo(User::class,'userID','id');
    }
}
