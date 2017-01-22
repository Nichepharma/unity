<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EvalSession extends Model
{
    /**
     * Get the phone record associated with the user.
     */
     protected $table = 'eval_sessions';
     protected $fillable = array('company', 'supervisor_id', 'rep_id', 'time','date', 'supervisor_signature', 'rep_signature');
}
