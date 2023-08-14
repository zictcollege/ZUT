<?php

namespace App\Models\Admissions;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPersonalInformation extends Model
{
    protected $table = 'users_personal_information';
    protected $fillable = ['user_id','dob', 'marital_status', 'province_state', 'town_city',
        'telephone', 'mobile', 'nationality', 'street_main', 'post_code'];
    use HasFactory;

    public function userinfor()
    {
        return $this->hasOne(User::class,'user_id');
    }

}
