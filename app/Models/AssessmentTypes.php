<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentTypes extends Model
{
    protected $table = 'ac_assessmentTypes';
    protected $fillable = ['name'];
    use HasFactory;
}
