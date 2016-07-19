<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanVisit extends Model
{
    /**
     * Get the phone record associated with the user.
     */
     protected $table = 'plan_visit';
     public $timestamps = false;
}
