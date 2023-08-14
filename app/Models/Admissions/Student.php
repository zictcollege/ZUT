<?php

namespace App\Models\Admissions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;


class Student extends Model
{
    protected $table = 'students';
    protected $guarded = ['id'];



    public function user() {
    	return $this->belongsTo(User::class);
    }

	public static function mystudentID($id){

	}


    //

}
