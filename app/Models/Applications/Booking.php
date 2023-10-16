<?php

namespace App\Models\Applications;

use App\Models\Accounting\Quotation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = ['id'];
    public function user()
    {
      return $this->belongsTo(User::class,'userID','id');
    }
    public function room()
    {
      return $this->belongsTo(Room::class,'roomID','id');
    }
    public function quotation()
    {
      return $this->hasOne(Quotation::class,'id','quotationID');
    }
    public static function validity($id){
      $booking = Booking::find($id);
      $validTill = $booking->created_at->addDays($booking->maxDuration);
      return $validTill;
    }
    public static function status($id)
    {
      $booking = Booking::find($id);
      $validTill    = Booking::validity($booking->id);


      $today        = strtotime(date('Y-m-d H:m:s')) * 1000;


      $bookingDate  = strtotime($booking->created_at) * 1000;
      $expireyDate  = strtotime($validTill) * 1000;


      if ($today > $expireyDate) {
        return "Expired";
      }else {
        return "Valid";
      }


    }


}
