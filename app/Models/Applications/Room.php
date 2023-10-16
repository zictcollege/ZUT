<?php

namespace App\Models\Applications;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Room extends Model
{
    protected $guarded = ['id'];

    public function bookings()
    {
      return $this->hasMany(Booking::class);
    }
    public function hostel()
    {
      return $this->belongsTo(Hostel::class,'hostelID','id');
    }
    public static function checkAvailability($roomID) {

      $room     = Room::find($roomID);
      $bookings = Booking::where('roomID',$roomID)->where('bookingStatus',1)->where('bookingElapsed',0)->get()->count();

      $freeSpaces = $room->maxBedspace - $bookings;

      return $freeSpaces;

    }

    public static function freeRooms() {
      $rooms = Room::all();
      $freeBedSpaces = 0;

      foreach ($rooms as $room) {
        # check how many spaces are left in the room
        $bookings = Booking::where('roomID',$room->id)->where('bookingStatus',1)->where('bookingElapsed',0)->get()->count();
        $freeSpaces = $room->maxBedspace - $bookings;
        $freeBedSpaces = $freeBedSpaces + $freeSpaces;

      }
      return $freeBedSpaces;
    }

    public static function freeRoomsByGender() {


      $rooms = Room::where('acceptableGender',Auth::user()->gender)->get();
      $freeBedSpaces = 0;

      foreach ($rooms as $room) {
        # check how many spaces are left in the room
        $bookings = Booking::where('roomID',$room->id)->where('bookingStatus',1)->where('bookingElapsed',0)->get()->count();
        $freeSpaces = $room->maxBedspace - $bookings;
        $freeBedSpaces = $freeBedSpaces + $freeSpaces;

      }
      return $freeBedSpaces;
    }

}
