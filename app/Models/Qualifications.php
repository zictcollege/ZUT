<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualifications extends Model
{
    protected $table = 'ac_qualifications';
    protected $fillable = ['name','slug'];
    use HasFactory;

    public function programs(){
        return $this->hasMany(Programs::class, 'qualification_id');
    }
}
