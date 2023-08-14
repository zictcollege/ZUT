<?php

namespace App\Models\Admissions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersNextOfKin extends Model
{
    protected $table = 'users_nextofkeen';
    protected $fillable = ['user_id','full_name', 'relationship', 'tel','city',
        'province','phone', 'country'];//removed email,address
    use HasFactory;

}
