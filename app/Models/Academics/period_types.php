<?php

namespace App\Models\Academics;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class period_types extends Model
{
    use HasFactory;
    protected $table = 'ac_periodTypes';
    protected $fillable = ['name', 'description'];

    public function academicPeriods()
    {
        return $this->hasMany(AcademicPeriods::class, 'type');
    }


}
