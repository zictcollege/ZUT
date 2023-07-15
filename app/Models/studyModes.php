<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\studyModes
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|studyModes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|studyModes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|studyModes query()
 * @method static \Illuminate\Database\Eloquent\Builder|studyModes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|studyModes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|studyModes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|studyModes whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class studyModes extends Model
{
    protected $table = 'ac_studyModes';
    use HasFactory;
    protected $fillable = ['name', 'description'];

    public function academicPeriods()
    {
        return $this->hasMany(AcademicPeriods::class, 'studyModeIDAllowed');
    }
    public function periodFees()
    {
        return $this->hasMany(PeriodFees::class, 'studyModeID');
    }

}
