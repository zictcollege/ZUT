<?php

namespace App\Models\Academics;

use App\Models\Academic\Program;
use Illuminate\Database\Eloquent\Model;

class ProgramStudyMode extends Model
{
    protected $table = "ac_program_study_modes";
    protected $guarded = ['id'];


    public function program()
    {
        return $this->belongsTo(Programs::class, 'program_id', 'id');
    }


}
