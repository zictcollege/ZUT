<?php

namespace App\Models\Academics;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prerequisite extends Model
{
    protected $table = 'ac_prerequisites';
    protected $fillable = ['courseID','prerequisiteID'];
    protected $primaryKey = 'id';
    use HasFactory;
    public function courses()
    {
        return $this->belongsTo(Courses::class, 'courseID');
    }

}
