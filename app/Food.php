<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    public function food()
    {
        return $this->belongsTo('App\User');
    }
}
