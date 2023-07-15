<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fees extends Model
{
    protected $table= 'ac_fees';
    protected $fillable = ['name'];
    use HasFactory;

    public function PeriodFees()
    {
        return $this->hasMany(PeriodFees::class, 'feeID');
    }

}
