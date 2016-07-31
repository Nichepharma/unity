<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CSMessage extends Model
{
     protected $table = 'csmessages';

     public function request(){
       return $this->belongsTo('App\CSRequest', 'csrequest_id');
     }
}
