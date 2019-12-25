<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favourites extends Model
{
      public function user()
    {
        return $this->belongsTo('App\User');
    }
      public function vendors()
    {
        return $this->belongsToMany('App\Vendor');
    }
}
