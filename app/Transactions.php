<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
