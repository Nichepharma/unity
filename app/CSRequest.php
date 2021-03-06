<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CSRequest extends Model
{
     protected $table = 'csrequests';
     protected $guarded = [];

     public function messages(){
       return $this->HasMany('App\CSMessage', 'csrequest_id')->orderBy('id', 'desc');
     }

     public function company(){
       return $this->belongsTo('App\Company', 'company_id');
     }
}
