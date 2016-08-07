<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * Get the phone record associated with the user.
     */
     protected $table = 'companies';
     public $timestamps = false;
}
