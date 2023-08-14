<?php

namespace App\Models\Admissions;

use Illuminate\Database\Eloquent\Model;

class UserSponser extends Model
{
    //
    protected $table 	= "users_nextofkeen";

    protected $fillable = [
    	'user_id','full_name','relationship','tel','phone','city','province','country',
    ]; 
}