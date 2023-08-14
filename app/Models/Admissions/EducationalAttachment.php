<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EducationalAttachment extends Model
{
    protected $table 	= "users_educational_attachments";
    protected $fillable = [
    	'user_id','file',
    ];




    /*
		
		this function should help you view all atachements 
		that have been uploaded by the guest on educational qualification 
		submissions. 


    */
    public static function file($fileName) {
    	
    	//$visibility = Storage::getVisibility($fileName);
		//return Storage::setVisibility($fileName, 'public');

		$url = Storage::url($fileName);
		return $url;
    
    }



}
