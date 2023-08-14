<?php

namespace App\Models\Admissions;

use App\Models\Academics\Programs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProgram extends Model
{
    protected $table = "ac_userPrograms";
    protected $guarded = ['id'];
    protected $fillable = ['programID','userID','activated_by','key'];

    public function program()
    {
        return $this->hasOne(Programs::class,'id','programID');
    }
}
