<?php
namespace App\Models\Audit;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Trail extends Model
{
    public $table = "audit_trail";
    protected $guarded = ['id'];

    public static function record($data) {

        // Description 
        Trail::create([
            'eventDescription'  => $data['eventDescription'],
            'module'            => $data['module'],
            'subModule'         => $data['subModule'],
            'actionInvolkedBy'  => $data['actionInvolkedBy'], 
            'affectedUser'      => $data['affectedUser'], 
            'programID'         => $data['programID'], 
        ]);

    }


    public static function examiationAudit($programID) {

        $rawTrails = Trail::where('programID',$programID)->orderBy('id','DESC')->get();

        foreach ($rawTrails as $trail) {
            $user = User::find($trail->affectedUser);
            $t = [
                'eventDescription'  => $trail->eventDescription,
                'module'            => $trail->module,
                'subModule'         => $trail->subModule,
                'actionInvolkedBy'  => Trail::returnUserDetails($trail->actionInvolkedBy),
                'affectedUser'      => Trail::returnUserDetails($trail->affectedUser),
                'student_id'        => $user->student_id,
                'date'              => $trail->created_at->toFormattedDateString(),
            ];

            $trails[] = $t;

        }

        if (empty($trails)) {
            $trails = [];
        }
        return $trails;

    }

    public static function examiationAuditUser($programID,$userID) {

        $rawTrails = Trail::where('programID',$programID)->where('affectedUser',$userID)->orderBy('id','DESC')->get();

        foreach ($rawTrails as $trail) {
            $user = User::find($trail->affectedUser);
            $t = [
                'eventDescription'  => $trail->eventDescription,
                'module'            => $trail->module,
                'subModule'         => $trail->subModule,
                'actionInvolkedBy'  => Trail::returnUserDetails($trail->actionInvolkedBy),
                'affectedUser'      => Trail::returnUserDetails($trail->affectedUser),
                'student_id'        => $user->student_id,
                'date'              => $trail->created_at->toFormattedDateString(),
            ];

            $trails[] = $t;

        }

        if (empty($trails)) {
            $trails = [];
        }
        return $trails;

    }

    public static function returnUserDetails($userID) {

        $user  = User::find($userID);
        return $user->first_name.' '.$user->middle_name.' '. $user->last_name;

    }

}
