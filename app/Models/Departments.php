<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departments extends Model
{
    protected $table = 'ac_departments';
    use HasFactory;
    protected $fillable = ['name','description','cover'];

    public function programs()
    {
        return $this->hasMany(Programs::class, 'departmentID');
    }
}
