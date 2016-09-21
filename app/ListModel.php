<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListModel extends Model
{
     protected $table = 'lists';
     protected $guarded = [];

     public function company(){
       return $this->belongsTo('App\Company', 'company_id');
     }
}
