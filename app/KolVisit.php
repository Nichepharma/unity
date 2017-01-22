<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KolVisit extends Model
{
    /**
     * Get the phone record associated with the user.
     */
     protected $table = 'kol_visits';
     protected $fillable = array('company', 'user_id', 'customer_id', 'rep_id' , 'comment', 'time', 'date');
}
