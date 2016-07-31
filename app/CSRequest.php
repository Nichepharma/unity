<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CSRequest extends Model
{
     protected $table = 'csrequests';

     public function messages(){
       return $this->HasMany('App\CSMessage', 'csrequest_id')->orderBy('created_at');
     }
}
