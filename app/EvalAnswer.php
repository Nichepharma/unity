<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EvalAnswer extends Model
{
    /**
     * Get the phone record associated with the user.
     */
     protected $table = 'eval_ans';
     public $timestamps = false;

     public function question(){
       return $this->belongsTo('App\EvalQuestion', 'q_id');
     }
}
