<?php

namespace App\Models\Academic;

use App\Local;
use Illuminate\Database\Eloquent\Model;

class ExemptionAttachment extends Model
{
  protected $table = "ac_exemption_attachments";
  protected $guarded = ['id'];

  public function users()
  {
    return $this->belongsTo(User::class, 'userID', 'id');
  }
  public static function data($id)
  {
    $upload = ExemptionAttachment::find($id);
    $url = Local::getURL($upload->url);
    return  [
      'id'       => $upload->id,
      'key'      => $upload->id,
      'userID'   => $upload->userID,
      'url'      => $url,
    ];
  }
}
