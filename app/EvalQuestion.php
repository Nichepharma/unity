<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EvalQuestion extends Model
{
    /**
     * Get the phone record associated with the user.
     */
     protected $table = 'eval_qus';
     public $timestamps = false;

     public function cat(){
       return $this->belongsTo('App\EvalQuestionCat', 'cat_id');
     }
}
