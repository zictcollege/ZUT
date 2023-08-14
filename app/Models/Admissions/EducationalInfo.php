<?php

namespace App\Models\Admissions;

use Illuminate\Database\Eloquent\Model;

class EducationalInfo extends Model
{
    protected $table 	= "users_education";
    
    protected $fillable = [
    	'user_id','high_school','primary_school','highest_qualification',
    ];
}
